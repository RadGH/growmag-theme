<?php
// ===========================
// Check that ACF is installed & active

function theme_alert_no_acf() {
	?>
	<div class="error">
		<p><strong>Theme Notice:</strong> Advanced Custom Fields is not available. Theme options will not be available. Please install Advanced Custom Fields.</p>
	</div>
	<?php
}

if( !function_exists('acf_add_options_page') ) {
	add_action( 'admin_notices', 'theme_alert_no_acf' );
	return;
}

// ===========================
// Register ACF Options Pages

$theme_settings = acf_add_options_page(array(
	'page_title' => 'Theme Options',
	'menu_title' => 'Theme Options',
	'menu_slug'  => 'theme-options-general',
	'icon_url'   => 'dashicons-desktop',
	'redirect'   => 'theme-options-branding',
	'capability' => 'manage_options',
));

acf_add_options_sub_page(array(
	'parent' => $theme_settings['menu_slug'],
	'page_title' => 'Branding',
	'menu_title' => 'Branding',
	'menu_slug' => 'theme-options-branding',
));

acf_add_options_sub_page(array(
	'parent' => $theme_settings['menu_slug'],
	'page_title' => 'Social Media',
	'menu_title' => 'Social Media',
	'menu_slug' => 'theme-options-social',
));

acf_add_options_sub_page(array(
	'parent' => $theme_settings['menu_slug'],
	'page_title' => 'Tracking',
	'menu_title' => 'Tracking',
	'menu_slug' => 'theme-options-tracking',
));

acf_add_options_sub_page(array(
	'parent' => $theme_settings['menu_slug'],
	'page_title' => 'Subscribe Popup',
	'menu_title' => 'Subscribe Popup',
	'menu_slug' => 'theme-options-subscribe-popup',
));

// add sub page
acf_add_options_sub_page(array(
	'page_title' 	=> 'Header Image',
	'menu_title' 	=> 'Header Image',
	'parent_slug' 	=> 'edit.php?post_type=tribe_events',
));

//include get_template_directory() . '/functions/theme-options/branding.php';
include get_template_directory() . '/functions/theme-options/social-media.php';
//include get_template_directory() . '/functions/theme-options/tracking.php';

// Per-page tracking code on page edit screen, see functions/defaults.php
//include get_template_directory() . '/functions/theme-options/tracking-pages.php';

// ===========================
// Add ACF Options Pages to the admin bar

function ld_acf_theme_settings( $links, $admin_bar ) {
	$links[] = array(
		'id'     => 'acf-theme-options',
		'title'  => 'Theme Options',
		'parent' => 'management',
		'href'   => admin_url( 'admin.php?page=theme-options-branding' ),
	);

	return $links;
}
add_filter( 'lm-admin-menu-links', 'ld_acf_theme_settings', 10, 2 );