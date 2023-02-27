<?php

if ( have_rows( 'cover-stories' ) ):
	echo '<div class="layout-row home-cover-row clear inside narrow">';

	echo '<h2>Feature Stories</h2>';
	
	echo '<div class="grid-3">';
	while ( have_rows('cover-stories') ) : the_row();
		$story = get_sub_field('cover-story');
		$img = wp_get_attachment_image_src( get_post_thumbnail_id( $story->ID), 'cover-small' );
		get_field( 'photo_darkness', $story->ID ) == 'dark' ? $classes = 'dark-photo' : $classes = 'light-photo';
		echo '<div class="post ' . $classes . '" style="background-image: url(' . $img[0] . ')">';
		echo '<a href="' . get_permalink( $story->ID ) . '"><div class="overlay">';
		echo '<h3>' . $story->post_title . ':<br /><span class="overlay-subtitle">' . get_field( 'subtitle', $story->ID ) . '</span></h3>';
		echo '</div></a>';
		echo '</div>';
	endwhile;
	echo '</div>';
	
	echo '</div>';
endif;