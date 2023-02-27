<?php
/*

Functions:

x	ld_custom_header_image()
		Allows custom header images to be defined, or a global header image as a fallback

	ld_allow_shortcodes_in_widgets()
		Allows shortcodes to be used in widgets

	ld_custom_wp_footer()
		Customize the footer text on the dashboard.

	ld_more_html_classes()
		Extends default classes of the <html> element, particularly adding an admin-bar class.

	ld_more_body_classes()
		Extends the default classes of the <body> element

	ld_seo_meta()
		Adds SEO relevance to the <head> by including some meta tags.

	ld_clean_header()
		Removes some useless meta stuff from the header, like emoji. Moves RSS links to the bottom of <head>

	ld_display_tracking_code_header()
	ld_display_tracking_code_body()
		Outputs tracking codes from theme settings.

	ld_clean_visitor_warnings()
		Cleans any outputted warnings/notices/etc that was output before get_header() was ran. Mainly for cleaning plugin errors.
		This is not performed for admins, or within the admin area.

	ld_custom_wp_title()
		Customizes the wp_title. This can be overriden by SEO Ultimate or the frontpage title functions below.

	ld_override_frontpage_title( $title )
	ld_override_frontpage_title_SU( $title )
	Overrides the title of the front page with the name of the website.
		The first function is for wordpress default, the second if SEO Ultimate is running.

	ld_enqueue_jquery()
		Adds jQuery to the website and admin dashboard. It usually is enqueued anyway, but this makes sure it is always available.

	ld_disable_comments()
		Disables comments by terminating any request to process a comment.

	ld_default_from_email( $email )
		Replaces the default "from" email address with the site administrator's email

	ld_default_from_email( $email )
		Replaces the default "from" sender name to the site title

	ld_disable_emoji()
	ld_disable_emoji_in_tinymce()
		Disables the new emoji functionality in WordPress 4.2
		From: https://wordpress.org/plugins/disable-emojis/

*/

function ld_custom_header_image( $image_url ) {
	if ( $image_url ) return $image_url;

	$image_id = get_field( 'header_image', get_the_ID() );

	if ( !$image_id ) $image_id = get_field( 'default_header_image', 'options' );

	return $image_id ? smart_media_size( $image_id, 'header-image' ) : false;
}

add_filter( 'theme_mod_header_image', 'ld_custom_header_image', 15 );


function ld_allow_shortcodes_in_widgets() {
	add_filter( 'widget_text', 'shortcode_unautop' );
	add_filter( 'widget_text', 'do_shortcode' );
}
add_action( 'init', 'ld_allow_shortcodes_in_widgets' );

function ld_custom_wp_footer() {
	?>
	&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> &ndash; Powered by <a href="http://zingmap.com/" target="_blank">ZingMap</a>
	<?php
	
	// Radley's account:
	if ( get_current_user_id() === 243 ) {
		global $current_screen;
		if ( $current_screen ) {
			$_current_screen = (array) $current_screen;
			
			// Hide WP stuff that isn't useful, like help tabs
			foreach( $_current_screen as $k => $v ) {
				if ( strpos( $k, 'WP_') !== false ) {
					unset($_current_screen[$k]);
					continue;
				}
			}
			
			echo '<div style="clear: left;">';
			echo '<table><tbody>';
			echo '<tr><td style="text-align: right; padding-right: 5px;">$current_screen->id:</td><td><code>'. $current_screen->id .'</code></td></tr>';
			echo '<tr><td style="text-align: right; padding-right: 5px;">$current_screen->base:</td><td><code>'. $current_screen->base .'</code></td></tr>';
			echo '<tr><td style="text-align: right; padding-right: 5px;">$current_screen->action:</td><td><code>'. $current_screen->action .'</code></td></tr>';
			echo '</tbody></table>';
			echo '</div>';
			echo '<style>#wpbody-content { padding-bottom: 120px; }</style>';
		}
	}
}
add_filter( 'admin_footer_text', 'ld_custom_wp_footer' );


function ld_more_html_classes( $classes ) {
	if ( is_admin_bar_showing() ) $classes[] = 'admin-bar';

	return $classes;
}
add_filter( 'html_class', 'ld_more_html_classes' );

function ld_more_body_classes( $classes ) {
	if ( is_front_page() ) $classes[] = 'front-page';

	// Display some classes regarding the user's role
	$user = wp_get_current_user();

	if ( $user && !empty($user->roles) ) {
		foreach( $user->roles as $role ) {
			$classes[] = 'user-role-'. $role;
		}
		$classes[] = 'logged-in';
	}else{
		$classes[] = 'user-role-none not-logged-in';
	}

	return $classes;
}
add_filter( 'body_class', 'ld_more_body_classes' );

function ld_seo_meta() {
	$description = get_bloginfo('description', 'display');

	if ( is_single() ) {
		if ( $d = get_post_meta(get_the_ID(), 'description', true) ) {
			$description = $d;
		} else if ( $d = get_the_excerpt() ) {
			$description = wp_strip_all_tags($d);
		}
	}

	if ( !$description ) {
		if ( is_front_page() ) {
			$description = get_bloginfo('description');
		}
	}

?>
<meta name="description" content="<?php echo esc_attr($description); ?>" />
<?php

	// Also display open graph tags, unless we opt to skip this section.
	// The SEO Ultimate will skip this if it is running, via plugin-seo-ultimate.php

	if ( apply_filters( 'ld_disable_open_graph', true ) == false ) return;

	$metadata = array(
		'og:description' => $description,
		'og:image' => '',

		'og:type' => 'article',
		'og:title' => '',
		'og:site_name' => '',
	);

	// Display an open graph image. Priority:
	// 1. Featured Image
	// 2. Image from post meta url or ID, "featured-image"
	// 3. First image from body content
	// 4. Fall back to theme open graph image
	// 5. Fall back to logo
	
	do {
		
		$post_id = get_the_ID();
		
		// (*) Front page uses cover image
		if ( is_front_page() ) {
			$cover_image_id = get_field( 'cover-image', null, false );
			$url = wp_get_attachment_url( $cover_image_id );
			if ( $url ) {
				$metadata['og:image'] = $url;
				break; // found image
			}
		}
		
		// 1. Get featured image url
		$url = get_the_post_thumbnail_url();
		if ( $url ) {
			$metadata['og:image'] = $url;
			break; // found image
		}
		
		// 1. Get featured image url the long way?
		/*
		$featured_image_id = get_post_thumbnail_id( $post_id );
		
		if ( $featured_image_id ) {
			$img = wp_get_attachment_image_url( $featured_image_id );
			if ( $img ) {
				// $img = "http://growmag.b-cdn.net/wp-content/uploads/IMG_7685-1-340x250.jpeg";
				$metadata['og:image'] = $img;
				break; // found image
			}
		}
		*/
		
		// 2. Image from post meta url or ID, "featured-image"
		// (there wasn't actually code for this when I reorganized on 4/21/2022 lol -radley)
		
		// 3. First image from body content
		if ( is_singular() ) {
			$img = limelight_archive_thumbnail( $post_id, 'full' );
			if ( $img ) {
				$metadata['og:image'] = $img;
				break; // found image
			}
		}
		
		// 4. Fall back to theme open graph image
		$og_image_id = (int)get_field( 'open_graph', 'options', false );
		if ( $og_image_id ) {
			$img = smart_media_size( $og_image_id, 'full' );
			$metadata['og:image'] = $img;
			break; // found image
		}
		
		// 5. Fall back to logo
		$logo_id = (int) get_field( 'logo', 'options', false );
		if ( $logo_id ) {
			$url = smart_media_size( $logo_id, 'full' );
			$metadata['og:image'] = $url;
			break; // found image
		}
		
	} while( false );
	
	$metadata['og:image'] = apply_filters( 'limelight_og_image', $metadata['og:image'] );
	
	// Other data
	$metadata['og:title'] = get_the_title();
	$metadata['og:site_name'] = get_bloginfo('title');

	// Other og tags
	unset($metadata['description']);
	foreach( $metadata as $k => $v ) {
		if ( !$v ) continue;

		echo "<meta name=\"". esc_attr($k) ."\" content=\"". esc_attr($v) ."\" />\n";
	}

}
add_action( 'wp_head', 'ld_seo_meta', 3 );


function ld_meta_tags() {
?>
<meta charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php
}
add_action( 'wp_head', 'ld_meta_tags', 28 );

function ld_clean_header() {
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	add_action( 'wp_head', 'feed_links_extra', 30 );

	remove_action( 'wp_head', 'feed_links', 2 );
	add_action( 'wp_head', 'feed_links', 30 );

	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'after_setup_theme', 'ld_clean_header' );

function ld_display_tracking_code_header() {
	echo get_field( 'tracking_head', 'options', false );

	if ( is_singular() ) {
		$codes = get_field( 'page-tracking-codes', get_the_ID(), true );
		if ( $codes && !empty($codes[0]['head']) ) echo $codes[0]['head'];
	}
}
add_action( 'wp_head', 'ld_display_tracking_code_header', 30 );

function ld_display_tracking_code_body() {
	echo get_field( 'tracking_body', 'options', false );

	if ( is_singular() ) {
		$codes = get_field( 'page-tracking-codes', get_the_ID(), true );
		if ( $codes && !empty($codes[0]['body']) ) echo $codes[0]['body'];
	}
}
add_action( 'wp_footer', 'ld_display_tracking_code_body', 30 );


function ld_clean_visitor_warnings() {
	// Administrators should see warnings
	if ( current_user_can( 'administrator' ) ) return;

	// Everyone else can have output cleared
	ob_clean();
}
add_action( 'get_header', 'ld_clean_visitor_warnings' );


function ld_custom_wp_title( $title, $sep ) {
	if ( is_feed() ) {
		return $title;
	}

	global $page, $paged, $wp_query;


	$page_count = ceil( $wp_query->found_posts / max( 1, get_query_var( 'posts_per_page' ) ) );

	$page_number = max( $page, $paged );
	if ( $page_number < 1 ) {
		$page_number = 1;
	}

	$page_range = '';
	if ( $page_count > 1 && $page_number <= $page_count ) {
		$page_range = sprintf( "%s of %s", $page_number, $page_count );
	}

	// Front Page
	if ( is_front_page() ) {
		return sprintf( '%s - %s', get_bloginfo( 'name', 'display' ), get_bloginfo( 'description', 'display' ) );
	}

    // Archive Pages
    if (is_archive()) {
        $archive_title = post_type_archive_title(null, false);

        if (!$archive_title) {
            $object = get_queried_object();

            if (isset($object->term_id)) {
                $archive_title = $object->name;
            }
        }

        if ( $page_number > 1 )
            return sprintf('%s - Page %s %s %s', $archive_title, $page_range, $sep, get_bloginfo('name', 'display'));
        else
            return sprintf('%s %s %s', $archive_title, $sep, get_bloginfo('name', 'display'));
    }

	// Search Results
	if ( is_search() ) {
		return sprintf( '%s - Page %s %s %s', 'Search Results', $page_range, $sep, get_bloginfo( 'name', 'display' ) );
	}

	// Single Page
	if ( is_singular() ) {
		return sprintf( '%s %s %s', get_the_title(), $sep, get_bloginfo( 'name', 'display' ) );
	}

	// Any other page
	$title = sprintf( '%s %s %s', $title, $sep, get_bloginfo( 'name', 'display' ) );

	// Add a page number if necessary:
	if ( $page_range && !is_404() ) {
		$title = sprintf( 'Page %s - %s', $page_range, $title );
	}

	return $title;
}

add_filter( 'wp_title', 'ld_custom_wp_title', 10, 2 );

function ld_override_frontpage_title( $title ) {
	if ( is_front_page() ) {
		$custom = get_field( 'fp_title', 'options' );

		return $custom ? $custom : get_bloginfo( 'name' );
	}

	return $title;
}

function ld_override_frontpage_title_SU( $value, $post_id, $meta_key, $single ) {
	if ( $meta_key != '_su_title' ) {
		return $value;
	}

	if ( is_front_page() ) {
		$custom = get_field( 'fp_title', 'options' );

		return $custom ? $custom : get_bloginfo( 'name' );
	}

	return $value;
}

if ( class_exists( 'SU_Titles' ) ) {
	add_filter( 'get_post_metadata', 'ld_override_frontpage_title_SU', 1000, 4 );
}else{
	add_filter( 'wp_title', 'ld_override_frontpage_title', 1000 );
}


function ld_disable_comments( $data ) {
	echo 'Comments are disabled.';
	exit;
}

//add_filter( 'preprocess_comment', 'ld_disable_comments' );


function ld_default_from_email( $email ) {
	// Get the default from address, the same code as in pluggable.php
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}

	$from_email = 'wordpress@' . $sitename;

	if ( $email === $from_email ) {
		// Replace default with admin email
		$email = get_bloginfo( 'admin_email' );
	}

	return $email;
}

function ld_default_from_name( $name ) {
	// Get the default from name, the same code as in pluggable.php
	if ( $name === 'WordPress' ) {
		// Replace default with site title
		$name = get_bloginfo( 'title' );
	}

	return $name;
}

add_filter( 'wp_mail_from', 'ld_default_from_email', 11 );
add_filter( 'wp_mail_from_name', 'ld_default_from_name', 11 );

function ld_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'ld_disable_emoji_in_tinymce' );
}
function ld_disable_emoji_in_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	}else{
		return array();
	}
}
add_action( 'init', 'ld_disable_emoji' );


// Adds a media URL column to the Media post type
function ld_media_column( $cols ) {
	// $cols["mediaurl"] = "URL";
	$cols =
		array_slice( $cols, 0, 3, true ) +
		array( 'media_url' => 'Media URL' ) +
		array_slice( $cols, 3, count($cols)-1, true );
	return $cols;
}


function ld_media_value( $column_name, $id ) {
	if ( $column_name != 'media_url' ) return;

	$meta = wp_get_attachment_metadata( $id );
	$url = smart_media_size( wp_get_attachment_url( $id ), 'full' );

	if ( !$url ) $url = wp_get_attachment_url( $id );

	if ( $url ) {
		?>
		<p><input type="text" onfocus="var $me=this;setTimeout(function(){$me.select();},60);" readonly="readonly" value="<?php echo esc_attr( $url ); ?>" class="code" style="width: 100%; box-sizing: border-box; direction: rtl;"></p>
		<?php
	}

	if ( $meta ) {
		echo '<p class="description">';

		if ( $meta['width'] && $meta['height'] ) {
			echo sprintf( 'Original Size: %s&times;%s', $meta['width'], $meta['height'] );
		}

		if ( $meta['sizes'] ) {

			$size_array = array();

			foreach( $meta['sizes'] as $size => $size_meta ) {
				$sized_url = smart_media_size( $id, $size );
				if ( $sized_url == $url ) continue;

				$size_array[] = sprintf(
					'<a href="%s" target="_blank" title="Image resolution: %sx%s">%s</a>',
					esc_attr($sized_url),
					$size_meta['width'],
					$size_meta['height'],
					esc_html($size)
				);
			}

			if ( $size_array && $meta['width'] && $meta['height'] ) echo "<br/>";

			if ( $size_array ) echo "Sizes: " . implode(', ', $size_array);
		}

		echo '</p>';
	}
}


function ld_media_hook_columns() {
	add_filter( 'manage_media_columns', 'ld_media_column' );
	add_action( 'manage_media_custom_column', 'ld_media_value', 10, 2 );
}
add_action( 'admin_init', 'ld_media_hook_columns' );