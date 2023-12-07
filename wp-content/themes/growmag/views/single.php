<div class="inside narrow">
	<article <?php post_class( 'loop-single' ); ?>>

		<div class="floating-header">
			<?php the_title( '<h1 class="title">', '</h1>' ); ?>
		</div>

		<div class="loop-body">

			<div class="loop-content">
				<?php the_content(); ?>
			</div><!-- .loop-content -->

		</div><!-- .loop-body -->

	</article>
</div>