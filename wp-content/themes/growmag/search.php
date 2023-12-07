<?php
/*
Template Name: Search
Template Description: Displays the search page
*/

global $wp_query;

$search_title = "Search";
$search_content = "Enter a search term below, then click search.";


if ( $wp_query->is_page() ) {

	// We're on a static page using the search template. Use a blank query as the search results.
	global $post;

	$search_title = $post->post_title;
	$search_content = $post->post_content;

	$page_query = $wp_query; // This query is the page query.
	$search_query = new WP_Query(); // Blank query as the search query.

}else{

	// We're on a pre-generated search archive.
	// Get the default search page if one is using the search template (this file)
	$args = array(
		'post_type' => 'page',
		'meta_query' => array(
			array(
				'key' => '_wp_page_template',
				'value' => 'search.php'
			)
		),
		'posts_per_page' => '1',
	);

	$page_query = new WP_Query( $args );

	if ( $page_query->have_posts() ) {
		$search_title = $page_query->posts[0]->post_title;
		$search_content = $page_query->posts[0]->post_content;
	}

	$search_query = $wp_query;
}

if ( $search_query->have_posts() ) {
	// The user found something in their search.
	$search_title = sprintf(
		'Found %s %s',
		$search_query->found_posts,
		($search_query->found_posts == 1) ? 'result' : 'results'
	);

	if ( !empty($_REQUEST['s']) ) {
		$search_content = sprintf(
			'You searched for "%s".',
			esc_html( stripslashes( $_REQUEST['s'] ) )
		);
	}
}else if ( isset($_REQUEST['s']) ) {
	// No results were found, and the user was searching for something.
	$search_title = 'No results found';
	$search_content = "Your search for <em>&quot;". esc_html(stripslashes($_REQUEST['s'])) ."&quot;</em> was not found.";
}


$article_id = "post-search";

if ( $page_query->have_posts() ) {
	$article_id = 'post-' . $page_query->posts[0]->ID;
}


get_header();
?>

<div class="inside narrow">

<article id="<?php echo esc_attr( $article_id ); ?>" <?php post_class('loop-single loop-search loop-search-main main-column'); ?>>

	<?php if ( $search_title ) { ?>
		<div class="floating-header">
			<h1 class="title"><?php echo esc_html( $search_title ); ?></h1>

			<?php if ( $search_content ) { ?>
				<h3 class="subtitle"><?php echo do_shortcode( $search_content ); ?></h3>
			<?php } ?>
		</div>
	<?php } ?>

	<?php get_search_form(); ?>

</article><!-- #post-## -->

<div id="sidebar">
	<?php get_sidebar(); ?>
</div>

<?php
if ( $search_query->have_posts() ) {
	?>
	<div class="search-result-container">

		<?php
		global $wp_query;
		$wp_query = $search_query;

		limelight_pagination();

		while ( $search_query->have_posts() ) : $search_query->the_post();
			
			$post_type = get_post_type();
			
			if ( $post_type == 'weekender' ) {
				// "weekender" posts should use "post" template parts
				$post_type = 'post';
			}
			
			$templates = array(
				'views/search-' . $post_type . '.php',
				'views/search.php',
				'views/archive-' . $post_type . '.php',
				'views/archive.php'
			);

			// Pick the first template from the above array and load it.
			// This allows us to fall back to an archive template if no search template is available.
			$locate_search = locate_template( $templates );

			if ( $locate_search ) include( $locate_search );

		endwhile;

		limelight_pagination(false);

		wp_reset_query();
		?>

	</div>

	<?php
}
?>
	
</div>
<!-- /.inside.narrow -->

<?php
get_footer();