<?php

function ld_ad_get_actual_markup( $ad_id, $location ) {
	static $ld_displayed_ads = null;
	if ( $ld_displayed_ads === null ) {
		$ld_displayed_ads = array();
	}

	$type = get_field( 'ad-type', $ad_id );
	$code = false;

	if ( $type == 'embed' ) {
		$code = get_field( 'ad-embed-code', $ad_id );
	}elseif ( $type == 'image' ){
		$url = get_field( 'ad-url', $ad_id );
		$image = get_field( 'ad-image', $ad_id );
		if ( $img_src = wp_get_attachment_image_src( $image, 'full' ) ) {
			$code = sprintf( '<a href="%s" target="_blank" rel="external nofollow" class="ldad-external-link"><img src="%s" width="%s" height="%s" alt="%s" /></a>', esc_attr( $url ), esc_attr( $img_src[0] ), esc_attr( $img_src[1] ), esc_attr( $img_src[2] ), esc_attr( get_the_title( $ad_id ) ) );

			if ( $location ) {
				$ld_displayed_ads[] = $ad_id;
			}
		}
	}elseif ( $type == 'external_image' ){
		$url = get_field( 'ad-url', $ad_id );
		if ( $img_src = get_field( 'ad-external-image', $ad_id ) ) {
			if ( $ld_ad_locations = ld_ads_get_locations() ) {
				if ( $ld_ad_locations[$location]['width'] && $ld_ad_locations[$location]['height'] ) {
					$code = sprintf( '<a href="%s" target="_blank" rel="external nofollow" class="ldad-external-link"><img src="%s" width="%s" height="%s" alt="%s" /></a>', esc_attr( $url ), esc_attr( $img_src ), esc_attr( $ld_ad_locations[$location]['width'] ), esc_attr( $ld_ad_locations[$location]['height'] ), esc_attr( get_the_title( $ad_id ) ) );
					if ( $location ) {
						$ld_displayed_ads[] = $ad_id;
					}
				}
			}
		}
	}

	if ( !$code ) {
		return false;
	}

	$classes = ld_ad_get_wrap_classes( $location );

	$classes[] = 'ld-ad-type-' . $type;

	return sprintf( '<div class="%s"><div class="ld-ad-inner">%s<span class="ad-text">Advertisement</span></div></div>', esc_attr( implode( ' ', $classes ) ), $code );
}

function ld_ad_get_wrap_classes( $location ) {

	$classes = array( 'ld-ad' );
	$classes[] = 'ld-ad-location-' . str_replace( '-', '_', sanitize_title_with_dashes( $location ) );

	// add classes depending on whether the given location supports desktop and/or mobile
	if ( $all_locations = ld_ads_get_locations() ) {
		if ( $loc = $all_locations[$location] ) {
			if ( $loc['desktop'] !== false ) {
				$classes[] = 'ld-ad-desktop';
			}
			if ( $loc['mobile'] !== false ) {
				$classes[] = 'ld-ad-mobile';
			}
		}
	}

	return $classes;
}

// records a view or a click on an ad
function ld_ads_track_stats( $post_id, $stat ) {
	$meta_name = 'ld_ads_' . $stat;
	$count = get_post_meta( $post_id, $meta_name, true );
	if ( !$count || $count < 1 ) {
		$count = 0;
	}
	$count++;
	update_post_meta( $post_id, $meta_name, $count );
}


function ld_ads_redirect_ad_to_destination() {
	if ( get_post_type() && is_singular( 'ld_ad' ) ) {
		add_filter( 'do_rocket_generate_caching_files', '__return_false' ); // prevent from caching, probably not necessary tho lol
		$destination = get_field( 'ad-url' );

		if ( $destination ) {
			ld_ads_track_stats( get_the_ID(), 'clicks' );
			wp_redirect( $destination );
		}else{
			if ( current_user_can( 'manage_options' ) ) {
				ob_start();
				?>
				<h2>Advertisement Redirection Error</h2>
				<p>The advertisement URL for this popup ad has not been configured. Please enter a URL for the ad.</p>
				<p>
					<a href="<?php echo esc_attr( get_edit_post_link( get_the_ID() ) ); ?>" target="_blank" rel="external">Edit the Ad</a>
				</p>
				<p>Return to <a href="<?php echo esc_attr( site_url() ); ?>">&laquo; Website</a> or
					<a href="<?php echo esc_attr( admin_url( 'edit.php?post_type=ld_ad' ) ); ?>">&laquo; Dashboard</a>
				</p>
				<?php
				wp_die( ob_get_clean() );
			}else{
				ob_start();
				?>
				<h2>Advertisement Error</h2>
				<p>We're sorry, the destination for this advertisement appears to be invalid. Please try again later.</p>
				<p>Return to
					<a href="<?php echo esc_attr( site_url() ); ?>">&laquo; Return to <?php bloginfo( 'name' ); ?></a>
				</p>
				<?php
				wp_die( ob_get_clean() );
			}
		}
		exit;
	}
}

add_action( 'wp', 'ld_ads_redirect_ad_to_destination' );

function ld_ads_stats_metabox() {
	add_meta_box( 'ld_ads_popup_ad_stats', 'Ad Statistics', 'ld_ads_stats_metabox_display', 'ld_ad', 'side' );
}

add_action( 'add_meta_boxes', 'ld_ads_stats_metabox', 2 );

function ld_ads_stats_metabox_display() {
	global $post;
	ld_ads_display_stats();

	$time = ld_ads_get_last_stat_time();
	if ( $time ) {
		?>
		<p>Since <?php echo date( get_option( 'date_format' ), $time ); ?></p>
		<?php
	}

	$nonce = wp_create_nonce( 'clear-ad-stats' );
	$url = add_query_arg( array( 'ldad' => $nonce ), get_edit_post_link( $post->ID ) );
	?>
	<p>
		<a href="<?php echo esc_attr( $url ); ?>" class="button button-secondary" onclick="if ( !confirm('This will reset the views and clicks recorded for this advertisement. Any changes to this post will NOT be saved. Continue?') ) return false;">Reset Statistics</a>
	</p>
	<?php
}

function ld_ads_reset_stats_query() {
	global $post;

	if ( isset( $_REQUEST['ldad'] ) && isset( $post ) && $post->ID ) {
		$nonce = $_REQUEST['ldad'];

		if ( wp_verify_nonce( $nonce, 'clear-ad-stats' ) ) {
			if ( ld_ads_reset_stats( $post->ID ) ) {
				?>
				<div class="updated">
					<p><strong>Limelight Advertisements:</strong> Ad statistics have been cleared for this ad.</p>
				</div>
				<?php
			}
		}
	}
}

add_action( 'admin_notices', 'ld_ads_reset_stats_query' );

// gets the last time the ad stats were cleared (or the time the ad was added)
function ld_ads_get_last_stat_time( $post_id = null ) {
	if ( $post_id === null ) {
		$post_id = get_the_ID();
	}

	if ( !$post_id ) {
		return false;
	}

	$since = get_post_meta( $post_id, 'ld_ads_stat_time', true );

	if ( $since ) {
		return $since;
	}else{
		return get_post_time( 'U', true, $post_id );
	}
}

// saves the last time the ad stats were cleared (or the time the ad was added)
function ld_ads_refresh_stat_time( $post_id = null ) {
	if ( $post_id === null ) {
		$post_id = get_the_ID();
	}

	if ( $post_id ) {
		update_post_meta( $post_id, 'ld_ads_stat_time', time() );
	}
}

function ld_ads_reset_stats( $post_id = null ) {
	if ( $post_id === null ) {
		$post_id = get_the_ID();
	}

	if ( $post_id ) {
		ld_ads_refresh_stat_time( $post_id );
		update_post_meta( $post_id, 'ld_ads_views', 0 );
		update_post_meta( $post_id, 'ld_ads_clicks', 0 );

		return true;
	}

	return false;
}

// Disabled ad warning
function ld_ads_disable_warning() {
	if ( get_field( 'ld_ads_disable', 'options' ) ) {
		?>
		<div class="error">
			<p><strong>Limelight - Advertisements:</strong> Warning</p>
			<p>Advertisements have been disabled temporarily. To re-enable advertisements, go to
				<a href="<?php echo esc_attr( admin_url( 'admin.php?page=ld-ad-settings' ) ); ?>">Advertisements &gt;
				                                                                                  Settings</a> and find
			   the Debugging section.</p>
		</div>
		<?php
	}
}

add_action( 'admin_notices', 'ld_ads_disable_warning' );


// Add unpublish field to the Publish box on ld_ad post type
function ldad_display_unpublish_datepicker() {
	global $post;
	if ( empty( $post->ID ) || get_post_type( $post ) != 'ld_ad' ) {
		return;
	}
	$ldad_unpublish = get_post_meta( $post->ID, 'ldad_unpublish', true );
	if ( $ldad_unpublish ) {
		date_default_timezone_set( get_option( 'timezone_string' ) );
		$ldad_unpublish = date( 'M d, Y', $ldad_unpublish );
	}
	?>
	<div id="ldad-unpublish" style="padding: 0 0 10px 36px"><label>Unpublish on:
			<input name="ldad_unpublish" value="<?php echo $ldad_unpublish; ?>" type="text" style="width: 110px; font-size: 13px; font-weight: bold" /></label>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery('#ldad-unpublish').find('input').datepicker({
				dateFormat: 'M d, yy'
			});
			jQuery('.ui-datepicker').wrap('<div class="acf-ui-datepicker"></div>');
		});
	</script>
	<?php
}

add_action( 'post_submitbox_misc_actions', 'ldad_display_unpublish_datepicker', 3 );

function ldad_save_unpublish_meta( $post_id ) {
	if ( get_post_type( $post_id ) == 'ld_ad' ) {

		// clear existing unpublish stuff
		wp_unschedule_event( get_post_meta( $post_id, 'ldad_unpublish', true ), 'ldad_unpublish_ad', array( $post_id ) );
		delete_post_meta( $post_id, 'ldad_unpublish' );

		if ( !empty( $_REQUEST['ldad_unpublish'] ) ) {
			date_default_timezone_set( get_option( 'timezone_string' ) );
			$ldad_unpublish = strtotime( $_REQUEST['ldad_unpublish'] );
			if ( $ldad_unpublish < time() ) {
				// unpublish already happened; delete post now and redirect to ad page
				wp_trash_post( $post_id );
				wp_redirect( admin_url( 'edit.php?post_type=ld_ad' ) );
				exit;
			}else{
				// schedule unpublish
				wp_schedule_single_event( $ldad_unpublish, 'ldad_unpublish_ad', array( $post_id ) );
				update_post_meta( $post_id, 'ldad_unpublish', $ldad_unpublish );
			}
		}
	}
}

add_action( 'save_post', 'ldad_save_unpublish_meta' );


// action hook to unpublish/trash an ad
function ldad_unpublish_ad_fun( $post_id ) {
	wp_trash_post( $post_id );
	delete_post_meta( $post_id, 'ldad_unpublish' );
}

add_action( 'ldad_unpublish_ad', 'ldad_unpublish_ad_fun', 10, 1 );