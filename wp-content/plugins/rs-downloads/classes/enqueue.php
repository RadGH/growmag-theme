<?php

class RS_Downloads_Enqueue {
	
	private $public_assets_enqueued = false;
	
	public function __construct() {

		// Register block styles on the front-end and editor. This does not actually enqueue the asset (the block and shortcode handle that)
		add_action( 'wp_enqueue_scripts', array( $this, 'register_block_styles' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_block_styles' ) );
		
		// Register CSS and JS on the back-end / dashboard
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		
		// Register block editor scripts on the editor page
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_scripts' ) );
		
	}
	
	/**
	 * Used to include the CSS and JS on the front-end.
	 *
	 * Called manually by the shortcode, or automatically by the block
	 * @see shortcode_rs_download_list()
	 * 
	 * @return void
	 */
	public function enqueue_public_scripts() {
		if ( $this->public_assets_enqueued ) return;
		else $this->public_assets_enqueued = true;
		
		// Front-end styles and scripts
		wp_enqueue_style( 'rs-downloads', RSD_URL . '/assets/public.css', array(), RSD_VERSION );
		wp_enqueue_script( 'rs-downloads', RSD_URL . '/assets/public.js', array('jquery'), RSD_VERSION );
		
		// Include data for public.js
		$data = array(
			// 'ajax_url' => admin_url( 'admin-ajax.php' ),
			'remove_query_args' => array( 'rsd_id', 'rsd_entry', 'rsd_key' ),
			'form_settings' => RS_Downloads()->Gravity_Forms->get_js_form_settings(),
		);
		
		wp_localize_script( 'rs-downloads', 'rs_downloads_settings', $data);
		
		// Ensuring block styles are registered
		$this->register_block_styles();
		
		// Enqueue the block styles. This is automatic for the gutenberg block, but not the shortcode
		wp_enqueue_style( 'rs-downloads-block-styles' );
		
	}
	
	/**
	 * Registers (but does not enqueue) block styles for the download list block
	 * 
	 * @return void
	 */
	public function register_block_styles() {
		wp_register_style( 'rs-downloads-block-styles', RSD_URL . '/assets/block-styles.css', array( 'rs-downloads' ), RSD_VERSION );
	}
	
	/**
	 * Register CSS and JS on the back-end / dashboard
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_style( 'rs-downloads-admin', RSD_URL . '/assets/admin.css', array(), RSD_VERSION );
		wp_enqueue_script( 'rs-downloads-admin', RSD_URL . '/assets/admin.js', array(), RSD_VERSION );
	}
	
	/**
	 * Register block editor scripts on the editor page
	 *
	 * @return void
	 */
	public function enqueue_block_editor_scripts() {
		wp_register_script( 'rs-downloads-block-editor', RSD_URL . '/assets/block-editor.js', array('wp-element', 'wp-hooks'), RSD_VERSION );
	}
	
}

return new RS_Downloads_Enqueue();