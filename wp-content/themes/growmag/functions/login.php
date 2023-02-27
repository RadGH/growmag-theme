<?php
/*

custom_logo_image()
custom_logo_url()
custom_login_title()
	Use the blog information, not wordpress.org

*/

/* Update Wordpress Login Image */
function custom_logo_image() {
	// $logo_id = get_field('logo','options');
	// $image_url = $logo_id ? wp_get_attachment_url($logo_id);
	$image_url = get_stylesheet_directory_uri() . '/includes/images/grow-logo-small-black.png';

	if ( $image_url ) {
		?>
		<style>
			body.login #login h1 a {
				background: url(<?php echo esc_attr($image_url); ?>) no-repeat center top transparent;
				width: 140px;
				height: 78px;
				background-size: contain;
			}
		</style>
		<?php
	}
}
add_action( 'login_head', 'custom_logo_image' );


function custom_logo_url() { return get_bloginfo('url'); }
add_filter( 'login_headerurl', 'custom_logo_url' );


function custom_login_title() { return get_bloginfo('title'); }
add_filter( 'login_headertitle', 'custom_login_title' );

