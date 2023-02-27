<?php
/*
SHORTCODE  OUTPUT
[copy]     ©
[reg]      ®
[trade]    ™
[year]     2015 (current year)
*/
function shortcode_copyright_copy_symbol( $atts, $content = '' ) {
	return '&copy;';
}
add_shortcode( 'copy', 'shortcode_copyright_copy_symbol' );

function shortcode_copyright_reg_symbol( $atts, $content = '' ) {
	return '&reg;';
}
add_shortcode( 'reg', 'shortcode_copyright_reg_symbol' );

function shortcode_copyright_trade_symbol( $atts, $content = '' ) {
	return '&trade;';
}
add_shortcode( 'tm', 'shortcode_copyright_trade_symbol' );

function shortcode_copyright_year( $atts, $content = '' ) {
	return date('Y');
}
add_shortcode( 'year', 'shortcode_copyright_year' );