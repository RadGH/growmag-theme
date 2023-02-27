<?php
/*

Functions:

	lm_remove_empty_links_from_menus( $item_output, $item, $depth, $args )
		Remove <a> element from menus that do not have a URL (replace with <span class="empty-link"></span>)
	
	class Limelight_Walker_Nav_Menu
	limelight_nav_menu_args( $args )
	limelight_menu_item_id_class( $menu_items, $args )
		Class & Functions that enhance the wp_nav_menu object.
		Each element gets new classes, listed below:
			ul.sub-menu:
				depth-N     - The depth of the element, example: ul.nav-menu > li.menu-item.depth-1 > ul.sub-menu.depth-1 > li.menu-item.depth-2
			
			li.menu-item:
				depth-N     - The depth of the element, like above. Top level elements will have depth-1.
				object-id-N - The ID of the object (eg, the page ID). This is different than the menu ID. If no ID is available (custom links), uses: object-id-none
				nth-N       - The order of the element based on it's siblings, started from 1. example: ul > li.nth-1 + li.nth-2 + li.nth-3
				menu-first  - An alias of :first-child or .nth-1, the first element among it's siblings.
				menu-last   - An alias of :last-child. The last element of it's siblings.
	
	limelight_menu_separators( $menu_items, $args )
		Inserts menu separators (li.sep) between each menu item. Must be enabled.
*/


// Remove <a> element from menus that do not have a URL (replace with <span class="empty-link"></span>)
function lm_remove_empty_links_from_menus( $item_output, $item, $depth, $args ) {
	// Currently, there is no filter for modifying the opening and closing <li> for a menu item.
	// This function can filter the inner <a>...</a> element of a menu. $item contains data specific to the menu item.
	// Does not affect any submenus stemming from this item.

	if ( !$item->url || $item->url == '#' ) {
		$replaces = array(
			array(
				'search'  => '<a',
				'replace' => '<span class="empty-link"',
			),
			array(
				'search'  => '</a',
				'replace' => '</span',
				'reverse' => true,
			),
			array(
				'search'  => ' target=',
				'replace' => ' data-target=',
			),
			array(
				'search'  => ' href=',
				'replace' => ' data-href=',
			),
		);


		foreach ( $replaces as $k ) {
			$search = $k['search'];
			$replace = $k['replace'];

			if ( isset( $k['reverse'] ) && $k['reverse'] ) {
				$pos = strrpos( $item_output, $search );
			}else{
				$pos = strpos( $item_output, $search );
			}

			if ( $pos !== false ) {
				$item_output = substr_replace( $item_output, $replace, $pos, strlen( $search ) );
			}
		}
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'lm_remove_empty_links_from_menus', 10, 4 );


// Add adjustments for the wp_nav_menu function to include additional classes.
class Limelight_Walker_Nav_Menu extends Walker_Nav_Menu
{
	function start_el( &$output, $item, $depth = 0, $args = array(), $current_object_id = 0 ) {
		if ( !property_exists( $item, 'classes' ) ) $item->classes = array();

		$item->classes[] = 'depth-' . ( $depth + 1 );
		$item->classes[] = 'object-id-' . ( $item->object_id ? absint( $item->object_id ) : 'none' );

		$output .= sprintf( '<li id="menu-item-%s" class="%s">', $item->ID, esc_attr( implode( ' ', $item->classes ) ) );

		// Various filters to insert custom HTML around the <a> element.

		if ( has_filter( 'menu_item_before_link' ) ) {
			$output .= apply_filters( 'menu_item_before_link', $output, $item, $depth, $args, $current_object_id );
		}

		$output .= sprintf( '<a href="%s">', esc_attr( apply_filters( 'menu_item_link', $item->url, $item, $depth, $args, $current_object_id ) ) );

		if ( has_filter( 'menu_item_prepend_link' ) ) {
			$output .= apply_filters( 'menu_item_prepend_link', $output, $item, $depth, $args, $current_object_id );
		}

		$output .= esc_html( apply_filters( 'menu_item_title', $item->title, $item, $depth, $args, $current_object_id ) );

		if ( has_filter( 'menu_item_append_link' ) ) {
			$output .= apply_filters( 'menu_item_append_link', $output, $item, $depth, $args, $current_object_id );
		}

		$output .= '</a>';

		if ( has_filter( 'menu_item_after_link' ) ) {
			$output .= apply_filters( 'menu_item_after_link', $output, $item, $depth, $args, $current_object_id );
		}

	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= sprintf( "\n%s<ul class=\"sub-menu depth-%s\">\n", $indent, ( $depth + 1 ) );
	}
}

// Use the limelight walker for nav menus by default.
function limelight_nav_menu_args( $args ) {
	if ( $args['walker'] == '' ) {
		$args['walker'] = new Limelight_Walker_Nav_Menu;
	}

	return $args;
}

add_filter( 'wp_nav_menu_args', 'limelight_nav_menu_args' );

// Iterate through all menu items. These are sorted, but are not heirarchical.
// This is done outside of the walker, since we must do this operation before we start "walking"
function limelight_menu_item_id_class( $menu_items, $args ) {
	$counter = array();

	foreach ( $menu_items as $k => $item ) {
		if ( !isset( $counter[$item->menu_item_parent] ) ) {
			$counter[$item->menu_item_parent] = 0;
		}
		$counter[$item->menu_item_parent] += 1;

		$item->menu_index = $counter[$item->menu_item_parent];
		$item->classes[] = 'nth-' . $item->menu_index;
	}

	foreach ( $menu_items as $k => $item ) {
		if ( $item->menu_index == 1 ) {
			$item->classes[] = 'menu-first';
		}
		if ( $item->menu_index == $counter[$item->menu_item_parent] ) {
			$item->classes[] = 'menu-last';
		}
	}

	return $menu_items;
}

add_action( 'wp_nav_menu_objects', 'limelight_menu_item_id_class', 10, 2 );

// Enables menu separators (<li class="sep">|</li>) between top-level menu items.
// You can either add  the action globally to affect all menus (default), or add it before (and remove after) each menu individually.
// add_action( 'wp_nav_menu_objects', 'limelight_menu_separators' );
function limelight_menu_separators( $menu_items, $args ) {
	$filtered_menu_items = array();

	$limit = count( $menu_items );

	$separator = new WP_Post();

	// These parameters will be added to our separator object.
	$add = array(
		'type'       => 'sep',
		'type_label' => 'sep',
		'title'      => '|',
		'classes'    => array( 'menu-separator' ),
	);
	foreach ( $add as $k => $v ) $separator->{$k} = $v;

	$i = 0;
	$last_sep = null;
	$last_toplevel_page = null;
	foreach ( $menu_items as $k => $item ) {
		$filtered_menu_items[$i] = $item;
		if ( $item->menu_item_parent == false ) {
			$last_toplevel_page = $i;
		}
		$i++;

		// Add our separator if this item is a top-level item (has no parent)
		if ( $item->menu_item_parent == false ) {
			// Our separator object is not a menu item, but a wp_post object. Add any missing menu item keys with a value of false (prevents errors)
			foreach ( $item as $menu_k => $menu_v ) if ( !property_exists( $separator, $menu_k ) ) {
				$separator->{$menu_k} = false;
			}

			// Use the ID of the current item, with append -sep to prevent ID clashing.
			$separator->ID = $item->ID . '-sep';

			// Add our separator to the menu
			$filtered_menu_items[$i] = $separator;
			$last_sep = $i;
			$i++;
		}
	}

	// The last element should never be a separator.
	if ( $last_sep && ( $last_toplevel_page < $last_sep ) ) {
		unset( $filtered_menu_items[$last_sep] );
	}

	return $filtered_menu_items;
}


/*
function filter_menu( $sorted_menu_items, $args ) {
  
  foreach( $sorted_menu_items as $k => $item ) {
    // -- Important Variables --
    // ID:                Post ID of this menu item
    // post_title:        Post title
    
    // title:             Title to display on the website (Not the attribute title, the actual link text)
    // url:               URL to use in the link
    // target:            Target attribute for the link
    // attr_title:        Title attribute for the link
    // menu_item_parent:  ID of the parent for this item
    
    if ( $item->url == "#sign-in" ) {
      $before = array_slice( $sorted_menu_items, 0, $k-1, true );
      $after = array_slice( $sorted_menu_items, $k, null, true );
      
      // Close the original, we then modify the parameters to suit our needs
      $clone = (array) $sorted_menu_items[$k];
      unset($sorted_menu_items[$k]);
      
      if ( is_user_logged_in() ) {
      
        $sign_in_args = array(
          'ID' => 'signin',
          'title' => 'Sign In',
          'url' => wp_login_url( get_permalink() ),
          'target' => '',
          'attr_title' => 'Sign in to ' . get_bloginfo('name'),
        );
        $sign_in_args['post_title'] = $sign_in_args['title'];
        $sign_in = (object) array_merge( $clone, $sign_in_args );
      
      }else{
        
        $log_out_args = array(
          
        );
        
      }
      
      $sorted_menu_items = $before + array( uniqid() => $sign_in ) + $after;
      break;
      
    }
  }
  
  
  return $sorted_menu_items;
  
}
add_filter( 'wp_nav_menu_objects', 'filter_menu', 10, 2 );
// */