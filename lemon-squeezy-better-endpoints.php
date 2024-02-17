<?php
/**
 * Plugin Name:       Lemon Squeezy - Better Endpoints
 * Plugin URI:        https://github.com/arraypress/lemon-squeezy-better-endpoints
 * Description:       Adds support for license key validation through the Lemon Squeezy REST API endpoint.
 * Author:            ArrayPress
 * Author URI:        https://arraypress.com
 * License:           GNU General Public License v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lemon-squeezy-better-endpoints
 * Domain Path:       /languages/
 * Requires PHP:      7.4
 * Requires at least: 6.4.3
 * Version:           1.0.0
 */

namespace ArrayPress\LemonSqueezy\Better_Endpoints;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use function plugin_dir_path;
use function load_plugin_textdomain;
use function add_action;

/**
 * Initializes the custom REST endpoints and loads the necessary components for the Lemon Squeezy plugin.
 *
 * This function checks for the existence of the Lemon Squeezy specific class to ensure that the Lemon Squeezy plugin is active.
 * If the plugin is active, it proceeds to set up the plugin's main path, load translations for better localization support,
 * include the custom endpoint class file, and finally, instantiate the custom endpoint class to register the custom REST endpoints.
 *
 * It is hooked into the 'plugins_loaded' action to make sure it runs after all plugins are loaded, ensuring that the class existence
 * check is accurate and that translations are loaded at the right time.
 *
 * @return void This function does not return any value.
 */
function lemon_squeezy_better_endpoints() {

	// Check if a class from Lemon Squeezy exists
	if ( ! class_exists( 'lemonsqueezy\LSQ_Rest_Controller' ) ) {
		return;
	}

	// Setup the main file
	$plugin_path = plugin_dir_path( __FILE__ );

	// Load translations
	load_plugin_textdomain( 'lemon-squeezy-better-endpoints', false, $plugin_path . 'languages/' );

	// Include the custom endpoint class file
	require_once $plugin_path . 'includes/class-rest-controller.php';

	// Instantiate your custom endpoint class
	Rest_Controller::get_instance();

}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\lemon_squeezy_better_endpoints', 20 );