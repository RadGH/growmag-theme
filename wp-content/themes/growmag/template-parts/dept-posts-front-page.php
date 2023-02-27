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
	if ( $get_posts_query ) :
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
				echo '<div class="overlay">';
				echo '<h3>' . $post->post_title . '</h3>';
				echo '<h4 class="subtitle">' . get_field( 'subtitle', $post->ID ) . '</h4>';
				echo '<a href="' . get_permalink( $post->ID ) . '">';
				echo '<div class="readmore button button-white">Read Now</div>';
				echo '</a>';
				echo '</div>';
				echo '</div>';
				$firstpost = false;
			} else {
				echo '<a href="' . get_permalink( $post->ID ) . '"><div class="overlay">';
				echo '<h3>' . $post->post_title;
				if ( get_the_title() && get_field( 'subtitle', get_the_ID() ) ) {
					echo ':<br />';
				}
				echo '<span class="overlay-subtitle">' . get_field( 'subtitle', $post->ID ) . '</span></h3>';
				echo '</div></a>';
			}
			echo '</div>';
		}
		echo '</div>';
	endif;
}