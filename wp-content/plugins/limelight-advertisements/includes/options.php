<?php
if ( function_exists( 'acf_add_options_sub_page' ) ) {
	
	acf_add_options_sub_page( array(
		'parent'     => 'edit.php?post_type=ld_ad',
		'page_title' => 'Settings',
		'menu_title' => 'Settings',
		'menu_slug'  => 'ld-ad-settings',
	) );
	
	//include( LDAds_PATH . '/fields/ad-locations.php' );
	include( LDAds_PATH . '/fields/ad-settings.php' );
	include( LDAds_PATH . '/fields/settings-articles.php' );
	include( LDAds_PATH . '/fields/settings-debugging.php' );
}




/*
function ldad_default_terms($field) {
	// by default, select all categories
	$field['default_value'] = get_terms(array(
		'taxonomy' 		=> 'category',
		'fields' 		=> 'ids', 
		'hide_empty' 	=> false
	));;
	return $field;
}
add_filter('acf/load_field/key=field_5723c4c8600a4', 'ldad_default_terms');
*/