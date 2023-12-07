<?php

add_theme_support( 'woocommerce' );

/*

Customizations for WooCommerce

	ld_more_woocommerce_body_classes()
		Adds more classes to the <body> tag specific to WooCommerce.

	ld_woocommerce_display_tracking_code_header()
	ld_woocommerce_display_tracking_code_body()
		Displays tracking codes when checkout is completed for WooCommerce

	ld_is_woocommerce_page()
		Checks if the current page is a WooCommerce page, which are not checked within is_woocommerce().

	ld_woocommerce_before()
	ld_woocommerce_after()
		Adds markup before and after woocommerce, to give our theme's sidebar and other features.

	ld_woocommerce_custom_title()
		Custom WooCommerce title & breadcrumbs

	ld_woocommerce_disable_title_breadcrumbs()
		Disable default title & breadcrumbs, which have moved to custom title

	ld_disable_woocommerce_gloss_style( $enqueue_styles )
		Disable the "gloss" stylesheet, but keeps the structural/responsive stylsheets.

	ld_woocommerce_filter_shortcodes()
		Enable shortcodes on specific WooCommerce filters

	ld_woocommerce_update_header_image_email()
		Update the header image used for WooCommerce emails
*/

// change hooks
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );


function ld_more_woocommerce_body_classes( $classes ) {
	if ( is_page_template( 'woocommerce.php' ) ) {
		$classes[] = 'woocommerce';
	}else{
		if ( is_page() && get_the_ID() ) {
			if ( get_the_ID() == get_option( 'woocommerce_cart_page_id' ) ) $classes[] = 'woocommerce woocommerce-cart';
			if ( get_the_ID() == get_option( 'woocommerce_checkout_page_id' ) ) $classes[] = 'woocommerce woocommerce-checkout';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'ld_more_woocommerce_body_classes' );


function ld_woocommerce_display_tracking_code_header() {
	// Conversion Codes - Header
	// This occurs when we are on the checkbox page with an order which has been paid for, and can only happen once per order.
	if ( class_exists('WC_Order') && get_the_ID() == get_option('woocommerce_checkout_page_id') ) {
		if ( $order_id = get_query_var('order-received') ) {
			$order = new WC_Order($order_id);

			// If the order is complete, pending, or closed...
			if ( $order && in_array($order->status, array( 'completed', 'processing', 'closed' )) ) {
				// Make sure the code hasn't been displayed for this order
				if ( !get_post_meta($order_id, 'conversion-code-displayed', true) ) {
					// Remember that we completed this order, this carries over to the <body> function.
					add_filter( 'ld_checkout_completed_show_tracking', '__return_true' );

					// Display the code
					echo get_field( 'tracking_head', 'options', false );

					// Remember that we displayed the code for this order.
					update_post_meta($order_id, 'conversion-code-displayed', true);
				}
			}
		}
	}

}
add_action( 'wp_head', 'ld_display_tracking_code_header', 30 );


function ld_woocommerce_display_tracking_code_body() {
	// Piggy-back off the calculation from ld_display_tracking_code_header()
	if ( apply_filters( 'ld_checkout_completed_show_tracking', false ) ) {
		echo get_field( 'tracking_body', 'options', false );
	}
}
add_action( 'wp_footer', 'ld_display_tracking_code_body', 30 );


// Return true if on checkour or cart page, which do not count within is_woocommerce()
if ( !function_exists('ld_is_woocommerce_page') ) {
function ld_is_woocommerce_page() {
	if ( !is_singular('page') ) return false;

	$post_id = get_the_ID();

	if ( $post_id == get_option('woocommerce_cart_page_id') ) return true;
	if ( $post_id == get_option('woocommerce_checkout_page_id') ) return true;

	return false;
}
}

// Display page markup before woocommerce
function ld_woocommerce_before() {
	?>
	<article <?php post_class('loop-single loop-single-woocommerce'); ?>>
		<div class="inside narrow">
	<?php
}
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
add_action('woocommerce_before_main_content', 'ld_woocommerce_before', 10);

// End page markup after woocommerce
function ld_woocommerce_after() {
	?>
		</div> <!-- /.inside -->
	</article>
	<?php
}
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_after_main_content', 'ld_woocommerce_after', 10);

/**
 * Modify WooCommerce breadcrumbs to remove the duplicate /shop/ page
 *
 * @param array $crumbs
 * @param WC_Breadcrumb $WC_Breadcrumb
 *
 * @return array
 */
function ld_woocommerce_breadcrumbs( $crumbs, $WC_Breadcrumb ) {
	
	if ( count($crumbs) >= 2 && $crumbs[0][1] == $crumbs[1][1] ) {
		// Remove the second item
		unset( $crumbs[1] );
		
		// Re-index from 0
		$crumbs = array_values( $crumbs );
	}

	return $crumbs;
}
add_filter( 'woocommerce_get_breadcrumb', 'ld_woocommerce_breadcrumbs', 30, 2 );

// Custom WooCommerce title & breadcrumbs
function ld_woocommerce_custom_title() {
	?>
	<div class="floating-header">
		<h1 class="title"><?php woocommerce_page_title(); ?></h1>

		<?php
		// Singular pages may have a subtitle
		if ( ld_is_woocommerce_page() && $subtitle = get_field( 'subtitle', get_the_ID() ) ) {
			printf( '<h3 class="loop-subtitle">%s</h3>', $subtitle );
		}

		woocommerce_breadcrumb();
		?>
	</div>
	<?php
}
add_action( 'woocommerce_before_main_content', 'ld_woocommerce_custom_title', 80 );

// Disable default title & breadcrumbs, which have moved to custom title
function ld_woocommerce_disable_title_breadcrumbs() {
	add_filter( 'woocommerce_show_page_title', '__return_false', 40 );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
}
add_action( 'init', 'ld_woocommerce_disable_title_breadcrumbs' );


// Disable the "gloss" stylesheet, but keeps the structural/responsive stylsheets.
function ld_disable_woocommerce_gloss_style( $enqueue_styles ) {
	// unset( $enqueue_styles['woocommerce-general'] );
	// unset( $enqueue_styles['woocommerce-layout'] );
	// unset( $enqueue_styles['woocommerce-smallscreen'] );
	
	return $enqueue_styles;
}
//add_filter( 'woocommerce_enqueue_styles', 'ld_disable_woocommerce_gloss_style' );

// Enable shortcodes on specific WooCommerce filters
function ld_woocommerce_filter_shortcodes() {
	add_filter( 'woocommerce_email_footer_text', 'do_shortcode', 80 );
}
add_action( 'init', 'ld_woocommerce_filter_shortcodes' );


// Disable Order Notes on checkout screen
function ld_disable_order_notes( $fields ) {
	unset($fields['order']['order_comments']);
	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'ld_disable_order_notes' );