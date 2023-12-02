<?php

if ( have_rows( 'cover-stories' ) ):
	echo '<div class="layout-row home-cover-row clear inside narrow">';

	echo '<h2><a href="/category/feature/">Feature Stories</a></h2>';
	
	echo '<div class="grid-3">';
	
	while ( have_rows('cover-stories') ) : the_row();
		$story = get_sub_field('cover-story');
		$img = wp_get_attachment_image_src( get_post_thumbnail_id( $story->ID), 'cover-small' );
		get_field( 'photo_darkness', $story->ID ) == 'dark' ? $classes = 'dark-photo' : $classes = 'light-photo';
		
		echo '<div class="post ' . $classes . '" style="background-image: url(' . $img[0] . ')">';
		echo '<a href="' . get_permalink( $story->ID ) . '">';
		gm_display_secondary_overlay( $story->ID );
		echo '</a>';
		echo '</div>';
	endwhile;
	
	echo '</div>'; // end .grid-3
	
	echo '</div>'; // end .layout-row
endif;