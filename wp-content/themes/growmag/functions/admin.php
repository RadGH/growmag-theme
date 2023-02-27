<?php
/*
Functions:

	lm_simplify_admin_bar()
		Removes unecessary links from the admin bar: WordPress logo, comments, themes, customize

	lm_simplify_dashboard()
		Removes unecessary links from the dashboard menu: Settings > Media
*/


// Removes unecessary links from the admin bar: WordPress logo, comments, themes, customize
function lm_simplify_admin_bar() {
	global $wp_admin_bar;

	if ( !$wp_admin_bar || !method_exists( $wp_admin_bar, 'remove_node' ) ) {
		return;
	}

	$wp_admin_bar->remove_node( 'wp-logo' );
	$wp_admin_bar->remove_node( 'comments' );
	$wp_admin_bar->remove_node( 'themes' );
	$wp_admin_bar->remove_node( 'customize' );
}

add_action( 'wp_before_admin_bar_render', 'lm_simplify_admin_bar' );


function lm_simplify_dashboard() {
	// The media controls are predefined in install.php. The user should not customize these.
	remove_submenu_page( 'options-general.php', 'options-media.php' );
}
add_action( 'admin_menu', 'lm_simplify_dashboard' );


function toolbar_add_custom_nodes( $wp_admin_bar ) {
	// Remove the default Updates button
	$wp_admin_bar->remove_node( 'updates' ); // Available updates
	$wp_admin_bar->remove_node( 'appearance' ); // Theme > Customize

	// =======================
	// Manage
	// - Quick access to management screens: Menus, widgets, plugins, users

	$wp_admin_bar->add_menu( array(
		'id'    => 'management',
		'title' => 'Manage',
		'href'  => '#',
		'meta'  => array( 'title' => __( 'Manage various site settings' ), ),
	) );

	// Hook to add theme option pages. Used in theme-settings.php
	$manage_pages = apply_filters( 'lm-admin-menu-links', array(), $wp_admin_bar );

	if ( !empty($manage_pages) ) {
		foreach( $manage_pages as $page ) {
			$wp_admin_bar->add_node( $page );
		}
	}

	$wp_admin_bar->add_menu( array(
		'id'     => 'management-menus',
		'parent' => 'management',
		'title'  => 'Menus',
		'href'   => admin_url( 'nav-menus.php' ),
		'meta'   => array(
			'title' => 'Menus',
			'class' => 'post-type-node post-type-node-menus',
		),
	) );

	$wp_admin_bar->add_menu( array(
		'id'     => 'management-widgets',
		'parent' => 'management',
		'title'  => 'Widgets',
		'href'   => admin_url( 'widgets.php' ),
		'meta'   => array(
			'title' => 'Widgets',
			'class' => 'post-type-node post-type-node-widgets',
		),
	) );

	$wp_admin_bar->add_menu( array(
		'id'     => 'management-plugins',
		'parent' => 'management',
		'title'  => 'Plugins',
		'href'   => admin_url( 'plugins.php' ),
		'meta'   => array(
			'title' => 'Plugins',
			'class' => 'post-type-node post-type-node-plugins',
		),
	) );

	$wp_admin_bar->add_menu( array(
		'id'     => 'management-users',
		'parent' => 'management',
		'title'  => 'Users',
		'href'   => admin_url( 'users.php' ),
		'meta'   => array(
			'title' => 'Users',
			'class' => 'post-type-node post-type-node-users',
		),
	) );

	// =======================
	// Content
	// - Lists all post types and users dashboard pages.
	$wp_admin_bar->add_menu( array(
		'id'    => 'post-types',
		'title' => 'Content',
		'href'  => '#',
		'meta'  => array( 'title' => __( 'Manage custom post type content' ), ),
	) );

	// Display built-in post types: Posts, pages, media
	$args = array(
		'public'   => true,
		'show_ui'  => true,
		'_builtin' => true,
	);

	$all_post_types = get_post_types( $args );

	foreach ( $all_post_types as $key => $type ) {
		$obj = get_post_type_object( $type );

		$wp_admin_bar->add_menu( array(
			'id'     => 'post-type-' . $key,
			'parent' => 'post-types',
			'title'  => $obj->labels->menu_name,
			'href'   => admin_url( 'edit.php?post_type=' . $key ),
			'meta'   => array(
				'title' => $obj->labels->menu_name,
				'class' => 'post-type-node post-type-node-' . $key,
			),
		) );
	}

	// Custom post types.
	$args = array(
		'public'   => true,
		'show_ui'  => true,
		'_builtin' => false,
	);

	$custom_post_types = get_post_types( $args );

	if ( $custom_post_types ) {

		// Add custom post types
		foreach ( $custom_post_types as $key => $type ) {
			$obj = get_post_type_object( $type );

			$wp_admin_bar->add_menu( array(
				'id'     => 'post-type-' . $key,
				'parent' => 'post-types',
				'title'  => $obj->labels->menu_name,
				'href'   => admin_url( 'edit.php?post_type=' . $key ),
				'meta'   => array(
					'title' => $obj->labels->menu_name,
					'class' => 'post-type-node post-type-node-' . $key,
				),
			) );
		}
	}
}

add_action( 'admin_bar_menu', 'toolbar_add_custom_nodes', 60 );