<?php
/*
Plugin Name: RS Digital Issues
Description: Display digital issues
Author: Radley Sustaire
Author URI: https://radleysustaire.com/
Version: 1.2.0
*/

defined( 'ABSPATH' ) || exit;

define('DI_URL', untrailingslashit( plugin_dir_url(__FILE__) ));
define('DI_PATH', untrailingslashit( plugin_dir_path(__FILE__) ));

add_action( 'plugins_loaded', 'di_init_plugin' );

function di_init_plugin() {
	// acf fields
	include( __DIR__ . '/acf-fields/fields.php' );
	
	// includes
	include( __DIR__ . '/includes/general.php' );
	include( __DIR__ . '/includes/enqueue.php' );
	include( __DIR__ . '/includes/post-type.php' );
	include( __DIR__ . '/includes/mailchimp-hooks.php' );
	include( __DIR__ . '/includes/mailchimp-api.php' );
}
