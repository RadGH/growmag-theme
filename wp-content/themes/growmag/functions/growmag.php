<?php

// Remove sorting dropdown from store
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Custom ad ajax url
// init array used for ads rendered later
function custom_ad_print_scripts() {
	?>
	<script type="text/javascript">
		var ld_ads_markup = [];
		var ad_ajax_url = '/ad_ajax.php';
	</script>
	<?php
}
remove_action( 'wp_print_scripts', 'ld_ads_print_scripts' );
add_action( 'wp_print_scripts', 'custom_ad_print_scripts' );

// Change woocommerce breadcrumb Home link to Shop home
function woo_custom_breadrumb_home_url() {
	return get_permalink( woocommerce_get_page_id( 'shop' ) );
}

add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );


// Change woocommerce breadcrumb Home text to Shop
function jk_change_breadcrumb_home_text( $defaults ) {
	$defaults['home'] = 'Shop';

	return $defaults;
}

add_filter( 'woocommerce_breadcrumb_defaults', 'jk_change_breadcrumb_home_text' );


function add_isotope() {
	if ( function_exists( "is_woocommerce" ) && is_woocommerce() ) {
		ld_enqueue_script( '/includes/libraries/isotope/isotope.pkgd.min.js', array( 'jquery' ), "3.0", true );
	}
}

add_action( 'wp_enqueue_scripts', 'add_isotope', 30 );


function add_woocommerce_category_filters() {
	$product_categories = get_terms( 'product_cat', array( "hide_empty" => 1 ) );
	if ( !$product_categories ) return;
	
	$active_obj = get_queried_object();
	$active_term_id = false;
	
	if ( $active_obj instanceof WP_Term ) foreach( $product_categories as $term ) {
		if ( $term->term_id == $active_obj->term_id ) $active_term_id = $active_obj->term_id;
	}
	
	echo '<ul id="woocommerce-category-filter">';
	echo '<li><a href="' . get_permalink( woocommerce_get_page_id( 'shop' ) ) . '" class="'. ($active_term_id === false ? 'active' : '') .'" data-filterclass="product">All</a></li>';
	
	foreach ( $product_categories as $product_category ) {
		$filtername = str_replace(" ","-",strtolower($product_category->name));
		echo '<li><a href="' . get_term_link( $product_category ) . '" class="'. ($active_term_id === $product_category->term_id ? 'active' : '') .'" data-filterclass="product_cat-'. $filtername .'">' . $product_category->name . '</a></li>';
	}
	
	echo "</ul>";
}

add_action( "woocommerce_archive_description", "add_woocommerce_category_filters", 5 );


// Renames "Category" to "Department"
/*
function eugmag_rename_category() {
	global $wp_taxonomies;
	$wp_taxonomies['category']->labels->name = "Departments";
	$wp_taxonomies['category']->labels->singular_name = 'Department';
	$wp_taxonomies['category']->labels->search_items = 'Search Departments';
	$wp_taxonomies['category']->labels->popular_items = 'Popular Departments';
	$wp_taxonomies['category']->labels->all_items = 'All Departments';
	$wp_taxonomies['category']->labels->parent_item = 'Parent Department';
	$wp_taxonomies['category']->labels->parent_item_colon = 'Parent Department:';
	$wp_taxonomies['category']->labels->edit_item = 'Edit Department';
	$wp_taxonomies['category']->labels->view_item = 'View Department';
	$wp_taxonomies['category']->labels->update_item = 'Update Department';
	$wp_taxonomies['category']->labels->add_new_item = 'Add New Department';
	$wp_taxonomies['category']->labels->new_item_name = 'New Department Name';
	$wp_taxonomies['category']->labels->separate_items_with_commas = 'Separate departments with commas';
	$wp_taxonomies['category']->labels->add_or_remove_items = 'Add or remove departments';
	$wp_taxonomies['category']->labels->choose_from_most_used = 'Choose from most used departments';
	$wp_taxonomies['category']->labels->not_found = 'No departments found';
	$wp_taxonomies['category']->labels->no_terms = 'No departments';
	$wp_taxonomies['category']->labels->menu_name = 'Departments';
	$wp_taxonomies['category']->label = 'Departments';
}

add_action( 'init', 'eugmag_rename_category' );
*/



// Disable Authorize.net gateway for ad purchases, and vice versa for other purchases
function eugmag_filter_gateways( $gateways ) {
	if ( is_admin() ) return $gateways;
	if ( WC()->cart === null ) return $gateways;
	
	$ad_product_id = function_exists('ldadstore_get_ad_product_id') ? ldadstore_get_ad_product_id() : -1;
	
	foreach( WC()->cart->get_cart_contents() as $key => $values ) {
		if ( $values['product_id'] === $ad_product_id ) {
			// Do not pay for ads with credit card here, ads use COD.
			unset($gateways['authorize_net_cim_credit_card']);
		}else{
			// Cash On Delivery is not meant for non-ad products.
			unset($gateways['cod']);
		}
	}

	if ( empty($gateways) ) {
		// Prevent this notice from being shown multiple times using a static $once.
		wc_clear_notices();
		$cart_url = wc_get_cart_url();
		wc_add_notice( 'You cannot purchase an advertisement and different product at the same time. Please <a href="'. esc_attr($cart_url) .'">return to your cart</a> and remove one of these items.', "error" );
	}

	return $gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'eugmag_filter_gateways', 1);



// add google maps API key to ACF backend
add_filter('acf/settings/google_api_key', function () {
	return rs_get_google_maps_api_key();
});

/**
 * Get cover image settings for the current page
 *
 * @return mixed
 */
function em_get_cover_header( $post_id = null ) {
	if ( $post_id === null ) $post_id = get_the_ID();
	
	// Field group: Homepage Display
	// https://growmag.com/wp-admin/post.php?post=8278&action=edit
	// cover-logo (image)
	// cover-image (image)
	// cover-title (text area)
	// cover-subtitle (text area)
	// cover-button (repeater -> url, label)
	// --
	// cover-title-color (color picker)
	// cover-subtitle-color (color picker)
	// cover-button-bg-color (color picker)
	// cover-button-text-color (color picker)
	// cover-icon-color (color picker)
	// --
	//
	// logo-position (select -> Left, Center, Right)
	// cover-position (select)
	// cover-title-align (select)
	// cover-subtitle-align (select)
	// cover-button-align (select)
	// --
	// cover-stories (repeater -> cover-story [post object])
	// --
	// home-depts (repeater -> department [taxonomy])
	
	// Field group: Post fields (show on: posts)
	// https://growmag.com/wp-admin/post.php?post=4669&action=edit
	// lead_in (taxonomy)
	// snippet (wysiwyg)
	
	// Field group: Post fields (show on: posts, the weekender)
	// https://growmag.com/wp-admin/post.php?post=8304&action=edit
	// subtitle (text)
	// post_author (text)
	
	// Field group: Color Theme (show on: posts, tax:category, pages (but not front page), weekender posts)
	// https://growmag.com/wp-admin/post.php?post=8335&action=edit
	// photo_darkness (radio -> dark, light)
	
	// Field group: Branding: WooCommerce (options page: branding)
	// https://growmag.com/wp-admin/post.php?post=8426&action=edit
	// header_background (image)
	// header_background_mobile (image)
	
	// Field group: Events Header Image (options page: header image)
	// https://growmag.com/wp-admin/post.php?post=8173&action=edit
	// events-cover-image (image)
	
	// Field group: Branding (options page: branding)
	// https://growmag.com/wp-admin/post.php?post=8156&action=edit
	// logo (white)
	// logo_black
	// use_logo_for_woocommerce
	// open_graph
	// social_networks
	// copyright_text
	
	// Field group: Dept Header Image (tax:category)
	// https://growmag.com/wp-admin/post.php?post=8148&action=edit
	// dept_header_img (image)
	
	$cover = array(
		'image' => get_post_thumbnail_id( $post_id ),
		
		'iconcolor' => get_field( 'photo_darkness', $post_id, false),
		
		'logo' => array(
			'image' => get_field( 'logo', 'options', false ),
			'align' => 'left',
		),
	);
	
	if ( get_field( 'photo_darkness', $post_id ) == 'light' ) {
		$cover['logo']['image'] = get_field( 'logo_black', 'options', false );
	}
	
	// Events pages
	if ( get_post_type() == "tribe_events" ) {
		$img = get_field( 'events-cover-image', 'options', false );
		if ( $img ) $cover['image'] = $img;
	}
	
	// Digital Issues
	if ( is_post_type_archive( 'digital-issue') ) {
		$img = get_field( 'archive_cover_image', 'digital_issues', false );
		if ( $img ) $cover['image'] = $img;
		
		$img = get_field( 'archive_cover_logo', 'digital_issues', false );
		if ( $img ) $cover['logo']['image'] = $img;
		
		$color = get_field( 'iconcolor', 'digital_issues', false );
		if ( $color == 'light' ) $cover['iconcolor'] = 'light';
		if ( $color == 'dark' ) $cover['iconcolor'] = 'dark';
	}
	
	// WooCommerce
	if ( function_exists('is_woocommerce') ) {
		$is_woocommerce = false;
		if ( is_woocommerce() ) $is_woocommerce = true;
		if ( ld_is_woocommerce_page() ) $is_woocommerce = true;
		
		if ( $is_woocommerce ) {
			$img = get_field( 'header_background', 'options' );
			if ( $img ) $cover['image'] = $img;
		}
	}
	
	// Fall back to front page. Normally front page is not used here, see cover-front-page.php
	if ( empty($cover['image']) ) {
		$front_page_id = gm_get_front_page_id();
		$cover['image'] = get_field( 'cover-image', $front_page_id, false );
		$cover['iconcolor'] = get_field( 'cover-icon-color', $front_page_id, false );
		$cover['logo']['image'] = get_field( 'cover-logo', $front_page_id, false );
		$cover['logo']['align'] = get_field( 'logo-position', $front_page_id, false );
	}
	
	return $cover;
}

/**
 * Outputs a data-location attribute displaying the relative path to the current file
 *
 * @return void
 */
function gm_data_location( $p = __FILE__ ) {
	$ob = debug_backtrace();
	$last_file = $ob[0]['file'];
	
	// remove flywheel wp directory
	if ( strpos($last_file, 'themes/growmag') !== false ) {
		$p = preg_replace( '/.*?themes\/growmag\//', '/', $last_file);
	}else{
		$p = preg_replace( '/.*?themes\//', '/wp-content/themes/', $last_file);
	}
	echo ' data-location="'. esc_attr($p) .'" ';
}


function gm_logo_css() {
	// $darkness = get_field( 'photo_darkness' );
	$logo_white = get_field( 'logo', 'options', false );
	$logo_black = get_field( 'logo_black', 'options', false );
	
	$white_src = wp_get_attachment_image_src( $logo_white, 'full' );
	$black_src = wp_get_attachment_image_src( $logo_black, 'full' );
	
	$file = str_replace( ABSPATH, '/', __FILE__ );
	?>
	<style data-file="<?php echo $file; ?>">
		.dark-photo .eugmaglogo a { background-image: url(<?php echo esc_attr($white_src[0]); ?>); }
		.light-photo .eugmaglogo a { background-image: url(<?php echo esc_attr($black_src[0]); ?>); }
	</style>
	<?php
}
add_action( 'wp_print_scripts', 'gm_logo_css' );

/**
 * Some posts have a weird ￼ character in the title. This removes them.
 * Example: https://growmag.com/category/cultivate-by-state/
 *
 * @param $content
 *
 * @return string
 */
function gm_remove_OBJ_char( $content ) {
	if ( is_admin() ) return $content;
	if ( wp_doing_ajax() ) return $content;
	if ( wp_is_json_request() ) return $content;
	
	$search = '￼';
	$replace = '';
	
	return str_replace( $search, $replace, $content );
}
ob_start( 'gm_remove_OBJ_char' );

function gm_get_front_page_id() {
	return (int) get_option( 'page_on_front' );
}

/**
 * Displays the primary menu composed of departments matching the front page.
 *
 * @return void
 */
function gm_display_primary_menu() {
	echo '<h2>Categories</h2>';
	echo '<nav class="nav-menu nav-mobile nav-departments">';
	
	// Using a menu
	if ( $menu = ld_nav_menu( 'categories' ) ) {
		echo $menu; // contains <ul.nav-login>
	}
	
	// Use front page departments
	/*
	$front_page_id = gm_get_front_page_id();
	$depts = get_field( 'home_depts', $front_page_id );
	
	// Add "Feature Stories" term (id: 108)
	// https://growmag.com/wp-admin/term.php?taxonomy=category&tag_ID=108&post_type=post
	array_unshift( $depts, array( 'department' => 108 ) );
	
	echo '<ul class="nav-list">';
	foreach ( $depts as $d ) {
		$term_id = $d['department'] ?? false;
		$term = $term_id ? get_term_by( 'id', $term_id, 'category' ) : false;
		if ( ! $term instanceof WP_Term ) continue;
		
		printf(
			'<li id="menu-item-%d" class="menu-item"><a href="%s">%s</a></li>',
			esc_attr( $term_id ),
			esc_attr( get_term_link( $term ) ),
			esc_html( $term->name )
		);
	}
	echo '</ul>';
	*/
	
	echo '</nav>';
}

/**
 * Display the secondary menu as defined in appearance > menus
 *
 * @return void
 */
function gm_display_secondary_menu() {
	echo '<nav class="nav-menu nav-mobile nav-pages">';
	
	if ( $menu = ld_nav_menu( 'pages' ) ) {
		echo $menu; // contains <ul.nav-login>
	}
	
	echo '<ul class="nav-login"><li><a href="/wp-admin/">Log in</a></li></ul>';
	
	echo '</nav>';
}

function gm_page_wrapper_start() {
	if ( is_front_page() ) return;
	
	?>
	<div id="content" class="clearfix">
		<div class="inside">
	<?php
}

function gm_page_wrapper_end() {
	if ( is_front_page() ) return;
	
	?>
		</div> <!-- .inside.narrow -->
	</div> <!-- #content -->
	<?php
}