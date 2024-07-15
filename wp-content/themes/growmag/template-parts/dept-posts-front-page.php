<?php

/*
$menu = wp_get_nav_menu_items( 14 );
foreach ( $menu as $key => $val ) {
	$menu[ $key ] = $val->object_id;
}
*/

/**
 * @var array $home_depts {
 *     @type int   $department   Term ID
 *     @type int[] $sticky_posts Post IDs to appear first
 * }
 */
$home_depts = get_field( 'home_depts' );

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

foreach ( $home_depts as $d ) {
	$deptID = $d['department'] ?? false;
	$sticky_post_ids = $d['sticky_posts'] ?? array();
	if ( empty($deptID) ) continue;
	
	$posts_to_display = 3;
	$display_posts = array();
	
	$stickyPosts = $sticky_post_ids ? get_posts(array(
		'posts_per_page' => $posts_to_display,
		// 'cat'           => $deptID,
		'post__not_in'  => $post_not_in,
		'post__in'      => $sticky_post_ids,
	)) : array();
	
	if ( $stickyPosts ) {
		$posts_to_display -= count($stickyPosts);
		$display_posts = array_merge( $display_posts, $stickyPosts );
		$post_not_in = array_merge( $post_not_in, wp_list_pluck( $stickyPosts, 'ID' ) );
	}
	
	if ( $posts_to_display > 0 ) {
		$deptPosts = get_posts( array(
			'posts_per_page' => $posts_to_display,
			'cat'           => $deptID,
			'post__not_in'  => $post_not_in,
		));
		
		if ( $deptPosts ) {
			$posts_to_display -= count($deptPosts);
			$display_posts = array_merge( $display_posts, $deptPosts );
			$post_not_in = array_merge( $post_not_in, wp_list_pluck( $deptPosts, 'ID' ) );
		}
	}
	
	if ( $display_posts ) {
		output_posts_from_query( $display_posts, get_category_link( $deptID ), get_cat_name( $deptID ) );
	}
	
	// Display Weekender Posts as the second item
	if ( $first ) {
		$first = false;
		output_posts_from_query( $weekenderPosts, get_post_type_archive_link( 'weekender' ), 'The Weekender' );
	}
}



function output_posts_from_query( $get_posts_query, $deptLink, $deptName ) {
	if ( ! $get_posts_query ) return;
	
	echo "\n<!-- \n" . 'output_posts_from_query' . "\n\n";
	echo esc_html(print_r(array(
		'count(get_posts_query)' => count($get_posts_query),
		'deptLink' => $deptLink,
		'deptName' => $deptName,
	), true));
	echo "\n\n-->";
	
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
			
			gm_display_secondary_overlay( $post->ID, get_permalink( $post->ID ) );
			
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
			
			gm_display_secondary_overlay( $post->ID, get_permalink( $post->ID ), false );
			
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