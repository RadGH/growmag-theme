<?php
$message = "Sorry, no posts are available.";

     if ( is_home() )     $message = "No blog posts are have been published yet.";
else if ( is_category() ) $message = "No blog posts have been published in this category.";
else if ( is_search() )   $message = "No posts match your search criteria";
else if ( is_day() )      $message = "No posts have been published on this day.";
else if ( is_month() )    $message = "No posts have been published this month.";
else if ( is_year() )     $message = "No posts have been published this year.";
?>
<article <?php post_class('loop-single loop-404 loop-404-post'); ?>>

	<div class="floating-header">
		<h1 class="title">No posts found</h1>
	</div>

	<div class="loop-body">

		<div class="loop-content">
			<?php echo wpautop( $message ); ?>
		</div><!-- .loop-content -->

		<?php get_search_form(); ?>

	</div><!-- .loop-body -->

</article>