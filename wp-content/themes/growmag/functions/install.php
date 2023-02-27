<?php
add_action( 'after_setup_theme', 'theme_setup_settings' );

function theme_setup_settings() {
	add_image_size('cover-full', 1400, 680, true);
	add_image_size('cover-large', 1020, 490, true);
	add_image_size('cover-small', 360, 235, true);
	
	// 2x quality @see ld_downsize_2x_images()
	add_image_size('cover-full-2x', 1400*2, 680*2, true);
	add_image_size('cover-large-2x', 1020*2, 490*2, true);
	add_image_size('cover-small-2x', 360*2, 235*2, true);
	
	add_image_size('thumbnail-uncropped', 340, 250, false);

	add_image_size('mobile-alt', 600, 415, true);

	// Enable RSS feed channels in the document header
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress handle the title tag
	add_theme_support( 'title-tag' );

	// Enable featured images for posts and pages
	add_theme_support( 'post-thumbnails' );

	// Enable HTML5 for the specified templates
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
}

function ld_get_attachment_mobile( $attachment_id ) {
	// Check for a mobile version of the photo. We need to make sure it matches the size above.
	if ( $m = wp_get_attachment_image_src( $attachment_id, 'mobile-alt' ) ) {
		if ( $m[1] == 600 && $m[2] == 415 ) {
			return $m;
		}
	}
	return false;
}

/**
 * Use 2x quality image when available (cover-large-2x, cover-medium-2x, cover-small-2x)
 *
 * @param $result
 * @param $attachment_id
 * @param $size
 *
 * @return array|mixed
 */
function ld_downsize_2x_images( $result, $attachment_id, $size ) {
	if ( is_array($size) ) return $result;
	if ( ! str_starts_with( $size, 'cover-' ) ) return $result;
	
	$size_2x = $size . '-2x';
	$image_2x = image_get_intermediate_size( $attachment_id, $size_2x );
	
	// If we can't get 2x version, use the full quality version
	if (  !$image_2x ) {
		remove_filter( 'image_downsize', 'ld_downsize_2x_images', 5 );
		return wp_get_attachment_image_src( $attachment_id, 'full' );
		add_filter( 'image_downsize', 'ld_downsize_2x_images', 5, 3 );
	}
	
	if ( $image_2x ) {
		return array(
			0 => $image_2x['url'],
			1 => $image_2x['width'],
			2 => $image_2x['height'],
			3 => true,
		);
	}
	
	return $result;
}
add_filter( 'image_downsize', 'ld_downsize_2x_images', 5, 3 );