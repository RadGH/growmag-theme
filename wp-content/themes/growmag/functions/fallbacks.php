<?php

if(is_admin()) {
	return;
}

// get_field, normally provided by Advanced Custom Fields
if ( !function_exists('get_field') ) {
function get_field( $name, $post_id = null, $format = null ) {
	if ( $post_id === null ) $post_id = get_the_ID();

	if ( substr(0, 6, strtolower($post_id)) == 'option' ) {
		return get_option( "options_" . $name );
	}else{
		return get_post_meta( $post_id, $name, true );
	}
}
}

// update_field, normally provided by Advanced Custom Fields
if ( !function_exists('update_field') ) {
function update_field( $name, $value, $post_id = null, $format = null ) {
	if ( $post_id === null ) $post_id = get_the_ID();

	if ( substr(0, 6, strtolower($post_id)) == 'option' ) {
		update_option( "options_" . $name, $value );
	}else{
		update_post_meta( $post_id, $name, $value );
	}

	return true;
}
}

// is_woocommerce
if ( !function_exists('is_woocommerce') ) {
	function is_woocommerce() {
		return false;
	}
}

// ld_is_woocommerce_page
if ( !function_exists('ld_is_woocommerce_page') ) {
	function ld_is_woocommerce_page() {
		if ( !is_singular('page') ) return false;
		
		$post_id = get_the_ID();
		
		if ( $post_id == get_option('woocommerce_cart_page_id') ) return true;
		if ( $post_id == get_option('woocommerce_checkout_page_id') ) return true;
		
		return false;
	}
}