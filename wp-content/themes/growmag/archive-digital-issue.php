<?php
if ( !have_posts() ) {
	get_template_part('404');
	return;
}

global $wp_query;

get_header();

$post_type = get_post_type();

// /wp-admin/edit.php?post_type=digital-issue&page=acf-options-settings
// $cover = get_field( 'archive_cover', 'digital_issues' );
// include( __DIR__ . '/template-parts/cover.php' );
// get_template_part( 'template-parts/cover' );
?>

<h2 class="digital-issues-title">Digital Issues</h2>

<div class="digital-issues-list">
	<?php
	while( have_posts() ) : the_post();
		get_template_part( 'views/archive-digital-issue' );
	endwhile;
	?>
</div>

<?php
if ( is_archive() && $wp_query->max_num_pages > 1 ) {
	$args = apply_filters( 'archive-pagination-args', array() );
	echo '<div class="pagination clear">';
	echo paginate_links( $args );
	echo '</div>';
}

get_footer();