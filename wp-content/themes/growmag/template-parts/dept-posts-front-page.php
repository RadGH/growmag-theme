<?php

/*
$menu = wp_get_nav_menu_items( 14 );
foreach ( $menu as $key => $val ) {
	$menu[ $key ] = $val->object_id;
}
*/
$menu = get_field( 'home_depts' );
if ( $menu ) $menu = wp_list_pluck($menu, 'department');

$post_not_in = array();
if ( $rows = get_field( 'cover-stories' ) ) {
	foreach ( $rows as $row ) {
		$post_not_in[] = $row['cover-story']->ID;
	}
}

$weekenderPosts = get_posts( array(
	'no_found_rows' => true,
	'showposts'     => 3,
	'post_type'     => 'weekender',
) );

$first = true;

foreach ( $menu as $deptID ) {
	if ( empty($deptID) ) continue;
	
	$deptPosts = get_posts( array(
		'no_found_rows' => true,
		'showposts'     => 3,
		'cat'           => $deptID,
		'post__not_in'  => $post_not_in,
	) );
	output_posts_from_query( $deptPosts, get_category_link( $deptID ), get_cat_name( $deptID ) );
	
	// Display Weekender Posts as the second item
	if ( $first ) {
		$first = false;
		output_posts_from_query( $weekenderPosts, get_post_type_archive_link( 'weekender' ), 'The Weekender' );
	}
}



function output_posts_from_query( $get_posts_query, $deptLink, $deptName ) {
	if ( ! $get_posts_query ) return;
	
	echo '<div class="layout-row home-dept-row clear inside">';
	
	$firstpost = true;
	
	foreach ( $get_posts_query as $post ) {
		get_field( 'photo_darkness', $post->ID ) == 'dark' ? $classes = 'dark-photo' : $classes = 'light-photo';
		
		if ( $firstpost ) {
			$classes .= ' header-post header-standard';
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'cover-large' );
		}else{
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'cover-small' );
		}
		
		if ( empty($img) ) $img = array( '' );
		
		echo '<div class="post ' . $classes . '" style="background-image: url(' . $img[0] . ')">';
		
		if ( $firstpost ) {
			echo '<div class="home-header-post-wrapper">';
			
			echo '<div class="header-line">';
				echo '<h2 class="category-header"><a href="' . $deptLink . '">' . $deptName . '</a></h2>';
			echo '</div>';
			
			gm_display_secondary_overlay( $post->ID, get_permalink() );
			
			/*
			echo '<div class="overlay">';
				echo '<h3 class="title">' . $title . '</h3>';
				if ( $subtitle ) echo '<h4 class="subtitle">' . $subtitle . '</h4>';
				echo '<a href="' . get_permalink( $post->ID ) . '">';
				if ( $firstpost ) {
					echo '<div class="readmore button button-white">Read Now</div>';
				}
				echo '</a>';
			echo '</div>';
			*/
			
			echo '</div>'; // end .home-header-post-wrapper
			
		} else {
			
			gm_display_secondary_overlay( $post->ID );
			
			/*
			echo '<a href="' . get_permalink( $post->ID ) . '">';
			echo '<div class="overlay">';
			echo '<h3 class="title">' . $title . '</h3>';
			if ( $subtitle ) echo '<h4 class="subtitle">' . $subtitle . '</h4>';
			echo '</div>';
			echo '</a>';
			*/
		}
		
		echo '</div>'; // end .post
		
		$firstpost = false;
	}
	
	echo '</div>'; // end .layout-row
}