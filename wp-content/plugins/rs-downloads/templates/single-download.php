<?php
global $post;

get_header();

$download = new RS_Downloads_Item( get_the_ID() );
?>

<div class="inside narrow">
	<article <?php post_class( 'loop-single' ); ?>>
		
		<div class="loop-header">
			<?php the_title( '<h1 class="loop-title">', '</h1>' ); ?>
		</div>
		
		<div class="loop-body">
			
			<div class="loop-content">
				<?php
				include( RSD_PATH . '/templates/download-list-item.php' );
				?>
			</div><!-- .loop-content -->
		
		</div><!-- .loop-body -->
	
	</article>
</div>

<?php
get_footer();