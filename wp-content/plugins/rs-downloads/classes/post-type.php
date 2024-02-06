<?php

class RS_Downloads_Post_Type {
	
	public string $post_type = 'rs_download';
	
	public function __construct() {
		
		// Register the post type
		add_action( 'init', array( $this, 'register_post_type' ) );
		
		// Remove unwanted meta boxes
		add_action( 'add_meta_boxes', array( $this, 'remove_unwanted_meta_boxes' ), 100 );
		
		// Remove unwanted columns from the post list screen
		add_filter( 'manage_edit-rs_download_columns', array( $this, 'remove_unwanted_columns' ), 100 );
		
		// Download a file when visiting the singular page
		add_action( 'template_redirect', array( $this, 'download_file' ) );
		
		// Replace the single page template with one from this plugin
		add_filter( 'template_include', array( $this, 'replace_single_template' ), 100 );
		
		// Make the ACF select dropdown field named "Gravity Form" show a list of forms
		add_filter( 'acf/load_field/key=field_6552b6986ac70', array( $this, 'load_gravity_form_field_choices' ) );
		
		// Modify the ACF Message field "statistics" to show download statistics when viewing a download
		add_filter( 'acf/load_field/key=field_6552f6a6e447c', array( $this, 'modify_statistics_field' ) );
		
		// Add a column to the post list screen to show the total and monthly downloads
		add_filter( 'manage_rs_download_posts_columns', array( $this, 'add_download_count_column' ) );
		
		// Display values for the custom columns
		add_action( 'manage_rs_download_posts_custom_column', array( $this, 'display_download_count_column' ), 10, 2 );
		
	}
	
	
	/**
	 * Registers the download post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		
		$labels = array(
			'name' => 'Downloads',
			'singular_name' => 'Download',
			'menu_name' => 'Downloads',
			'name_admin_bar' => 'Download',
			'archives' => 'Item Archives',
			'attributes' => 'Item Attributes',
			'parent_item_colon' => 'Parent Item:',
			'all_items' => 'All Downloads',
			'add_new_item' => 'Add New Item',
			'add_new' => 'Add Download',
			'new_item' => 'New Item',
			'edit_item' => 'Edit Item',
			'update_item' => 'Update Item',
			'view_item' => 'View Item',
			'view_items' => 'View Items',
			'search_items' => 'Search Item',
			'not_found' => 'Not found',
			'not_found_in_trash' => 'Not found in Trash',
			'featured_image' => 'Featured Image',
			'set_featured_image' => 'Set featured image',
			'remove_featured_image' => 'Remove featured image',
			'use_featured_image' => 'Use as featured image',
			'insert_into_item' => /** @lang text */ 'Insert into item',
			'uploaded_to_this_item' => 'Uploaded to this item',
			'items_list' => 'Items list',
			'items_list_navigation' => 'Items list navigation',
			'filter_items_list' => 'Filter items list',
		);
		
		$args = array(
			'label' => 'Download',
			'labels' => $labels,
			'supports' => array( 'title', 'author', 'revisions', 'editor', 'thumbnail' ),
			'hierarchical' => false,
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 20,
			'menu_icon' => 'dashicons-download',
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => false,
			'can_export' => true,
			'has_archive' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'rewrite' => array(
				'slug' => 'download',
				'with_front' => false,
			),
			
			// Enable gutenberg
			'show_in_rest' => true,
		);
		
		register_post_type( 'rs_download', $args );
		
	}
	
	/**
	 * Remove unwanted meta boxes
	 *
	 * @return void
	 */
	function remove_unwanted_meta_boxes() {
		remove_meta_box( 'wpseo_meta', 'rs_download', 'normal' );             // Yoast
		remove_meta_box( 'yoast_internal_linking', 'rs_download', 'side' );   // Yoast internal linking
		remove_meta_box( 'gos_simple_redirect', 'rs_download', 'side' );      // Simple Redirects
		remove_meta_box( 'A2A_SHARE_SAVE_meta', 'rs_download', 'advanced' );  // AddToAny Share Buttons
		remove_meta_box( 'at_widget', 'rs_download', 'advanced' );            // AddThis Tools
	}
	
	/**
	 * Remove unwanted columns from the post list screen
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	function remove_unwanted_columns( $columns ) {
		$remove_columns = array(
			'mr_et_managers', // Elegant Themes
			'wpseo-title',    // Yoast
			'wpseo-metadesc', // Yoast
			'wpseo-focuskw',  // Yoast
			'wpseo-links',    // Yoast internal linking
			'wpseo-linked',   // Yoast internal linking
			
		);
		
		foreach( $remove_columns as $key ) {
			if ( isset($columns[ $key ]) ) {
				unset($columns[ $key ]);
			}
		}
		
		return $columns;
	}
	
	/**
	 * Check if a download post is valid by ensuring it returns the correct post type.
	 *
	 * @param WP_Post|int $post_id
	 *
	 * @return bool
	 */
	function is_valid( $post_id ) {
		return ( get_post_type($post_id) === 'rs_download' );
	}
	
	/**
	 * @return void
	 */
	function download_file() {
		if ( ! is_singular('rs_download') ) return;
		
		$download_id = get_the_ID();
		$download = new RS_Downloads_Item( $download_id );
		
		if ( ! $download ) {
			$this->process_error_message( $download, 'invalid_post' );
		}
		
		// Check if a valid download nonce was provided
		if ( $download && $this->allow_download( $download ) ) {
			$this->process_download( $download );
		}else{
			$this->process_error_message( $download, 'no_access' );
		}
		
		exit;
	}
	
	/**
	 * @param RS_Downloads_Item $download
	 *
	 * @return bool
	 */
	function allow_download( $download ) {
		if ( ! $this->is_valid( $download->get_id() ) ) return false;
		
		// Allow download by default
		$can_download = true;
		
		// Allow filters to modify download permission
		/** @see RS_Downloads_Gravity_Forms::validate_download_key() */
		$can_download = apply_filters( 'rs_downloads/can_download', $can_download, $download->get_id(), $download );
		
		return (bool) $can_download;
	}
	
	/**
	 * Download the target file. Make sure to check $this->allow_download() first.
	 *
	 * @param RS_Downloads_Item $download
	 *
	 * @return void
	 */
	function process_download( $download ) {
		do_action( 'rs_downloads/process_download', $download->get_id(), $download );
		
		$download->process_download();
	}
	
	/**
	 * Redirects to the download page with an error message, or shows an error message using wp_die if no page is defined
	 *
	 * @param RS_Downloads_Item $download
	 * @param string $error_code
	 *
	 * @return void
	 */
	function process_error_message( $download = null, $error_code = 'unknown_error' ) {
		$post_id = get_field( 'download_page', 'rs_downloads' );
		
		/**
		 * To see where error messages are displayed, see:
		 * @see RS_Downloads_Post_Type::get_error_message()
		 * @see RS_Downloads_Post_Type::display_messages()
		 */
		
		if ( $post_id ) {
			
			// Prepare URL for the download page including a GET parameter for the error code and download ID
			$url = get_permalink($post_id);
			
			// Remove certain query args from the URL
			$url = remove_query_arg( array('rsd_entry', 'rsd_key'), $url );
			
			// Add the error message and download ID to the URL
			$args = array();
			$args['rsd_key'] = 'expired';
			$args['rsd_error'] = urlencode($error_code);
			if ( $download ) $args['rsd_id'] = urlencode($download->get_id());
			$url = add_query_arg($args, $url);
			
			$url .= '#rsd-messages';
			wp_redirect( $url );
			
		}else{
			
			// If no download page specified in the settings, show a generic error message
			$message = 'Sorry, this download is not available.';
			$message.= "\n\n" . 'Error code: ' . esc_html($error_code);
			if ( $download ) $message .= "\n" . 'Download ID: ' . esc_html($download->get_id());
			
			$args = array(
				'response' => 403,
				'link_text' => '&larr; Back to ' . get_bloginfo('name'),
				'link_url' => site_url('/'),
			);
			wp_die(wpautop($message), 'Download Error', $args);
			
		}
		
		exit;
	}
	
	function replace_single_template( $template ) {
		if ( is_singular('rs_download') ) {
			$template = RSD_PATH . '/templates/single-download.php';
		}
		
		return $template;
	}
	
	/**
	 * Make the ACF select dropdown field named "Gravity Form" show a list of forms
	 *
	 * @param array $field
	 *
	 * @return mixed
	 */
	function load_gravity_form_field_choices( $field ) {
		// Ignore the edit field group screen
		if ( acf_is_screen( 'acf-field-group' ) ) return $field;
		
		$field['choices'] = array();
		
		$forms = GFAPI::get_forms();
		if ( ! $forms ) return $field;
		
		foreach( $forms as $form ) {
			$field['choices'][ $form['id'] ] = $form['title'] . ' [#'. $form['id'] .']';
		}
		
		return $field;
	}
	
	/**
	 * Modify the ACF Message field "statistics" to show download statistics when viewing a download
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	public function modify_statistics_field( $field ) {
		// Ignore the "Edit field group" screen
		if ( acf_is_screen( 'acf-field-group' ) ) return $field;
		
		// Get the download ID (post ID)
		$download_id = get_the_ID();
		if ( ! RS_Downloads()->Post_Type->is_valid( $download_id ) ) return $field;
		
		$field['message'] = RS_Downloads()->Gravity_Forms->get_statistics_html( $download_id );
		
		return $field;
	}
	
	public function add_download_count_column( $columns ) {
		
		// Insert the columns 2nd from last
		$columns = array_slice( $columns, 0, -2, true ) +
			array( 'rs_total_downloads' => 'Total Downloads' ) +
			array( 'rs_monthly_downloads' => 'Monthly Downloads' ) +
			array_slice( $columns, -2, 2, true );
		
		return $columns;
	}
	
	public function display_download_count_column( $column, $post_id ) {
		if ( $column === 'rs_total_downloads' ) {
			$args = array();
			$count = RS_Downloads()->Gravity_Forms->count_downloads( $post_id, $args );
			echo $count;
		}
		
		if ( $column === 'rs_monthly_downloads' ) {
			$args = array( 'start_date' => date( 'Y-m-d', strtotime( '-30 days' ) ) );
			$count = RS_Downloads()->Gravity_Forms->count_downloads( $post_id, $args );
			echo $count;
		}
	}
	
	/**
	 * Displays an error message based on the error code $_GET['rsd_error']
	 *
	 * Example: ?rsd_error=no_access&rsd_id=9959
	 *
	 * @return void
	 */
	function display_messages() {
		$messages = array();
		
		// Check for an error message from URL parameter "rsd_error"
		if ( isset($_GET['rsd_error']) ) {
			$code = stripslashes($_GET['rsd_error']);
			$download_id = intval($_GET['rsd_id']);
			$messages[] = array(
				'type' => 'error',
				'message' => $this->get_error_message( $code, $download_id )
			);
		}
		
		// Check if an entry was submitted from URL parameter "rsd_submitted"
		if ( isset($_GET['rsd_submitted']) ) {
			$m = get_field( 'confirmation_message', 'rs_downloads' );
			if ( $m ) $messages[] = array(
				'type' => 'message',
				'message' => $m
			);
		}
		
		if ( $messages ) {
			?>
			<div id="rsd-messages" class="rsd-messages">
				<?php
				foreach( $messages as $m ) {
					?>
					<div class="rsd-message type-<?php echo esc_attr($m['type']); ?>">
						<?php echo wpautop($m['message']); ?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
	}
	
	/**
	 * Get an error message from an error code
	 *
	 * @param string    $code
	 * @param int|null  $download_id
	 *
	 * @return string|false
	 */
	function get_error_message( $code, $download_id = null ) {
		if ( ! $code ) return false;
		
		switch( $code ) {
			case 'download_expired':
				return 'The download link you provided has expired, please try again.';
				
			case 'no_access':
				return 'Failed to start download, please try again.';
				
			case 'invalid_post':
				return 'That download is no longer available, it may have been moved or deleted.';
				
			case 'unknown_error':
				return 'An unknown error was thrown.';
				
			default:
				return false;
		}
	}
	
	/**
	 * Get a WP_Query that contains all downloads
	 *
	 * @param array $custom_args Optional array with additional args
	 *
	 * @return WP_Query
	 */
	function get_all( $custom_args = array() ) {
		$args = array(
			'post_type' => 'rs_download',
			'posts_per_page' => -1,
			'nopaging' => true,
			'orderby' => 'title',
			'order' => 'ASC',
		);
		
		$args = array_merge( $args, $custom_args );
		
		$query = new WP_Query( $args );
		
		return $query;
	}
	
}

return new RS_Downloads_Post_Type();