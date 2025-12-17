<?php
/**
 * Plugin Name: Floaty Button
 * Plugin URI:  https://github.com/vizuh/floaty-button
 * Description: Floaty button
 * Version:     1.0.0
 * Author:      Vizuh
 * Author URI:  https://vizuh.com
 * Text Domain: floaty-button
 * Requires at least: 6.4
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package FloatyButton
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VZFLTY_VERSION', '1.0.0' );
define( 'VZFLTY_OPTION_KEY', 'vzflty_options' );
define( 'VZFLTY_PLUGIN_FILE', __FILE__ );

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/class-vzflty-plugin.php';
require_once __DIR__ . '/includes/admin/class-vzflty-admin.php';
require_once __DIR__ . '/includes/frontend/class-vzflty-frontend.php';

/**
 * Initialize the plugin.
 */
function vzflty_init() {
	$plugin = new VZFLTY_Plugin();
	$plugin->init();
}

add_action( 'plugins_loaded', 'vzflty_init' );

register_activation_hook( __FILE__, array( 'VZFLTY_Plugin', 'activate' ) );
