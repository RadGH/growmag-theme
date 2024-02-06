<?php

add_action( 'acf/init', function() {
	acf_add_options_page( array(
		'page_title' => 'RS Downloads – Settings',
		'menu_slug' => 'rs-downloads-settings',
		'parent_slug' => 'edit.php?post_type=rs_download',
		'menu_title' => 'Settings',
		'position' => '',
		'redirect' => false,
		'updated_message' => 'Settings Updated',
		'post_id' => 'rs_downloads',
		'autoload' => true,
	) );
} );

