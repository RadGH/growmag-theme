<div class="inside narrow">
	<article <?php post_class( 'loop-single' ); ?>>

		<div class="loop-header">
			<?php the_title( '<h1 class="loop-title">', '</h1>' ); ?>
		</div>

		<div class="loop-body">

			<div class="loop-content">
				<?php the_content(); ?>
			</div><!-- .loop-content -->

		</div><!-- .loop-body -->

	</article>
</div>