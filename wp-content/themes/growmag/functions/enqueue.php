<?php

/*
* Get the path to a theme file
*/
function ld_enqueue_path( $file ) {
	return untrailingslashit( get_template_directory() ) . $file;
}

/*
* Get the URL to a theme file
*/
function ld_enqueue_url( $file ) {
	return untrailingslashit( get_template_directory_uri() ) . $file;
}

/*
* Get the last modification time of a theme file
*/
function ld_mtime( $file ) {
	return current_time( 'Y-m-d', filemtime( ld_enqueue_path( $file ) ) );
}

/*
* Enqueue a script by relative theme filename
*/
function ld_enqueue_script( $filename, $deps = array(), $version = null, $in_footer = false ) {
	if ( !file_exists( ld_enqueue_path($filename) ) ) {
		echo "<!-- Invalid enqueue script: " . esc_html($filename) . " -->\n";
		return;
	}

	if ( $version === null ) $version = ld_mtime( $filename );

	$key = 'ld-' . pathinfo($filename, PATHINFO_FILENAME);
	$url = ld_enqueue_url( add_query_arg(array('em' => $version), $filename ));
	wp_enqueue_script( $key, $url, $deps, $version, $in_footer );
}

/*
* Enqueue a style by relative theme filename
*/
function ld_enqueue_style( $filename, $deps = array(), $version = null, $media_types = 'all' ) {
	if ( !file_exists( ld_enqueue_path($filename) ) ) {
		echo "<!-- Invalid enqueue style: " . esc_html($filename) . " -->\n";
		return;
	}

	if ( $version === null ) $version = ld_mtime( $filename );

	$key = 'ld-' . pathinfo($filename, PATHINFO_FILENAME);
	
	$url = ld_enqueue_url( add_query_arg(array('em' => $version), $filename ));
	wp_enqueue_style( $key, $url, $deps, $version, $media_types );
}


// ---------------------


// Global includes (Front end, backend, login)
function ld_enqueue_global_scripts() {
	wp_enqueue_script( 'jquery' );

	ld_enqueue_style( '/includes/css/global.css' );

	// Use open sans from Google Fonts instead of WordPress.
	if ( is_user_logged_in() ) {
		wp_dequeue_style('open-sans');
		wp_deregister_style('open-sans');
		wp_enqueue_style( 'open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic', array() );
	}
}
add_action( 'wp_enqueue_scripts', 'ld_enqueue_global_scripts', 25 );
add_action( 'admin_enqueue_scripts', 'ld_enqueue_global_scripts', 25 );
add_action( 'login_enqueue_scripts', 'ld_enqueue_global_scripts', 25 );



// Front-end includes
function ld_enqueue_theme_scripts() {
	// Third party files
	ld_enqueue_script( '/includes/libraries/modernizr/modernizr.min.js', array(), '3.0.0c' );
	ld_enqueue_script( '/includes/libraries/prefixfree/prefixfree.min.js', array(), '1.0.3' );
	ld_enqueue_style(  '/includes/libraries/animate-css/animate.min.css', array(), '1.0.0' );
	//ld_enqueue_style(  '/includes/libraries/slick/slick.css', array(), '1.0.0' );
	//ld_enqueue_style(  '/includes/libraries/slick/slick-theme.css', array(), '1.0.0' );
	//ld_enqueue_script( '/includes/libraries/slick/slick.min.js', array(), '1.0.0' );

	// Font files
	ld_enqueue_style( '/includes/fonts/BlissLight.css', '1.0.0' );
	ld_enqueue_style( '/includes/fonts/BookmanOldStyleStd.css', '1.0.0' );
	ld_enqueue_style( '/includes/fonts/Cataneo.css', '1.0.0' );
	//ld_enqueue_style( '/includes/fonts/GillSansMTProMedium.css', '1.0.0' );
	ld_enqueue_style( '/includes/fonts/MyriadProSemibold.css', '1.0.0' );
	ld_enqueue_style( '/includes/fonts/HelveticaBlack.css', '1.0.0' );
	
	// fonts
	wp_enqueue_style( 'theme-fonts', 'https://use.typekit.net/mnm5gkb.css', array() );

	// Theme files
	ld_enqueue_script( '/includes/js/main.js', array( 'jquery' ) );
	ld_enqueue_style(  '/includes/libraries/normalize/normalize.min.css', array(), '3.0.2' );
	ld_enqueue_style(  '/includes/css/style.css' );
	ld_enqueue_style(  '/includes/css/grow-custom.css', array(), '2024-11-19' );

	ld_enqueue_style(  '/includes/css/print.css', array(), null, 'print' );

	if ( get_theme_support( 'woocommerce' ) ) {
//		ld_enqueue_style(  '/includes/css/woocommerce-general.css' );
//		ld_enqueue_style(  '/includes/css/woocommerce-custom.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'ld_enqueue_theme_scripts', 30 );



// Admin includes
function ld_enqueue_admin_scripts() {
	ld_enqueue_script( '/includes/admin/admin.js', array( 'jquery' ) );
	ld_enqueue_style(  '/includes/admin/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'ld_enqueue_admin_scripts', 30 );



// Login includes
// function ld_enqueue_login_scripts() {
// }
// add_action( 'login_enqueue_scripts', 'ld_enqueue_login_scripts', 30 );