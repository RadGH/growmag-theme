<?php

// SHOP REMOVAL:
// 1. Hide the subscribe button
function gm_shop_disabled_css() {
	?>
	<style>
		.menu-button-subscribe {
			display: none;
		}
	</style>
	<?php
}
add_action( 'wp_print_scripts', 'gm_shop_disabled_css', 100 );

// 2. Remove shop links from nav menus
/*
function gm_shop_disabled_nav_menu( $html, $args ) {
	$html = preg_replace( '/<li .*?class=".*?menu-item-.*?href=".*?\/shop\/.*?<\/li>/', '', $html );
	
	return $html;
}
add_filter( 'wp_nav_menu_items', 'gm_shop_disabled_nav_menu', 10, 2 );
*/

/* ===========================

Table of Contents:

  1. Theme Function Files
  2. Plugin Functions
  3. Menus & Sidebars
  4. Widgets
  5. Shortcodes

=========================== */


// ===========================
// 1. Theme Function Files

// include_once 'includes/admin/editor-styles.php'; // Custom admin editor styles
include_once 'functions/install.php';            // Functions triggered when the theme is first activated
include_once 'functions/enqueue.php';            // Includes various CSS/JS files used throughout the theme
include_once 'functions/utility.php';            // A variety of custom functions to use within the theme
include_once 'functions/defaults.php';           // Customize Wordpress default settings, such as the "From" address in emails
include_once 'functions/admin.php';              // Customizations to the admin section, admin bar, dashboard, etc
include_once 'functions/login.php';              // Use custom logo, blog url, etc. for the login screen
include_once 'functions/menus.php';              // Set up our menus and hook into menu displaying functionality
include_once 'functions/sharing.php';            // Allows us to create various sharing links for a page.
include_once 'functions/rss.php';                // Improves RSS feeds, adds featured images and image size
include_once 'functions/theme-options.php';      // ACF Theme options pages
include_once 'functions/template-tags.php';      // Functions that are utilized within the theme's template files
include_once 'functions/pagination.php';         // Features for pagination

include_once 'functions/visual-composer-fallback.php'; // Fix missing visual composer shortcodes


/*
 * Brand specific functions
 */
include_once 'functions/growmag.php';  // Renames categories to departments
// include_once 'functions/growmag-weekender.php';  // Renames categories to departments


// Hide editor on homepage
add_action( 'admin_init', 'hide_editor' );
function hide_editor() {
	if ( isset( $_GET['post'] ) && $_GET['post'] == 97 ) {
		remove_post_type_support( 'page', 'editor' );
	}
}


// ===========================
// 2. Plugin Functions

global $seo_ultimate;
if ( isset( $seo_ultimate ) ) {
	include_once 'plugin-addons/seo-ultimate.php';
}

if ( class_exists( 'WooCommerce' ) ) {
	include_once 'plugin-addons/woocommerce.php';
	// include_once 'plugin-addons/woocommerce-cart-data.php';
}

if ( class_exists( 'acf' ) ) {
	include_once 'plugin-addons/advanced-custom-fields.php';
}

if ( defined( 'LDAds_URL' ) ) {
	include_once 'plugin-addons/limelight-ads.php';
}

if ( defined( 'LMNEWS_URL' ) ) {
	include_once 'plugin-addons/limelight-newsletter.php';
}

// Fallback functions, in case a plugin isn't loaded
include_once 'functions/fallbacks.php';

// ===========================
// 3. Menus & Sidebars

function define_menus() {
	$menus = array(
		'categories' => 'Categories',
		'pages'      => 'Other Pages',
		
		//'mobile_departments' => 'Menu - Departments',
		//'mobile_pages'       => 'Menu - Pages',
		//'footer_departments' => 'Footer - Departments',
		//'footer_pages'       => 'Footer - Pages',
	);

	$sidebars = array(
		'sidebar'  => array(
			'Sidebar',
			'Used when a more specific sidebar is not in use.',
		),
		'blog'     => array(
			'Articles',
			'Appears on single article pages.',
		),
		/*
		'footer_center'     => array(
			'Footer (Center)',
			'Appears on the footer of every page in the center column.',
		),
		*/
		'footer_right'     => array(
			'Footer (Right)',
			'Appears on the footer of every page in the right column.',
		),
	);

	// Register the menus & sidebars
	register_nav_menus( $menus );

	foreach ( $sidebars as $key => $bar ) {
		register_sidebar( array(
			'id'          => $key,
			'name'        => $bar[0],
			'description' => $bar[1],

			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}

add_action( 'after_setup_theme', 'define_menus' );

// ===========================
// 4. Widgets
include_once 'widgets/textButtonWidget.php';
include_once 'widgets/socialMediaButtons.php';
include_once 'widgets/facebook-page.php';
include_once 'widgets/twitter-feed.php';
// include_once 'widgets/instagram-feed.php'; // replaced with smash balloon instagram widget

// ===========================
// 5. Shortcodes
function theme_register_shortcodes() {
	include_once 'shortcodes/copyright.php'; // copy, reg, tm, year
	include_once 'shortcodes/button.php'; // Allows you to easily render buttons onto pages.
	include_once 'shortcodes/ll_accordian.php'; //Accordian shortcode.  Does not provide animation or styling, only sets classes.
	include_once 'shortcodes/ll_columns.php';  //Create multiple columns on standard content pages.
}

add_action( 'init', 'theme_register_shortcodes' );