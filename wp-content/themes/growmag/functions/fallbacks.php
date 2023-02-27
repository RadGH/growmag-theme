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