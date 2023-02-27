<?php

// Create the post type
function ld_ad_register_post_type() {
	$labels = array(
		'name'                => 'Advertisements',
		'singular_name'       => 'Advertisement',
		'menu_name'           => apply_filters('ld_ad_menu_name', 'Advertisements'),
		'name_admin_bar'      => 'Advertisements',
		'parent_item_colon'   => 'Parent Ad:',
		'all_items'           => 'All Advertisements',
		'add_new_item'        => 'New Ad Item',
		'add_new'             => 'New Advertisement',
		'new_item'            => 'New Ad',
		'edit_item'           => 'Edit Ad',
		'update_item'         => 'Update Ad',
		'view_item'           => 'View Ad',
		'search_items'        => 'Search ads',
		'not_found'           => 'Not found',
		'not_found_in_trash'  => 'Not found in Trash',
	);

	$args = array(
		'label'               => 'Advertisement',
		'labels'              => $labels,
		'supports'            => array( 'title', 'author', 'revisions', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => '5.101',
		'menu_icon'           => 'dashicons-align-center',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => array(
			'slug' => 'ad',
		),
		'capability_type'     => 'page',
	);

	$args = apply_filters( 'ld_ad_args', $args );

	register_post_type( 'ld_ad', $args );
}
add_action( 'init', 'ld_ad_register_post_type', 0 );


// Populate the "Locations" checkbox field with locations from the theme's functions.php file.
function ld_ad_populate_locations_field_acf( $field ) {
	$field['choices'] = array();

	// Show a "- Select -" placeholder for all except the checkboxes
	if ( $field['type'] != 'checkbox' ) {
		$field['choices'][] = '&ndash; Select &ndash;';
	}

	if ($ld_ad_locations = ld_ads_get_locations()) {
		foreach( $ld_ad_locations as $key => $loc ) {
			$field['choices'][$key] = '<strong>' . $key . '</strong>';
			$height = (int) $ld_ad_locations[$key]['height'];
			$width = (int) $ld_ad_locations[$key]['width'];
			if ($height && $width ) {$field['choices'][$key] .= ' &ndash; ' . $width . '&times;' . $height . 'px';}
			if ($ld_ad_locations[$key]['desc'] && $field['type'] == 'checkbox') {$field['choices'][$key] .= ' &ndash; <em>' . $ld_ad_locations[$key]['desc'] . '</em>';}
		}
	}

	return $field;

}
add_filter('acf/load_field/key=field_5759fd27056e8', 'ld_ad_populate_locations_field_acf');
add_filter('acf/load_field/key=field_563bc7bd31dd8', 'ld_ad_populate_locations_field_acf');
add_filter('acf/load_field/key=field_56902573ff214', 'ld_ad_populate_locations_field_acf');
add_filter('acf/load_field/key=field_56902431ff20e', 'ld_ad_populate_locations_field_acf');
add_filter('acf/load_field/key=field_56902538ff211', 'ld_ad_populate_locations_field_acf');


// Create columns to preview ad, show which location they reside in, and show stats
function ld_ad_register_column( $columns ) {
	return array(
		'cb' => $columns['cb'],
		'ad-status' => '<span class="screen-reader-text">Status</span>',
		'title' => $columns['title'],
		'ad-preview' => 'Preview',
		'ad-locations' => 'Locations',
		'ad-stats' => 'Views / <abbr title="Only image ads can track clicks">Clicks</abbr>',
		'date' => $columns['date'],
	);
}
add_action( "manage_edit-ld_ad_columns", 'ld_ad_register_column' );


// Display the columns mentioned in ld_ad_register_column()
function ld_ad_display_column( $column, $post_id ) {
	switch( $column ) {
		case 'ad-preview':
			$type = get_field('ad-type', $post_id);

			if ( $type == 'image' ) {
				$image_id = get_field( 'ad-image', $post_id );
				$src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
				if ( $image_id && $src && $src[0] ) {
					echo '<a href="'. esc_attr(get_edit_post_link($post_id)) .'"><img src="'. esc_attr($src[0]) .'" style="max-width: 100%; max-height: 150px; width: auto; height: auto;" /></a>';
				}else{
					echo 'Image Ad (No image available)';
				}
			}elseif ( $type == 'external_image' ) {
				$src = get_field( 'ad-external-image', $post_id );
				if ( $src ) {
					echo '<a href="'. esc_attr(get_edit_post_link($post_id)) .'"><img src="'. esc_attr($src) .'" style="max-width: 100%; max-height: 150px; width: auto; height: auto;" /></a>';
				}else{
					echo 'External Image Ad (No image available)';
				}
			}else{
				echo esc_html(ucwords($type));
			}
			break;

		case 'ad-locations':
			$locations = get_field('ad-locations', $post_id);

			echo esc_html( implode(", ", $locations) );
			break;

		case 'ad-stats':
			ld_ads_display_stats();
			break;

		case 'ad-status':
			$validation = ld_ad_validate( $post_id );
			$status = get_post_status( $post_id );

			echo '<span class="ld-ad-status-indicator ld-post-status-', $status, ' ld-ad-status-', esc_attr($validation), '" title="', esc_attr($validation), '">';
				switch( $validation ) {
					case 'ok':
						if ( get_post_status( $post_id ) == 'publish' ) {
							echo '<span class="dashicons dashicons-yes"></span>';
						}else{
							echo '<span class="dashicons dashicons-hidden"></span>';
						}
						break;
					case 'incomplete':
						echo '<span class="dashicons dashicons-edit"></span>';
						break;
					case 'invalid':
						echo '<span class="dashicons dashicons-no-alt"></span>';
						break;
				}

				echo '<span class="screen-reader-text">', esc_html($validation), '</span>';
			echo '</span>';

			break;
	};
}
add_action( "manage_ld_ad_posts_custom_column", 'ld_ad_display_column', 10, 2 );



function ld_ads_display_stats( $post_id = null ) {
	if ( $post_id === null ) {
		global $post;
		if ( !$post ) return;

		$post_id = $post->ID;
	}

	if ( !$post_id ) return;

	$type = get_field( 'ad-type', $post_id );

	$views = (int) get_post_meta( $post_id, 'ld_ads_views', true );
	if ( $views <= 0 ) $views = 0;

	if ( $type == 'image' || $type == 'external_image' ) {
		$clicks = (int) get_post_meta( $post_id, 'ld_ads_clicks', true );
		if ( $clicks <= 0 ) $clicks = 0;
	}

	?>
	<p>
		Views: <?php echo number_format_i18n($views, 0); ?>
		<?php if ( $type == 'image' || $type == 'external_image' ) { ?><br />Clicks: <?php echo number_format_i18n($clicks, 0); ?><?php } ?>
	</p>
	<?php
}


// Saves each location/allowed post ID as a separate meta key, different than ACF's default behavior.
// This lets us use more advanced meta queries.
function ld_ad_save_locations_and_post_ids_as_separate_keys( $post_id ) {
	if ( get_post_type($post_id) !== 'ld_ad' ) return;

	// process ad locations
	delete_post_meta( $post_id, 'ld-ad-location' );
	$locations = get_field( 'ad-locations', $post_id );
	if ( $locations ) foreach( $locations as $loc ) {
		add_post_meta( $post_id, 'ld-ad-location', $loc );
	}
	
	// process allowed post ids
	delete_post_meta( $post_id, 'ld-ad-post-ids' );
	if(get_field('show_only_on_specific_postspages', $post_id)) {
		$show_on_post_ids = get_field( 'show_on_posts_pages', $post_id );
		if ( $show_on_post_ids ) {
			foreach ( $show_on_post_ids as $show_on_post_id ) {
				add_post_meta( $post_id, 'ld-ad-post-ids', $show_on_post_id );
			}
		}
	}

	ld_ad_validate( $post_id );
}
add_action( 'save_post', 'ld_ad_save_locations_and_post_ids_as_separate_keys', 30 );



// Ensures the ad is assigned to acceptable locations. Image ads cannot be combined in the same location as a script ad.
function ld_ad_validate_locations() {
	$screen = get_current_screen();

	if ( $screen->base == 'post' && $screen->id == 'ld_ad' ) {
		global $post;

		$validation = ld_ad_validate( $post->ID, true );

		if ( $validation['status'] == 'invalid' ) {
			?>
			<div class="error">
				<p><strong>This ad is INVALID</strong></p>

				<?php if ( $validation['type'] == 'embed' ) { ?>
					<p>This is a third party ad code. These ads cannot share a location with any other ad. You must move this ad, or the following ads, to a new location.</p>
				<?php }else if ( $validation['type'] == 'image' || $validation['type'] == 'external_image' ) { ?>
					<p>This is an image ad. They cannot mix with third party ad codes, which are listed below.</p>
				<?php } ?>

				<p><strong>Conflicting Ads:</strong></p>

				<ul class="ul-disc">
					<?php
					foreach( $validation['items'] as $id ) {
						printf(
							'<li><a href="%s" target="_blank">%s</a></li>',
							esc_attr( get_edit_post_link($id) ),
							esc_html( get_the_title($id) )
						);
					}
					?>
				</ul>

			</div>
			<?php
		}
		
		if ( $validation['status'] == 'incomplete' && $screen->action != "add" ) {
			?>
			<div class="error">
				<p><strong>This ad is INCOMPLETE:</strong></p>
				<p>You must supply the following information:</p>
				<ul class="ul-disc">
					<li><?php echo implode("</li><li>", $validation['items']); ?></li>
				</ul>
			</div>
			<?php
		}
	}
}
add_action( 'admin_notices', 'ld_ad_validate_locations' );


// Validates an ad.
// If it is assigned to the same location as another type of ad, it's "invalid".
// If it isn't assigned to a location or the type isn't set, it is "incomplete".
// If everything is good, it's "ok"
function ld_ad_validate( $ad_post_id, $return_detail = false ) {
	$detail = array(
		'status' => null,
		'items' => array(),
		'type' => '',
		'locations' => '',
	);

	$type = get_field( 'ad-type', $ad_post_id );
	$locations = get_field( 'ad-locations', $ad_post_id );

	$detail['type'] = $type;
	$detail['locations'] = $locations;

	// ------- Check for completeness
	// Check if ad is incomplete (missing required data)
	if ( empty($type) || empty($locations) ) {
		$detail['status'] = 'incomplete';
		if ( empty($type) ) $detail['items'][] = 'Type';
		if ( empty($locations) ) $detail['items'][] = 'Location';

		ld_ad_set_status( $ad_post_id, 'incomplete' );
		return $return_detail ? $detail : 'incomplete';
	}

	if ( $type == 'image' || $type == 'external_image' ) {
		
		// get image, depending on if it was uploaded or linked externally
		$img = ($type=='image') ? get_field( 'ad-image', $ad_post_id ) : get_field( 'ad-external-image', $ad_post_id );
		
		$url = get_field( 'ad-url', $ad_post_id );

		if ( empty($img) || empty($url) ) {
			$detail['status'] = 'incomplete';
			if ( empty($img) ) $detail['items'][] = 'Image';
			if ( empty($url) ) $detail['items'][] = 'Ad URL';

			ld_ad_set_status( $ad_post_id, 'incomplete' );
			return $return_detail ? $detail : 'incomplete';
		}
	}elseif ( $type == 'embed' ) {
		$code = get_field( 'ad-embed-code', $ad_post_id );

		if ( empty($code) ) {
			$detail['status'] = 'incomplete';
			$detail['items'][] = 'Ad Code';

			ld_ad_set_status( $ad_post_id, 'incomplete' );
			return $return_detail ? $detail : 'incomplete';
		}
	}

	// ------- Check for valid location usage
	// Image ads cannot share a location with embedded ads. If it does, the ad is considered invalid.

	// Get all ads that share the location, except itself.
	$args = array(
		'post_type' => 'ld_ad',
		'nopaging' => 1,
		'post__not_in' => array( $ad_post_id ),
		'meta_query' => array(
			array(
				'key' => 'ld-ad-location', // Note: This is different than ad-locations and refers to a single location. See function: ld_ad_save_locations_as_separate_keys
				'value' => $locations, // This is an array of locations.
				'compare' => 'IN',
			),
			array(
				'key' => 'ld-ad-status', // Only compare to ads that are set up properly
				'value' => 'ok',
			),
		),
	);

	// Allow plugins to filter these arguments
	$args = apply_filters( 'ld_ad_validate_location_args', $args, $locations );

	$ads = new WP_Query($args);

	// No competing ads?
	if ( $ads->have_posts() == false ) {
		$detail['status'] = 'ok';
		ld_ad_set_status( $ad_post_id, 'ok' );
		return $return_detail ? $detail : 'ok';
	}

	// If this is an embed ad, it cannot compete at all. Only image ads can. All ids are conflicting.
	if ( $type == 'embed' && $ads->have_posts() ) {
		$detail['status'] = 'invalid';
		foreach( $ads->posts as $p ) $detail['items'][] = $p->ID;

		ld_ad_set_status( $ad_post_id, 'invalid' );
		return $return_detail ? $detail : 'invalid';
	}

	$conflicting_ads = array();

	// this ad is not an embed, but check for embed ads that are already in this location
	foreach( $ads->posts as $p ) {
		$other_ad_type = get_field( 'ad-type', $p->ID );
		if ( $other_ad_type == 'embed' ) $conflicting_ads[] = $p->ID;
	}

	if ( empty($conflicting_ads) ) {
		$detail['status'] = 'ok';
		ld_ad_set_status( $ad_post_id, 'ok' );
		return $return_detail ? $detail : 'ok';
	}else{
		$detail['status'] = 'invalid';
		$detail['items'] = $conflicting_ads;

		ld_ad_set_status( $ad_post_id, 'invalid' );
		return $return_detail ? $detail : 'invalid';
	}
}

function ld_ad_statuses() {
	return apply_filters( 'ld_ad_statuses', array( 'ok', 'invalid', 'incomplete' ) );
}

function ld_ad_set_status( $ad_id, $status ) {
	if ( !in_array( $status, ld_ad_statuses() ) ) {
		wp_die( 'Invalud ad status:', $status );
	}

	update_post_meta( $ad_id, 'ld-ad-status', $status );
}


function ld_ad_get_status( $ad_id, $_second_attempt = null ) {
	$status = get_post_meta( $ad_id, 'ld-ad-status' );

	// No status has been set. Retrieve it then try again
	if ( !$status || !in_array( $status, ld_ad_statuses() ) ) {
		if ( $_second_attempt !== null ) return false; // Failed to get ad status after validating an ad? WEIRD!! Abort.

		ld_ad_validate( $ad_id );

		return ld_ad_get_status( $ad_id, true );
	}

	return $status;
}