<?php

function di_enqueue_assets() {
	if ( !is_digital_issues_page() ) return;
	
	$url = DI_URL;
	$path = DI_PATH;
	
	$v = filemtime("$path/assets/digital-issues.css");
	
	wp_enqueue_style( 'digital-issues', "{$url}/assets/digital-issues.css?em={$v}", array(), null );
	// wp_enqueue_script( 'digital-issues', "{$url}/assets/digital-issues.js?em={$v}", array( 'jquery' ), null, true );
}
add_action( 'wp_enqueue_scripts', 'di_enqueue_assets' );