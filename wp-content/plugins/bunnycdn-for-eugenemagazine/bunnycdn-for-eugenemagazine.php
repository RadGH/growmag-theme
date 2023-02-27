<?php
/*
Plugin Name: BunnyCDN for Grow Magazine
Description: Rewrite all upload URLs to bunny CDN.
Version: 1.2.1
*/

ob_start( 'bcdn_em_hook_end' );

function bcdn_em_hook_end( $content ) {
	if ( is_admin() ) return $content;
	if ( wp_doing_ajax() ) return $content;
	if ( wp_is_json_request() ) return $content;
	if ( ! is_plugin_active( 'bunnycdn/bunnycdn.php' ) ) return $content;
	
	$src = '//growmag.com/wp-content/';
	$cdn = '//growmag.b-cdn.net/wp-content/';
	
	// REMOVE cdn if ?nocdn
	if ( isset($_GET['nocdn']) ) {
		return str_replace( $cdn, $src, $content );
	}else{
		return str_replace( $src, $cdn, $content );
	}
}