<?php
/**
 * Plugin Name: Interagents Booking Calendar
 * Description: Free bilingual workflow-call bookings for Interagents.ai.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Interagents.ai
 * Text Domain: interagents-booking-calendar
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IABC_VERSION', '1.0.0' );
define( 'IABC_PLUGIN_FILE', __FILE__ );
define( 'IABC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IABC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once IABC_PLUGIN_DIR . 'includes/class-iabc-plugin.php';

register_activation_hook( __FILE__, array( 'IABC_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'IABC_Plugin', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		IABC_Plugin::instance()->init();
	}
);
