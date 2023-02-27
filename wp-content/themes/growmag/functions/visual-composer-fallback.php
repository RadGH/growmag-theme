<?php

function gm_vc_blank_shortcode( $atts, $content = '' ) {
	return do_shortcode($content);
}
add_shortcode('vc_row', 'gm_vc_blank_shortcode');
add_shortcode('vc_column', 'gm_vc_blank_shortcode');
add_shortcode('vc_column_text', 'gm_vc_blank_shortcode');