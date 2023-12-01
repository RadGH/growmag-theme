<?php
/*
Template Name: Front Page
*/

get_header();
?>

<div id="content">
	<div class="inside">
	    <?php get_template_part( 'template-parts/coverstories', 'front-page' ); ?>
	    <?php get_template_part( 'template-parts/dept-posts', 'front-page' ); ?>
	</div>
		
		<?php
		if ( get_the_content() ) {
			?>
			<div class="inside narrow">
				<article class="main-column">
					<div class="loop-content">
						<?php the_content(); ?>
					</div><!-- .loop-content -->
				</article><!-- .main-column -->
			</div><!-- .inside.narrow -->
			<?php
		}
		?>
</div>
<!-- #content -->

<?php
get_footer();