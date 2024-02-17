# Lemon Squeezy - Better Endpoints

Enhances the [Lemon Squeezy WordPress plugin](https://wordpress.org/plugins/lemon-squeezy/) by adding a `validate_license_key`
endpoint, facilitating license key validation
directly through custom REST API endpoints. This extension aims to provide a seamless integration for license management
within the WordPress ecosystem, leveraging the Lemon Squeezy API for secure and efficient validation processes.

## Minimum Requirements

- **PHP:** 7.4 or higher

## Installation

As this extension is not a standalone plugin but an enhancement for the Lemon Squeezy plugin, it should be installed on
a website which already has the Lemon Squeezy WordPress plugin active.

To include this extension in your project, follow the steps below:

1. Ensure that the Lemon Squeezy plugin is installed and activated on your WordPress site.
2. Download or clone this extension into your project directory.

## Usage

Once installed, the extension automatically registers a new REST API endpoint /lsq/v1/validate_license_key/. This endpoint accepts POST requests with a `license_key` and `instance_id` as parameters to validate the license key against the Lemon Squeezy API.

## Contributions

Contributions to this library are highly appreciated. Raise issues on GitHub or submit pull requests for bug
fixes or new features. Share feedback and suggestions for improvements.

## License: GPLv2 or later

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.