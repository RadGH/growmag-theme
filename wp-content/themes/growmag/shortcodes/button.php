<?php

function shortcode_button_element( $atts, $content = '' ) {

    $a = shortcode_atts( array(
        'url' => '#null',
        'target' => '_self',
        'classes' => ''
    ), $atts );


	return '<a href="' . $a['url'] . '" target="' . $a['target'] . '" class="button ' . $a['classes'] . '">' . $content . "</a>";
}
add_shortcode( 'button', 'shortcode_button_element' );