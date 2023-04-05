<?php
/*
Plugin Name: Limelight - Advertisements
Version: 1.4.2
Plugin URI: http://www.limelightdept.com/
Description: Allows you to define advertisement "locations" and independently create advertisements that can display in various locations.
Author: Radley Sustaire
Author URI: mailto:radleygh@gmail.com

Copyright 2015 Limelight Department (radley@limelightdept.com)
For use by Limelight Department and affiliates, do not distribute
*/

if( !defined( 'ABSPATH' ) ) exit;

define( 'LDAds_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'LDAds_PATH', dirname(__FILE__) );
define( 'LDAds_VERSION', '1.4.2' );

function ldad_init_plugin() {
	if ( !function_exists('acf') ) {
		add_action( 'admin_notices', 'ldad_acf_not_found' );
		return;
	}

	include( LDAds_PATH . '/includes/ad.php' );
	include( LDAds_PATH . '/includes/enqueue.php' );
	include( LDAds_PATH . '/includes/options.php' );
	include( LDAds_PATH . '/includes/post-type.php' );
	include( LDAds_PATH . '/includes/shortcode.php' );
	include( LDAds_PATH . '/includes/articles.php' );
	include( LDAds_PATH . '/widgets/ad-location.php' );
}
add_action( 'plugins_loaded', 'ldad_init_plugin', 10 );

function ldad_acf_not_found() {
	?>
	<div class="error">
		<p><strong>Limelight - Ad Automations: Error</strong></p>
		<p>The required plugin <strong>Advanced Custom Fields Pro</strong> is not running. Your ads will not appear until ACF Pro is running.</p>
	</div>
	<?php
}


function ld_ad_activate_plugin() {
	include( LDAds_PATH . '/includes/post-type.php' );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ld_ad_activate_plugin' );

function ld_ad_deactivate_plugin() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ld_ad_deactivate_plugin' );