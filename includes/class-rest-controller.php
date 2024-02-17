<?php
/**
 * Rest Controller Class
 *
 * @package     ArrayPress/LemonSqueezy
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\LemonSqueezy\Better_Endpoints;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function add_action;
use function get_option;
use function is_wp_error;
use function register_rest_route;
use function wp_remote_post;
use function wp_remote_retrieve_response_code;
use function wp_remote_retrieve_response_message;

/**
 * Handles REST API requests for the Lemon Squeezy plugin, facilitating license validation through custom endpoints.
 *
 * This class is responsible for registering a custom REST API endpoint within the WordPress environment to validate
 * license keys for the Lemon Squeezy plugin. It ensures secure, efficient handling of license validation requests by
 * interfacing with the Lemon Squeezy API, providing a mechanism for authenticating and validating license keys and
 * instance IDs submitted by users or systems.
 */
class Rest_Controller {

	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Initializes the REST Controller by registering custom routes for the API endpoints.
	 */

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Ensures a single instance of the Rest_Controller class is created (Singleton Pattern).
	 *
	 * @return object Instance of the Rest_Controller class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers the custom routes for the REST API endpoints.
	 *
	 * This method defines the route(s) and their respective callback(s) for license key validation, including required
	 * arguments and permission checks to ensure proper API request handling.
	 */
	public function register_routes() {
		$namespace = 'lsq/v1';

		register_rest_route(
			$namespace,
			'/validate_license_key/',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'validate_key' ),
				'args'                => array(
					'license_key' => [
						'description'       => 'License key.',
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
					'instance_id' => [
						'description'       => 'Instance ID of the existing activation.',
						'type'              => 'string',
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					],
				),
				'permission_callback' => function ( WP_REST_Request $request ) {
					return true;
				},
			)
		);

	}

	/**
	 * Validates a given license key and instance ID through the Lemon Squeezy API.
	 *
	 * Processes the REST API request by extracting the license key and instance ID, validating them against the Lemon
	 * Squeezy API, and returning the validation results.
	 *
	 * @param WP_REST_Request $request Full data about the request including license key and instance ID.
	 *
	 * @return WP_REST_Response|WP_Error Returns a WP_REST_Response on success or WP_Error on failure.
	 */
	public function validate_key( WP_REST_Request $request ) {
		$license_key   = $request->get_param( 'license_key' );
		$instance_id   = $request->get_param( 'instance_id' );
		$is_valid      = false;
		$error_message = '';
		$api_key       = get_option( 'lsq_api_key' );

		if ( empty( $api_key ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'error'   => __( 'Unauthorized request', 'lemon-squeezy-better-endpoints' ),
				),
				401
			);
		}

		// Prepare the query arguments, making 'instance_id' conditional
		$query_args = [ 'license_key' => $license_key ];
		if ( ! empty( $instance_id ) ) {
			$query_args['instance_id'] = trim( $instance_id );
		}

		$validation_url = add_query_arg( $query_args, LSQ_API_URL . '/v1/licenses/validate' );

		$response = wp_remote_post(
			$validation_url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'Accept'        => 'application/vnd.api+json',
					'Content-Type'  => 'application/vnd.api+json',
					'Cache-Control' => 'no-cache',
				),
			)
		);

		$body = json_decode( $response['body'], true );

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				$is_valid = true;
			} else {
				$error_message = isset( $body['error'] ) ? $body['error'] : wp_remote_retrieve_response_message( $response );
			}
		} else {
			$error_message = $response->get_error_message();
		}

		return new WP_REST_Response(
			array(
				'success' => $is_valid,
				'error'   => $error_message,
				'data'    => $body,
			),
			$is_valid ? 200 : 400
		);
	}

}
