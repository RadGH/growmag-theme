<?php

class RS_Downloads_Item {
	
	private int $post_id;
	private WP_Post $post;
	
	function __construct( $post_id ) {
		$this->post_id = $post_id;
		$this->post = get_post( $post_id );
		
		// ACF fields:
		// type = string, "file" or "url"
		// file = int, attachment id
		// force_download = bool, whether to force a download instead of viewing in the browser (files only)
		// url = string, custom url
		// button_text = string, button text (whether file or url)
		
	}
	
	/**
	 * Checks if this download item is valid
	 *
	 * @return bool
	 */
	function is_valid() {
		return RS_Downloads()->Post_Type->is_valid( $this->post_id );
	}
	
	/**
	 * Sends the file to the browser to be downloaded, or redirects to a URL if that was selected
	 *
	 * @return void
	 */
	function process_download() {
		$type = $this->get_type();
		
		if ( $type === 'file' ) {
			$file_id = get_field( 'file', $this->post_id );
			$force_download = $this->is_force_download();
			$this->download_file( $file_id, $force_download );
		}else{
			$url = get_field( 'url', $this->post_id );
			$this->redirect_to_url( $url );
		}
	}
	
	private function download_file( $file_id, $force_download = false ) {
		if ( ! $file_id ) {
			wp_die('Sorry, that file is not currently available.<br><br><em>The attachment ID #'. $file_id . ' could not be found.</em>' );
			exit;
		}
		
		// Stream the file to the page as a download
		$file_path = get_attached_file( $file_id );
		$mime_type = get_post_mime_type( $file_id );
		
		header( 'Content-Type: ' . $mime_type );
		
		if ( $force_download ) {
		header( 'Content-Disposition: attachment; filename="' . basename( $file_path ) . '"' );
		}
		
		header( 'Content-Length: ' . filesize( $file_path ) );
		
		readfile( $file_path );
		exit;
	}
	
	private function redirect_to_url( $url ) {
		// Redirect to the target url
		if ( ! $url ) {
			wp_die('Error: No file or url specified for download #'. $this->post_id .'.');
		}
		
		wp_redirect( $url );
		exit;
	}
	
	// Getters
	function get_id() {
		return $this->post_id;
	}
	
	function get_title() {
		return get_the_title( $this->post_id );
	}
	
	function get_content() {
		return apply_filters( 'the_content', $this->post->post_content );
	}
	
	function get_url() {
		return get_permalink( $this->post_id );
	}
	
	function get_type() {
		return get_field( 'type', $this->post_id ) ?: 'file';
	}
	
	function is_force_download() {
		if ( $this->get_type() != 'file' ) {
			return false; // Only files can be force-downloaded
		}else{
			return (bool) get_field( 'force_download', $this->post_id );
		}
	}
	
	function get_button() {
		$url = $this->get_url();
		$button_text = get_field( 'button_text', $this->post_id ) ?: 'Download';
		
		// <span class="indicator" aria-hidden="true"></span>
		return sprintf(
			'<a href="%s" class="button rs-download-link" data-download-id="%d" target="_blank">%s</a>',
			esc_attr( $url ),
			esc_attr( $this->post_id ),
			esc_html($button_text)
		);
	}
	
	function get_featured_image_id() {
		return get_post_thumbnail_id( $this->post_id );
	}
	
	function get_featured_image() {
		return wp_get_attachment_image( $this->get_featured_image_id(), 'full' );
	}
	
	function get_statistics() {
		$form_id = RS_Downloads()->Gravity_Forms->get_form_id();
	}
	
}