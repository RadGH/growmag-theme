<?php
/*
Plugin Name: Leaving Site Popup
Version: 1.2.9
Description: Displays a newsletter promotional popup when a user attempts to leave the page.
Author: Radley Sustaire
Author URI: mailto:radleygh@gmail.com
*/

if( !defined( 'ABSPATH' ) ) exit;

define( 'LDleavingsite_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'LDleavingsite_PATH', dirname(__FILE__) );
define( 'LDleavingsite_VERSION', '1.2.9' );

function ld_leavingsite_init_plugin() {
	if ( !function_exists('acf') ) {
		add_action( 'admin_notices', 'ld_leavingsite_acf_error' );
		return;
	}
	
	include( LDleavingsite_PATH . '/includes/enqueue.php' );
	include( LDleavingsite_PATH . '/includes/options.php' );
	include( LDleavingsite_PATH . '/includes/popup.php' );

	// include( LDleavingsite_PATH . '/fields/settings.php' );
}
add_action( 'plugins_loaded', 'ld_leavingsite_init_plugin', 10 );

function ld_leavingsite_acf_error() {
	$plugin_data = get_plugin_data( LDleavingsite_PATH . '/limelight-leaving-site-popup.php' );

	?>
	<div class="error">
		<p><strong><?php echo $plugin_data['Name']; ?>: Error</strong></p>
		<p>The required plugin <strong>Advanced Custom Fields Pro</strong> is not running. Please install and activate ACF Pro.</p>
	</div>
	<?php
}