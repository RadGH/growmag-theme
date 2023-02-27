<?php
// Add featured image to rss items as a media tag
function lm_add_feature_image_to_rss( $content ) {
    global $post;
	
	$img_id = false;
	
	// From post meta, custom field
	$key = apply_filters( "rss_image_meta_key", "blog-image" );
	if ( $key && $i = get_post_meta( get_the_ID(), $key, true ) ) {
		$img_id = image_get_attachment_id( $i );
	}
	
	// Fall back to featured image
	if ( !$img_id ) {
		$img_id = get_post_thumbnail_id();
	}
	
	// Fall back to first image from post content
	if ( !$img_id && $i = limelight_archive_thumbnail() ) {
		if ( $i ) $img_id = image_get_attachment_id( $i );
	}
	
	// If we have an image URL, attach it
	if( $img_id ) {
		$full_size = smart_media_size( (int) $img_id, 'large' );
		
		$type = 'image/jpg';
		if ( substr($full_size, -3) == 'png' ) $type = 'image/png';
		
		echo "\t";
		echo sprintf(
			'<media:content url="%s" medium="image" width="560" height="280" type="%s" />',
			esc_attr($full_size),
			esc_attr($type)
		);
		echo "\n";
	}
}
add_action( 'rss2_item', 'lm_add_feature_image_to_rss' );

// Add media namespace to RSS
function lm_add_image_ns_to_rss() {
	echo 'xmlns:media="http://search.yahoo.com/mrss/"' . "\n\t";
}
add_action( 'rss2_ns', 'lm_add_image_ns_to_rss' );