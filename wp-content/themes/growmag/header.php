<!doctype html>
<html <?php language_attributes(); ?> class="<?php ld_html_classes( 'no-js' ); ?>">
<head>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<?php
// Display top ad
if ( is_front_page() ) {
	echo do_shortcode( '[ad location="Before Header (full width)"]' );
}

// Display mobile menu
get_template_part( 'template-parts/menu' );

// Display wrapper
gm_page_wrapper_start();

// Display cover / header image
$cover_type = '';
if ( is_front_page() ) $cover_type = 'front-page';
get_template_part( 'template-parts/cover', $cover_type );