<div class="inside narrow">
	<article <?php post_class( 'loop-single' ); ?>>

		<div class="loop-header">
			<?php the_title( '<h1 class="loop-title">', '</h1>' ); ?>

			<?php
			// Singular pages may have a subtitle
			if ( $subtitle = get_field( 'subtitle', get_the_ID() ) ) {
				printf( '<h3 class="loop-subtitle">%s</h3>', $subtitle );
			}
			?>
		</div>

		<div class="loop-body">

			<div class="loop-content">
				<?php the_content(); ?>
			</div><!-- .loop-content -->

		</div><!-- .loop-body -->

	</article>
</div>