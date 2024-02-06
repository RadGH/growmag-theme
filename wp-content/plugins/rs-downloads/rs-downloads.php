<?php
/*
Plugin Name: RS Downloads
Description: Adds a downloads post type which can integrate with Gravity Forms for generating leads and tracking download counts.
Version: 1.2.0
Author: Radley Sustaire
Author URI: https://radleysustaire.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

define( 'RSD_VERSION', '1.2.0' );
define( 'RSD_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'RSD_PATH', dirname(__FILE__) );

class RS_Downloads_Plugin {
	
	public RS_Downloads_Enqueue          $Enqueue;
	public RS_Downloads_Blocks           $Blocks;
	public RS_Downloads_Post_Type        $Post_Type;
	public RS_Downloads_Tracking         $Tracking;
	public RS_Downloads_Gravity_Forms    $Gravity_Forms;
	
	public function __construct() {
		
		// Load the rest of the plugin when other plugins have finished loading.
		add_action( 'plugins_loaded', array( $this, 'load_plugin' ) );
		
		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
		
	}
	
	public function load_plugin() {
		
		// Classes
		// - For usage outside of this plugin: RS_Downloads()->Enqueue->something();
		$this->Enqueue = include( RSD_PATH . '/classes/enqueue.php' );
		$this->Blocks = include( RSD_PATH . '/classes/blocks.php' );
		$this->Post_Type = include( RSD_PATH . '/classes/post-type.php' );
		$this->Tracking = include( RSD_PATH . '/classes/tracking.php' );
		$this->Gravity_Forms = include( RSD_PATH . '/classes/gravity-forms.php' );
		
		// Instances (Classes that can be used multiple times)
		include( RSD_PATH . '/instances/download-item.php' );
		
		// Shortcodes
		include( RSD_PATH . '/shortcodes/rs_download_list.php' );
		
		// ACF fields and settings pages
		include( RSD_PATH . '/acf/fields.php' );
		include( RSD_PATH . '/acf/options.php' );
		
	}
	
	public function activate_plugin() {
		
		// Register the custom post type
		$this->Post_Type = include( RSD_PATH . '/classes/post-type.php' );
		
		// Register the post type immediately
		$this->Post_Type->register_post_type();
		
		// Flush permalinks
		flush_rewrite_rules();
		
	}
	
	public static function deactivate_plugin() {
		
		// Flush permalinks
		flush_rewrite_rules();
		
	}
	
}

/**
 * Get the plugin object, creating it the first time.
 * 
 * @return RS_Downloads_Plugin
 */
function RS_Downloads() {
	static $instance = null;
	if ( $instance === null ) $instance = new RS_Downloads_Plugin();
	return $instance;
}

// Initialize the plugin
RS_Downloads();