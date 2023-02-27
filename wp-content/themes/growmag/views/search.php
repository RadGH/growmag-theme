<article <?php post_class('loop-archive loop-search loop-search-item'); ?>>

	<div class="loop-header">
		<?php the_title( '<h2 class="loop-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
	</div>

	<div class="loop-body">

		<div class="loop-summary">
			<?php the_excerpt(); ?>
		</div><!-- .loop-summary -->

	</div><!-- .loop-body -->

</article>