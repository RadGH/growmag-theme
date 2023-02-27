<?php
/*
Plugin Name: Limelight - Require a Category
Version: 1.0
Plugin URI: http://www.limelightdept.com/
Description: Displays an error and prevents a post from being published if a category is not set. "Uncategorized" is also treated as if no category is set.
Author: Radley Sustaire
Author URI: mailto:radleygh@gmail.com

Copyright 2015 Limelight Department (radley@limelightdept.com)
For use by Limelight Department and affiliates, do not distribute
*/

if( !defined( 'ABSPATH' ) ) exit;

define( 'LDRC_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'LDRC_PATH', dirname(__FILE__) );

add_action( 'admin_notices', 'ldrc_require_category_notice' );
add_action( 'save_post', 'ldrc_require_category_unpublish', 80 );

function ldrc_require_category_notice() {
	$screen = get_current_screen();

	if ( $screen->id == 'post' ) {
		$post_id = false;
		if ( isset($_REQUEST['post']) ) $post_id = (int) $_REQUEST['post'];
		if ( !$post_id ) $post_id = get_the_ID();

		// Must be an existing, previously saved post
		if ( !$post_id ) return;
		if ( get_post_status($post_id) == 'auto-draft' ) return;
		if ( get_post_type($post_id) != 'post' ) return;

		if ( !ldrc_has_real_category($post_id) ) {
			$message = 'Exactly one category is required.';

			if ( get_post_status($post_id) !== 'publish' && get_post_status($post_id) !== 'future' ) {
				$message .= ' Your post cannot be published until you have selected only one category!';
			}
			?>
			<div class="error">
				<p><strong>Category is required:</strong></p>
				<p><?php echo $message; ?></p>
			</div>
			<?php
		}
	}
}

function ldrc_require_category_unpublish( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if ( get_post_type($post_id) != 'post' ) return;

	if ( !ldrc_has_real_category($post_id) ) {
		$status = get_post_status($post_id);

		if ( in_array($status, array('publish', 'future')) ) {
			$args = array(
				'ID' => $post_id,
				'post_status' => 'draft',
			);

			remove_action('save_post', 'ldrc_require_category_unpublish');
			wp_update_post( $args );
			add_action('save_post', 'ldrc_require_category_unpublish');
		}
	}
}

function ldrc_has_real_category( $post_id = null, $only_one = true ) {
	if ( $post_id === null ) $post_id = get_the_ID();

	$categories = get_the_category($post_id);

	if ( $categories ) foreach( $categories as $term ) {
		if ( $term->name != 'Uncategorized' ) {
			return true;
		}
	}

	return false;
}