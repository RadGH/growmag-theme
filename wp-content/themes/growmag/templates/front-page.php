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
</div>
<!-- #content -->

<?php
get_footer();