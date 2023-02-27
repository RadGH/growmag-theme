<?php
function acf_fields_do_shortcode($value, $post_id, $field) {
	if ( is_admin() ) return $value;

	return do_shortcode($value);
}
add_filter('acf/load_value/type=textarea', 'acf_fields_do_shortcode', 15, 3);
add_filter('acf/load_value/type=text', 'acf_fields_do_shortcode', 15, 3);