<?php
// init array used for ads rendered later
function ld_ads_print_scripts() {
	$url = parse_url( LDAds_URL );
	?>
	<script type="text/javascript">
		var ld_ads_markup = [];
		//var ad_ajax_url = '<?php echo $url['path']; ?>/includes/ajax.php';
	</script>
	<?php
}

add_action( 'wp_print_scripts', 'ld_ads_print_scripts' );


// Display an ad in an ad location using a shortcode
function ld_ad_shortcode( $atts, $content = '' ) {
	
	// variable to assign unique ID to each ad slot on the page
	static $js_count = - 1;
	
	// Location shortcode attribute is required
	$location = ! empty( $atts['location'] ) ? $atts['location'] : false;
	if ( empty( $location ) ) {
		return '<!-- Invalid ad location specified, use [ad location="Your Location"] -->';
	}
	
	// If ads are disabled, show admins what ad should go there normally. Users don't see anything.
	if ( get_field( 'ld_ads_disable', 'options' ) ) {
		if ( current_user_can( 'administrator' ) ) {
			$classes   = ld_ad_get_wrap_classes( $location );
			$classes[] = 'ld-ad-type-embed';
			
			ob_start();
			?>
			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
				<div class="ld-ad-inner">
					<div style="background: #444; color: #f0f0f0; font-family: 'Source Code Pro', monospace; font-size: 12px; line-height: 18px;">
						Location: <?php echo esc_html( $location ); ?></div>
					<span class="ad-text">Debugging Ads</span>
				</div>
			</div>
			<?php
			return ob_get_clean();
		} else {
			return '';
		}
	}
	
	// Get all ads in this location
	$args = array(
		'post_type'      => 'ld_ad',
		'posts_per_page' => - 1,
		'meta_query'     => array(
			array(
				// ad must be valid
				'key'   => 'ld-ad-status',
				'value' => 'ok',
			),
			array(
				// ad must have this location
				'key'   => 'ld-ad-location', // Note: This is different than ad-locations. See function: ld_ad_save_locations_and_post_ids_as_separate_keys
				'value' => $location,
			),
			array(
				// check first for ads restricted to this page
				'key'   => 'ld-ad-post-ids', // Note: This is different than show_on_posts_pages. See function: ld_ad_save_locations_and_post_ids_as_separate_keys
				'value' => get_the_ID(),
			),
		),
	);
	
	// Allow plugins to filter these arguments
	$args = apply_filters( 'ld_ad_display_args', $args, $location );
	
	$ads = new WP_Query( $args );
	
	if ( $ads->found_posts == 0 ) {
		// no post-specific ads found, try again looking for generic ads
		$args['meta_query'] = array(
			array(
				// ad must be valid
				'key'   => 'ld-ad-status',
				'value' => 'ok',
			),
			array(
				// ad must have this location
				'key'   => 'ld-ad-location', // Note: This is different than ad-locations. See function: ld_ad_save_locations_and_post_ids_as_separate_keys
				'value' => $location,
			),
			array(
				// since there are no ads specific to this page, grab any valid ads in this location
				'relation' => 'OR',
				array(
					'key'     => 'ld-ad-post-ids',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'ld-ad-post-ids',
					'compare' => '',
				),
			),
		);
		
		// Allow plugins to filter these arguments
		$args = apply_filters( 'ld_ad_display_args', $args, $location );
		
		$ads = new WP_Query( $args );
	}
	
	
	$js_count ++;
	
	if ( $ads->found_posts == 1 ) {
		// load directly if only one ad in this location
		$post_id = $ads->posts[0]->ID;
		ob_start();
		?>
		<!-- LDA: Single Ad [#<?php echo esc_attr( $post_id ); ?>] -->
		<div class="ld-ad-hardcode" id="ld-insert-<?php echo $js_count; ?>" data-ld-id="<?php echo $post_id; ?>"><?php echo ld_ad_get_actual_markup( $post_id, $location ); ?></div>
		<?php
		return ob_get_clean();
	} elseif ( $ads->found_posts > 1 ) {
		// this location has more than one ad (thus these must be image ads)
		// load their markup and pass it to js so we can use js to pick one and output
		$markup = array();
		$posts  = $ads->get_posts();
		foreach ( $posts as $post ) {
			$markup[] = array(
				$post->ID,
				ld_ad_get_actual_markup( $post->ID, $location ),
			); // get markup
		}
		
		//print("<pre>".print_r($markup, true)."</pre>");
		ob_start();
		?>
		<!-- LDA: Image Ads JS -->
		<div class="ld-ad-insert" id="ld-insert-<?php echo $js_count; ?>"></div>
		<script type="text/javascript"> ld_ads_markup[<?php echo $js_count; ?>] = <?php echo json_encode( $markup ); ?>; </script>
		<?php
		return ob_get_clean();
	}
	
	return '<!-- LDA: Ad position has no results to display -->';
}

add_shortcode( 'ad', 'ld_ad_shortcode' );