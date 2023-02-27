<?php

add_filter( 'archive-pagination-args', 'ld_pagination_args' );

function ld_pagination_args( $args ) {
	$args = array(
		'end_size'           => 3,
		'mid_size'           => 3,
		'prev_next'          => True,
		'prev_text'          => '&lsaquo; Previous',
		'next_text'          => 'Next &rsaquo;',
		'type'               => 'text',
	);

	return $args;
}