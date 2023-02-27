<?php
global $wp_query;

get_header();

$post_type = get_post_type();

if ( $post_type == 'weekender' ) {
	// "weekender" posts should use "post" template parts
	$post_type = 'post';
}

if ( have_posts() ) {
	
	$firstpost = true;
	
	while( have_posts() ) : the_post();
		
		if ( is_singular() ) {
			get_template_part( 'views/single', $post_type );
		} else {
			if ( $firstpost ) {
				get_template_part( 'views/archive-first', $post_type );
				echo '<div class="inside narrow">';
			} else {
				get_template_part( 'views/archive', $post_type );
			}
		}
		
		$firstpost = false;
	
	endwhile;
	
	if ( is_archive() && $wp_query->max_num_pages > 1 ) {
		$args = apply_filters( 'archive-pagination-args', array() );
		echo '<div class="pagination clear">';
		echo paginate_links( $args );
		echo '</div>';
	}
	
	if ( ! is_singular() ) {
		echo '</div> <!-- /.inside.narrow -->';
	}
	
} else {
	get_template_part( 'views/404', $post_type );
}

get_footer();