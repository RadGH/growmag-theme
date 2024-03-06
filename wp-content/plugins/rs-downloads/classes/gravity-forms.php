<?php

class RS_Downloads_Gravity_Forms {
	
	/******************
	 * Public methods *
	 ******************/
	
	public function __construct() {
		
		// Check if Gravity Forms is loaded
		if ( ! class_exists( 'GFForms' ) ) return;
		
		// Prepare the form to be displayed on the page, if it is enabled.
		// It will be added as a hidden popup to every page by default.
		// @todo: Only add the form to pages that actually use the download list block or shortcode?
		add_action( 'init', array( $this, 'prepare_form' ) );
		
		// Add custom fields to the form that handles the download request
		add_filter( 'gform_get_form_filter', array( $this, 'add_fields_to_form' ), 10, 2 );
		
		// When a new entry is made, check if it was for a download. Apply a download statistic if so.
		// add_action( 'gform_after_submission', array( $this, 'process_download_entry' ), 10, 2 );
		
		// Filter the confirmation to use a custom redirection URL back to the current page
		add_filter( 'gform_confirmation', array( $this, 'filter_confirmation' ), 10, 4 );
		
		// Check if a visitor can start a download
		add_filter( 'rs_downloads/can_download', array( $this, 'validate_download_key' ), 10, 3 );
		
		// Make the download fields available to export
		add_filter( 'gform_export_fields', array( $this, 'add_export_field' ) );
		add_filter( 'gform_export_field_value', array( $this, 'add_export_value' ), 10, 4 );
		
		// Make the download fields visible when viewing an entry
		add_filter( 'gform_entry_detail_meta_boxes', array( $this, 'add_entry_meta_box' ), 10, 3 );
		
		// List the download of each entry as a column when viewing the list of entries
		add_filter( 'gform_entry_list_columns', array( $this, 'add_entry_list_column' ), 10, 2 );
		
		// Display the value in the column
		add_filter( 'gform_entries_field_value', array( $this, 'display_entry_list_column_value' ), 10, 4 );
		
		// Add a conditional logic field to export screen and entry list screen
		add_filter( 'gform_field_filters', array( $this, 'add_conditional_logic_field' ), 10, 2 );
		
	}
	
	/**
	 * Get the display value for the download column
	 *
	 * @param $download_id
	 * @param $none_label
	 *
	 * @return mixed|string
	 */
	public function format_download_preview_value( $download_id, $none_label = '' ) {
		if ( $download_id ) {
			if ( RS_Downloads()->Post_Type->is_valid( $download_id ) ) {
				$url = get_edit_post_link( $download_id );
				$title = get_the_title( $download_id );
				return '<a href="'. esc_url($url) .'" target="_blank">'. esc_html($title) .'</a>';
			}else{
				return '<em>Invalid download #'. $download_id .'</em>';
			}
		}else{
			return $none_label;
		}
	}
	
	// -----
	// Hooks
	// -----
	
	/**
	 * Prepares to display the form in the footer, and enqueues required assets
	 * 
	 * @return void
	 */
	function prepare_form() {
		if ( ! $this->is_enabled() ) return;
		
		$form_id = $this->get_form_id();
		if ( ! $form_id ) return;
		
		// Enqueue download list assets
		RS_Downloads()->Enqueue->enqueue_public_scripts();
		
		// Register the form to be displayed in the footer
		add_action( 'wp_footer', array( $this, 'display_form_popup_html' ) );
	}
	
	/**
	 * Displays the form html at the end of the page, hidden until a download is opened
	 *
	 * @hooked wp_footer
	 *
	 * @return void
	 */
	function display_form_popup_html() {
		
		// Variables to be passed to the template
		$form_id = $this->get_form_id();
		
		// Load the template
		include( RSD_PATH . '/templates/form-popup.php' );
		
	}
	
	/**
	 * Get the form HTML from a form ID, with hidden fields that process a download event upon submission
	 *
	 * @param $form_id
	 *
	 * @return string
	 */
	public function get_form_html( $form_id ) {
		// Create the shortcode
		$shortcode = '[gravityform id="' . $form_id . '" title="false" description="false" ajax="true"]';
		
		// Display the form HTML, using output buffering to ensure the entire form and any output is included
		ob_start();
		echo do_shortcode( $shortcode );
		$html = ob_get_clean();
		
		return $html;
	}
	
	/**
	 * Add custom fields to the form that handles the download request
	 * 
	 * @param $form_string
	 * @param $form
	 *
	 * @return array|mixed|string|string[]
	 */
	public function add_fields_to_form( $form_string, $form ) {
		// Do not modify other forms
		if ( ! $form['id'] === $this->get_form_id() ) return $form_string;
		
		// Add a field to indicate that this form is used for downloads
		$inputs = '<input type="hidden" name="rs_downloads_form_id" value="'. intval($form['id']) .'" />';
		$inputs.= '<input type="hidden" name="rs_downloads_post_id" value="'. intval(get_the_ID()) .'" />';
		$inputs.= '<input type="hidden" name="rs_downloads_download_id" value="" />';
		
		$form_string = str_replace( '</form>', $inputs . '</form>', $form_string );
		
		return $form_string;
	}
	
	/**
	 * When a new submission is made, check if it was for a download. Apply a download statistic if so.
	 *
	 * @param array $entry The Entry object.
	 * @param array $form The Form object.
	 *
	 * @return array
	 */
	/*
	public function process_download_entry( $entry, $form ) {
		// Check if this form is used for downloads
		if ( ! $this->is_download_form( $form['id'] ) ) return $entry;
		
		// Get value from custom fields added to the form html
		$form_id = (int) rgpost( 'rs_downloads_form_id' );
		$post_id = (int) rgpost( 'rs_downloads_post_id' );
		$entry_id = (int) $entry['id'];
		
		return $entry;
	}
	*/
	
	/**
	 * Filter the confirmation to use a custom redirection URL back to the current page
	 *
	 * @param array $confirmation
	 * @param array $form
	 * @param array $entry
	 * @param bool $ajax
	 *
	 * @return array
	 */
	public function filter_confirmation( $confirmation, $form, $entry, $ajax ) {
		// Check if this form is used for downloads
		if ( ! $this->is_download_form( $form['id'] ) ) return $confirmation;
		
		// Get value from custom fields added to the form html
		$form_id = (int) rgpost( 'rs_downloads_form_id' );
		$post_id = (int) rgpost( 'rs_downloads_post_id' );
		$download_id = (int) rgpost( 'rs_downloads_download_id' );
		$entry_id = (int) $entry['id'];
		
		// Generate a unique identifier, used to validate future download requests
		$download_key = md5( $form_id . $entry_id . microtime() );
		
		// Store that this entry is the original entry that was submitted for this series
		gform_update_meta( $entry_id, 'rsd_original_entry_id', $entry_id );
		
		// Store the code in the entry
		gform_update_meta( $entry_id, 'rsd_download_key', $download_key );
		
		// Store the download ID and title in the entry
		gform_update_meta( $entry_id, 'rsd_download_id', $download_id ?: '' );
		gform_update_meta( $entry_id, 'rsd_download_title', $download_id ? get_the_title($download_id) : '' );
		
		// Redirect to the downloads page, falling back to the current page
		$download_page_id = get_field( 'download_page', 'rs_downloads' );
		
		$url = get_permalink( $download_page_id ?: $post_id );
		
		// Add custom query args to the URL
		$url = add_query_arg(array(
			'rsd_submitted' => 1,
			'rsd_entry' => $entry_id,
			'rsd_key' => $download_key,
		), $url);
		
		// Redirect to the current page
		return array( 'redirect' => $url );
	}
	
	/**
	 * Check if a visitor can start a download
	 *
	 * @param bool $is_valid
	 * @param int $download_id
	 * @param RS_Downloads_Item $download_item
	 *
	 * @return false|mixed|void
	 */
	public function validate_download_key( $is_valid, $download_id, $download_item ) {
		if ( ! $this->is_enabled() ) return $is_valid;
		
		$rsd_key = isset($_GET['rsd_key']) ? (string) stripslashes($_GET['rsd_key']) : false;
		$rsd_entry = isset($_GET['rsd_entry']) ? (string) stripslashes($_GET['rsd_entry']) : false;
		if ( ! $rsd_key || ! $rsd_entry ) return false;
		
		// Get the entry from the database
		$entry = GFAPI::get_entry( $rsd_entry );
		if ( ! $entry || is_wp_error($entry) ) return false;
		
		// Get the download key from the entry
		$download_key = (string) gform_get_meta( $entry['id'], 'rsd_download_key' );
		if ( ! $download_key ) return false;
		
		// If the download key is incorrect, do not allow the download
		if ( $download_key !== $rsd_key ) return false;
		
		// Check if the entry has "expired"
		if ( $this->is_entry_expiration_enabled() ) {
			
			// Entry date uses UTC time
			$entry_time = strtotime( $entry['date_created'] );
			$time_now = time();
			
			$diff_in_seconds = $time_now - $entry_time;
			$expiration_in_seconds = 60 * $this->get_entry_expiration_minutes();
			
			// Check if the download has expired
			if ( $expiration_in_seconds < $diff_in_seconds ) {
				// Throw an error that the download has expired
				RS_Downloads()->Post_Type->process_error_message( $download_item, 'download_expired' );
				return false;
			}
			
		}
		
		// Get the download ID that was used for the entry that generated the key
		$entry_download_id = rgar( $entry, 'rsd_download_id' );
		
		// Check if the download is different than the original, and create a new entry if so.
		if ( $download_id != $entry_download_id ) {
			$this->maybe_duplicate_entry( $entry, $download_id );
		}
		
		// The download is valid
		return true;
	}
	
	
	/**
	 * Make the download fields available to export
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function add_export_field( $form ) {
		$form['fields'][] = array(
			'label' => 'Download ID',
			'id' => 'rsd_download_id',
		);
		
		$form['fields'][] = array(
			'label' => 'Download Title',
			'id' => 'rsd_download_title',
		);
		
		// Consider adding:
		// rsd_download_key
		// rsd_original_entry_id
		
		return $form;
	}
	
	/**
	 * Add values to the exportable fields
	 *
	 * @param string|array $value
	 * @param int          $form_id
	 * @param string       $field_id
	 * @param array        $entry
	 *
	 * @return string|array
	 */
	public function add_export_value( $value, $form_id, $field_id, $entry ) {
		switch( $field_id ) {
			
			case 'rsd_download_id':
			case 'rsd_download_title':
				$value = gform_get_meta( $entry['id'], $field_id );
				break;
			
		}
		
		return $value;
	}
	
	/**
	 * Make the download fields visible when viewing an entry
	 *
	 * @param $meta_boxes
	 * @param $entry
	 * @param $form
	 *
	 * @return mixed
	 */
	public function add_entry_meta_box( $meta_boxes, $entry, $form ) {
		if ( $this->get_form_id() === $form['id'] ) {
			
			$meta_boxes['rsd_download_id'] = array(
				'title'      => 'Download',
				'callback'   => array( $this, 'display_entry_meta_box' ),
				'context'    => 'side', // location of the meta box
				'entry'      => $entry
			);
			
		}
		
		return $meta_boxes;
	}
	
	/**
	 * Displays the download details in the meta box
	 *
	 * @param $args
	 * @param $metabox
	 *
	 * @return void
	 */
	function display_entry_meta_box( $args, $metabox ) {
		$entry = $args['entry'];
		$download_id = gform_get_meta( $entry['id'], 'rsd_download_id' );
		
		if ( $download_id && RS_Downloads()->Post_Type->is_valid( $download_id ) ) {
			echo $this->format_download_preview_value( $download_id );
			echo '<br>';
			echo 'Post ID: '. esc_html($download_id);
		}else{
			echo '<em>None</em>';
		}
	}
	
	/**
	 * List the download of each entry as a column when viewing the list of entries
	 *
	 * @param $columns
	 * @param $form_id
	 *
	 * @return mixed
	 */
	public function add_entry_list_column( $columns, $form_id ) {
		if ( $this->get_form_id() !== $form_id ) return $columns;
		
		$columns['rsd_download'] = 'Download';
		
		return $columns;
	}
	
	/**
	 * Display the download value in the column
	 *
	 * @param $value
	 * @param $form_id
	 * @param $field_id
	 * @param $entry
	 *
	 * @return mixed|string
	 */
	public function display_entry_list_column_value( $value, $form_id, $field_id, $entry ) {
		if ( $this->get_form_id() !== $form_id ) return $value;
		if ( $field_id !== 'rsd_download' ) return $value;
		
		$download_id = gform_get_meta( $entry['id'], 'rsd_download_id' );
		
		return $this->format_download_preview_value( $download_id );
	}
	
	
	/**
	 * Add a conditional logic field to export screen (and other areas)
	 *
	 * @param array $field_filters The form field, entry properties, and entry meta filter settings.
	 * @param array $form          The form object the filter settings have been prepared for.
	 *
	 * @return array
	 */
	public function add_conditional_logic_field( $field_filters, $form ) {
		
		// Get all download posts to create a list of dropdown values
		$downloads = RS_Downloads()->Post_Type->get_all();
		
		// Create a list compatible with Gravity Forms
		$list = array();
		
		// Add an option for "No download"
		$list[] = array(
			'text' => '(None)',
			'value' => '',
		);
		
		if ( $downloads->have_posts() ) foreach( $downloads->posts as $p ) {
			$list[] = array(
				'text' => get_the_title( $p->ID ),
				'value' => $p->ID,
			);
		}
		
		$field_filters[] = array(
			'key'             => 'rsd_download_id',
			'preventMultiple' => false,
			'text'            => esc_html__( 'Download', 'rs-downloads' ),
			'operators'       => array( 'is', 'isnot', 'contains' ),
			'values'          => $list,
		);
		
		return $field_filters;
	}
	
	/**
	 * Check if the entry is for a different download and creates a new entry if so.
	 * The new entry is a duplicate of the original, but the download ID/title is changed.
	 * 
	 * @param array $original_entry
	 * @param int $new_download_id
	 *
	 * @return array|false
	 */
	public function maybe_duplicate_entry( $original_entry, $new_download_id ) {
		
		// Search for an entry that used the same original entry but with the new download ID
		$search_criteria = array(
			'status' => 'active',
			'field_filters' => array(
				array(
					'key' => 'rsd_original_entry_id',
					'value' => $original_entry['id']
				),
				array(
					'key' => 'rsd_download_id',
					'value' => $new_download_id
				),
			),
		);
		
		$entries = GFAPI::get_entries( $this->get_form_id(), $search_criteria );
		
		// If an entry was found, return it, and do not create a new entry
		if ( $entries && is_array($entries) && count($entries) > 0 ) {
			return $entries[0];
		}
		
		// Entry was not found. Create a new one using the previous fields.
		$new_entry = $original_entry;

		// Remove some fields to let them re-populate
		if ( isset($new_entry['id']) ) unset( $new_entry['id'] );
		if ( isset($new_entry['ip']) ) unset( $new_entry['ip'] );
		if ( isset($new_entry['user_agent']) ) unset( $new_entry['user_agent'] );
		if ( isset($new_entry['status']) ) unset( $new_entry['status'] );
		if ( isset($new_entry['date_created']) ) unset( $new_entry['date_created'] );
		if ( isset($new_entry['date_updated']) ) unset( $new_entry['date_updated'] );
		if ( isset($new_entry['source_url']) ) unset( $new_entry['source_url'] );
		if ( isset($new_entry['created_by']) ) unset( $new_entry['created_by'] );
		
		// Set the "repeat_entry" hidden field value to 1.
		$new_entry[10] = 1;
		
		// Create the new entry
		$new_entry_id = GFAPI::add_entry( $new_entry );
		
		if ( $new_entry_id && is_numeric($new_entry_id) ) {
			
			// Update the new entry with the new download information
			gform_update_meta( $new_entry_id, 'rsd_download_key', '' );
			gform_update_meta( $new_entry_id, 'rsd_download_id', $new_download_id );
			gform_update_meta( $new_entry_id, 'rsd_download_title', get_the_title( $new_download_id ) );
			
			// Store the original entry ID
			gform_update_meta( $new_entry_id, 'rsd_original_entry_id', $original_entry['id'] );
			
			return $new_entry;
		}else{
			return false;
		}
	}
	
	// -----------------
	// Utility Functions
	// -----------------
	
	/**
	 * Check if a form is used for downloads
	 *
	 * @param $form_id
	 *
	 * @return bool
	 */
	public function is_download_form( $form_id ) {
		// Check if this form is used for downloads
		if ( ! $this->is_enabled() ) return false;
		
		// Check if this form is used for downloads
		if ( $form_id != $this->get_form_id() ) return false;
		
		return true;
	}
	
	/**
	 * Returns true if the Gravity Forms integration is enabled (and a valid form ID is provided)
	 * @return bool
	 */
	public function is_enabled() { return $this->get_setting( 'enabled' ); }
	
	/**
	 * Returns the form ID to be shown before starting a download.
	 * @return int|false
	 */
	public function get_form_id() { return $this->get_setting( 'form_id' ); }
	
	/**
	 * Returns true if the "entry cookie" is enabled.
	 * This clientside feature remembers your entry, allowing you to download multiple files without filling out the form again.
	 * @return bool
	 */
	public function is_entry_expiration_enabled() { return $this->get_setting( 'entry_expiration_enabled' ); }
	
	
	/**
	 * Get the number of minutes specified for the entry to expire.
	 * @return int
	 */
	public function get_entry_expiration_minutes() { return $this->get_setting( 'entry_expiration_minutes' ); }
	
	/*******************
	 * Private methods *
	 *******************/
	
	private $initialized_settings = false;
	
	/**
	 * @var array $settings {
	 *
	 *    @type bool $enabled                  Whether Gravity Forms integration is enabled
	 *    @type int|false $form_id             The form ID to use for download links
	 *    @type bool $entry_expiration_enabled Whether to use a cookie to remember your entry
	 *    @type int $entry_expiration_minutes  The number of minutes until the entry expires
	 *
	 */
	private $settings = array(
		'enabled' => false,
		
		'form_id' => 0,
		
		'entry_expiration_enabled' => false,
		'entry_expiration_minutes' => 60,
	);
	
	/**
	 * Load and validate plugin settings once per page load.
	 * These settings have autoload enabled through the ACF options page.
	 *
	 * @return void
	 */
	private function initialize_settings() {
		if ( $this->initialized_settings ) {
			return; // already loaded
		}else{
			$this->initialized_settings = true;
		}
		
		$this->settings['enabled'] = (bool) get_option( 'rs_downloads_enable_gravity_forms', '0' );
		
		$this->settings['form_id'] = ((int) get_option( 'rs_downloads_form_id', '0' )) ?: false;
		
		$this->settings['entry_expiration_enabled'] = (bool) get_option( 'rs_downloads_entry_expiration_enabled', '1' );
		$this->settings['entry_expiration_minutes'] = (int) get_option( 'rs_downloads_entry_expiration_minutes', '7' );
		
		// Form ID is required for the integration to work
		if ( ! $this->settings['form_id'] ) {
			$this->settings['enabled'] = false;
			return;
		}
		
		// Cookie defaults to 60 minutes, although it must still be enabled to use this value.
		if ( $this->settings['entry_expiration_minutes'] < 1 ) {
			$this->settings['entry_expiration_minutes'] = 60;
		}
	}
	
	/**
	 * Get a setting, initializing settings if they haven't already been initialized.
	 *
	 * For use within this class only. Try the public methods, for example: RS_Downloads()->Gravity_Forms->get_form_id()
	 *
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	private function get_setting( $key ) {
		if ( ! $this->initialized_settings ) {
			$this->initialize_settings();
		}
		
		if ( isset($this->settings[ $key ]) ) {
			return $this->settings[ $key ];
		}
		
		return null;
	}
	
	/**
	 * Get all gravity form settings.
	 *
	 * For properties returned, see:
	 * @see RS_Downloads_Gravity_Forms::$settings
	 *
	 * @return array
	 */
	public function get_all_settings() {
		if ( ! $this->initialized_settings ) {
			$this->initialize_settings();
		}
		
		return $this->settings;
	}
	
	/**
	 * Get only the settings that are used in public.js
	 *
	 * @return array
	 */
	public function get_js_form_settings() {
		$form_settings = array(
			'enabled' => $this->is_enabled() ? 1 : 0
		);
		
		return $form_settings;
	}
	
	/**
	 * Count the number of downloads based on the number of entries submitted to the lead generation form
	 *
	 * @param int $download_id
	 *
	 * @return array[]
	 */
	public function get_download_counts( $download_id ) {
		
		$results = array(
			'total' => array(
				'title' => 'All Time',
				'count' => $this->count_downloads( $download_id ),
			),
			'30-days' => array(
				'title' => 'Last 30 Days',
				'count' => $this->count_downloads( $download_id, array(
					'start_date' => date( 'Y-m-d', strtotime( '-30 days' ) ),
				)),
			),
		);
		
		return $results;
	}
	
	/**
	 * Get html table to use in the statistics field for a download
	 *
	 * @param int $download_id
	 *
	 * @return string
	 */
	public function get_statistics_html( $download_id ) {
		
		// Get the download statistics from Gravity Forms
		$stats = RS_Downloads()->Gravity_Forms->get_download_counts( $download_id );
		
		// Prepare the message
		$message = '<table><tbody>';
		
		foreach( $stats as $s ) {
			$n = sprintf( _n( '%d download', '%d downloads', $s['count'], 'rs-downloads' ), $s['count'] );
			$message.= '<tr><th style="text-align: left; padding-right: 5px;">'. esc_html($s['title']) .':</th><td>'. esc_html($n) . '</td></tr>';
		}
		
		$message.= '</tbody></table>';
		
		$message .= '<br>';
		
		$form_id = $this->get_form_id();
		
		$entries_list_url = admin_url('admin.php?page=gf_entries&view=entries&id='. $form_id .'&filter');
		
		// Add download as a filter
		$entries_list_url = add_query_arg(array(
			'field_id' => 'rsd_download_id',
			'operator' => 'is',
			's' => $download_id,
		), $entries_list_url);
		
		$message .= '<a href="'. esc_attr( $entries_list_url ) .'" target="_blank">View all entries</a>';
		
		return $message;
	}
	
	/**
	 * Count the number of entries for a download. Custom args can be provided to filter by date, etc.
	 *
	 * @param int $download_id
	 * @param array $args
	 *
	 * @return int
	 */
	public function count_downloads( $download_id, $args = array() ) {
		$form_id = $this->get_form_id();
		
		$search_args = array(
			'field_filters' => array(
				array(
					'key' => 'rsd_download_id',
					'value' => $download_id,
				),
			),
		);
		
		if ( $args ) $search_args = array_merge( $search_args, $args );
		
		return GFAPI::count_entries( $form_id, $search_args );
	}
	
	
}

return new RS_Downloads_Gravity_Forms();