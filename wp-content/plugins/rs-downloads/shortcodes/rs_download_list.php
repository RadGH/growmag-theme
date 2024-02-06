<?php

function shortcode_rs_download_list( $atts, $content = '', $shortcode_name = 'rs_download_list' ) {
	$atts = shortcode_atts(array(
		// Display options
		'layout'          => 'list',
		'display_images'  => '1',
		'display_content' => '1',
		'display_button' => '1',
		
		// Query options
		'limit'    => 20,
		'orderby'  => 'date',
		'order'    => 'DESC',
		'ids'      => '',
		
		// Other options
		'no_results_message' => '<em>No downloads found.</em>',
	), $atts, $shortcode_name );
	
	// Enqueue download list assets
	RS_Downloads()->Enqueue->enqueue_public_scripts();
	
	$is_true = function( $value ) {
		return in_array( $value, array( 'true', '1', true, 1 ), true );
	};
	
	// Prepare display options
	$layout = $atts['layout'] === 'grid' ? 'grid' : 'list';
	$display_images  = $is_true( $atts['display_images'] );
	$display_content = $is_true( $atts['display_content'] );
	$display_button  = $is_true( $atts['display_button'] );
	
	// Prepare query
	$query_args = array(
		'post_type' => 'rs_download',
		'posts_per_page' => $atts['limit'],
		'orderby' => $atts['orderby'],
		'order' => $atts['order'],
	);
	
	if ( $atts['ids'] ) {
		$query_args['post__in'] = explode(',', $atts['ids']);
	}
	
	$query = new WP_Query( $query_args );
	
	if ( ! $query->have_posts() ) {
		return $atts['no_results_message'] ? wpautop($atts['no_results_message']) : '';
	}

	// Classes
	$classes = array();
	$classes[] = 'rs-downloads-list';
	$classes[] = 'layout-' . $layout;
	$classes[] = $display_images ? 'show-images' : 'hide-images';
	$classes[] = $display_content ? 'show-content' : 'hide-content';
	$classes[] = $display_button ? 'show-buttons' : 'hide-buttons';
	
	ob_start();
	?>
	
	<?php
	// Display any messages, such as errors from an existing download attempt, or confirmation messages
	RS_Downloads()->Post_Type->display_messages();
	?>
	
	<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
		
		<?php
		
		// Display the download list
		foreach( $query->posts as $post ) {
			$download = new RS_Downloads_Item( $post->ID );
			
			include( RSD_PATH . '/templates/download-list-item.php' );
		}
		
		?>
		
	</div>
	<?php
	
	return trim(ob_get_clean());
}
add_shortcode( 'rs_download_list', 'shortcode_rs_download_list' );