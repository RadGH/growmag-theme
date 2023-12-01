<?php

// if we're removing weekender posts automatically, set up the cron event
// otherwise remove any existing cron event
if ( function_exists('get_field') ) {
	$remove_posts = get_field( "weekender_remove_posts", "options" );
	if ( ! $remove_posts || $remove_posts == 'no' ) {
		$timestamp = wp_next_scheduled( 'weekender_scheduled_remove' );
		wp_unschedule_event( $timestamp, 'weekender_scheduled_remove' );
	} else {
		if ( ! wp_next_scheduled( 'weekender_scheduled_remove' ) ) {
			wp_schedule_event( time(), 'daily', 'weekender_scheduled_remove' );
		}
		add_action( 'weekender_scheduled_remove', 'weekender_scheduled_remove_fun' );
	}
}


// trash or delete any weekender posts older than the given threshold
function weekender_scheduled_remove_fun() {
	
	$remove_posts = get_field( "weekender_remove_posts", "options" );
	if ( ! $remove_posts || ! in_array( $remove_posts, array( "trash", "delete" ) ) ) {
		// posts are not being trashed or deleted
		return;
	}
	
	$removal_threshold_days = (int) get_field( "weekender_days_to_remove", "options" );
	if ( ! $removal_threshold_days ) {
		// invalid days to remove
		return;
	}
	
	$weekender_posts = get_posts( array(
		'posts_per_page' => - 1,
		'post_type'      => 'weekender',
		'fields'         => 'ids',
		'post_status'    => ( $remove_posts == 'delete' ) ? array( 'publish', 'trash' ) : 'publish',
	) );
	
	$delete_threshold = time() - ( DAY_IN_SECONDS * $removal_threshold_days );
	
	foreach ( $weekender_posts as $postid ) :
		
		if ( $delete_threshold < get_the_time( 'U', $postid ) ) {
			continue;
		}
		
		if ( $remove_posts == 'delete' ) {
			wp_delete_post( $postid, true );
		} else {
			wp_trash_post( $postid );
		}
	
	endforeach;
}


// Register Custom Post Type
function register_weekender_post_type() {
	
	$labels = array(
		'name'                  => 'The Weekender',
		'singular_name'         => 'The Weekender',
		'menu_name'             => 'The Weekender',
		'name_admin_bar'        => 'The Weekender',
		'archives'              => 'Post Archives',
		'attributes'            => 'Post Attributes',
		'parent_post_colon'     => 'Parent Post:',
		'all_posts'             => 'All Posts',
		'add_new_post'          => 'Add New Post',
		'add_new'               => 'Add New',
		'new_post'              => 'New Post',
		'edit_post'             => 'Edit Post',
		'update_post'           => 'Update Post',
		'view_post'             => 'View Post',
		'view_posts'            => 'View Posts',
		'search_posts'          => 'Search Post',
		'not_found'             => 'Not found',
		'not_found_in_trash'    => 'Not found in Trash',
		'featured_image'        => 'Featured Image',
		'set_featured_image'    => 'Set featured image',
		'remove_featured_image' => 'Remove featured image',
		'use_featured_image'    => 'Use as featured image',
		'insert_into_post'      => 'Insert into post',
		'uploaded_to_this_post' => 'Uploaded to this post',
		'posts_list'            => 'Posts list',
		'posts_list_navigation' => 'Posts list navigation',
		'filter_posts_list'     => 'Filter posts list',
	);
	$args   = array(
		'label'               => 'The Weekender',
		'description'         => 'Weekender articles.',
		'labels'              => $labels,
		'supports'            => array( 'title', 'author', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
		'taxonomies'          => array( 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'rewrite'             => array( 'slug' => 'the-weekender' ),
	);
	register_post_type( 'weekender', $args );
	
	if ( function_exists( 'acf_add_options_sub_page' ) ) {
		$args = array(
			'page_title'  => 'The Weekender Options',
			'menu_title'  => 'Options',
			'parent_slug' => 'edit.php?post_type=weekender',
		);
		acf_add_options_sub_page( $args );
	}
	
}

add_action( 'init', 'register_weekender_post_type', 0 );