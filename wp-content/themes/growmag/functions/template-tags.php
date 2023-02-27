<?php
/*

Functions that are used within the theme's template files

	ld_html_classes()
		Function used to output html classes. Similar to body_class() built-in function.

    ld_nav_menu($name, $style = null)
        Generates a navigation menu if it exists and has any menu items.
        $name and $style can be used to define "header_primary" and assign classes for each term separately, eg nav-header nav-primary.

    ld_menu_social($class = false)
        Generates a navigation menu for social items, defined in theme settings.
        $class will be added to the classes of the menu.

*/

function ld_html_classes( $classes = array() ) {
	$classes = (array) $classes;

	$classes = apply_filters( 'html_class', $classes );

	// Get rid of any duplicate classes and escape the value of each class
	$classes = array_values($classes);

	$unique_classes = array();
	foreach($classes as $key => $name) {
		$name = esc_attr($name);
		$unique_classes[$name] = $name;
	}

	echo implode(' ', $unique_classes);
}

function ld_nav_menu( $name, $style = null ) {
	if ( $style === null ) {
		$menu_id = $name;
		$menu_name = ucwords($name);
	}else{
		$menu_id = $name . '_' . $style;
		$menu_name = ucwords($name . ' - ' . $style);
	}

	if ( !has_nav_menu( $menu_id ) ) return false;

	$args = array(
		'theme_location' => $menu_id,
		'menu' => $menu_name,
		'container' => '',
		'container_id' => '',
		'menu_class' => '',
		'items_wrap' => '<ul class="nav-list">%3$s</ul>',

		'echo' => false,
		'walker' => new Limelight_Walker_Nav_Menu,
	);

	return wp_nav_menu($args);
}

function ld_social_menu( $name = null, $class = null ) {
	$networks = get_field( 'social_networks', 'options' );

	if ( empty($networks) || count($networks) == 1 && empty($networks[0]['url']) ) {
		if ( current_user_can('manage_options') ) {
			echo '<p><strong>ld_social_menu(): No social networks have been defined under Theme Options &gt; Social Media. This menu won\'t be displayed to your visitors.</strong></p>';
		}

		return;
	}

	// Get all icon layouts, if there aren't any, give an error.
	$layouts = get_field( 'social_layouts', 'options' );

	if ( empty($layouts) || (count($layouts) == 1 && empty($layouts[0]['icons'])) ) {
		if ( current_user_can('manage_options') ) {
			echo '<p><strong>ld_social_menu(): No social media icon layouts have been defined under Theme Options &gt; Social Media. This menu won\'t be displayed to your visitors.</strong></p>';
		}

		return;
	}

	// Which icon layout will we use? Start with the first one, then look for a named one.
	$active_layout = $layouts[0];

	if ( $name !== null ) {
		foreach( $layouts as $k => $v ) {
			if ( strtolower($v['name']) == strtolower($name) ) $active_layout = $v;
		}
	}

	// Display the necessary CSS & Markup
	$classes = array( 'nav-menu', 'nav-social' );
	if ( $class !== null ) $classes[] = esc_attr($class);

	$layout_class = 'layout-' . sanitize_title_with_dashes($active_layout['name']);
	$classes[] = $layout_class;

	$icon_image = smart_media_size( $active_layout['icons'], 'full' );
	$width = (int) $active_layout['width'];
	$height = (int) $active_layout['height'];
	$margin = $active_layout['margin'];
	$styles = $active_layout['styles'];
	$services = $active_layout['services'];

	$offset_left = 0;

	if ( $margin ) {
		// Get the left margin from a CSS string allowing formats:
		//// TOP, RIGHT, BOTTOM, LEFT
		//// TOP, RIGHT/LEFT, BOTTOM
		//// TOP/BOTTOM, RIGHT/LEFT
		//// TOP/BOTTOM/RIGHT/LEFT

		if ( preg_match( '/^[\s]*(-?[0-9]+)(px)? (-?[0-9]+)(px)? (-?[0-9]+)(px)? (-?[0-9]+)(px)?[\s]*/', $margin, $matches ) ) {
			$offset_left = $matches[7];
		}else if ( preg_match( '/^[\s]*(-?[0-9]+)(px)? (-?[0-9]+)(px)?/', $margin, $matches ) ) {
			$offset_left = $matches[3];
		}else if ( preg_match( '/^[\s]*(-?[0-9]+)(px)?/', $margin, $matches ) ) {
			$offset_left = $matches[1];
		}

		$offset_left = -1 * floor($offset_left/2);
	}
	?>
	<style type="text/css">
		<?php if ( $offset_left > 1 ) { ?>
		.<?php echo $layout_class; ?> .nav-list {
			margin-left: <?php echo $offset_left; ?>px;
		}
		<?php } ?>

		.<?php echo $layout_class; ?> a {
			width: <?php echo $width; ?>px;
			height: <?php echo $height; ?>px;
		<?php if ( $margin ) echo 'margin: ' . $margin . ';'; ?>

			background: url(<?php echo esc_attr( $icon_image ); ?>) 1000px 1000px no-repeat;
		}
		<?php
		foreach( $styles as $style_index => $style ) {
			// $style_index = 0
			// $style = 'normal'
			foreach( $services as $service_index => $service ) {
				// $service_index = 0
				// $service = array( 'name' => 'Facebook' )
				
				// Check if this service is being used by Branding
				$is_used = false;
				foreach( $networks as $soc ) {
					$network_name = $soc['name'] ?? $soc['service'];
					// @tip $soc['name'] should be $soc['service']
					// $soc = array( 'service' => 'Facebook', 'url' => 'https://', 'action' => 'Like us on facebook' )
					if ( $network_name == $service['name'] ) {
						$is_used = true;
						break;
					}
				}
				if ( !$is_used ) continue;

				// Display the CSS for this service
				$sel = '.' . $layout_class . ' li.social-' . strtolower(sanitize_title_with_dashes($service['name'])) . ' a';
				if ( $style != 'normal' ) $sel .= ':' . $style;

				$x_offset = -1 * ( $service_index * $width );
				$y_offset = -1 * ( $style_index * $height );

				printf(
					"%s { background-position: %spx %spx; }\n",
					$sel,
					(int) $x_offset,
					(int) $y_offset
				);
			}
		}
		?>
	</style>
	
	<nav class="<?php echo esc_attr(implode(' ', $classes)); ?>">
		<ul class="nav-list sliding">
			<?php
			foreach( $networks as $i => $soc ) {
				$network_name = $soc['name'] ?? $soc['service'];
				$action = $soc['action'];
				$url = $soc['url'];

				$classes = array( 'menu-item', 'menu-item-social' );
				$classes[] = 'nth-' . ($i + 1);
				if ( $i === 0 ) $classes[] = 'menu-first';
				if ( $i == (count($networks) - 1) ) $classes[] = 'menu-last';
				$classes[] = 'depth-1';
				$classes[] = 'social-' . strtolower(sanitize_title_with_dashes($network_name));

				printf(
					'<li class="%s"><a href="%s" target="_blank" rel="external" %s><span>%s</span></a></li>',
					esc_attr( implode( ' ', $classes ) ),
					esc_attr( $url ),
					$action ? 'title="'. esc_attr($action) . '"' : '',
					esc_html($network_name)
				);
			}
			?>
		</ul>
	</nav>
	<?php
}