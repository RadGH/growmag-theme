<?php
add_action( 'wp_head', 'ldad_enable_content_filter', 100 );

function ldad_enable_content_filter() {
	if ( is_admin() ) return;
	if ( !is_singular('post') ) return;

	add_filter( 'the_content', 'ldad_article_check_shortcodes', 2 );
	add_filter( 'the_content', 'ldad_article_content', 25 );

	add_action( 'wp_footer', 'ldad_disable_content_filter', 2 );
}

function ldad_disable_content_filter() {
	remove_filter( 'the_content', 'ldad_article_check_shortcodes', 2 );
	remove_filter( 'the_content', 'ldad_article_content', 25 );
}

function ldad_article_placement( $area, $change = null ) {
	static $places = null;

	if ( $places === null ) {
		$places = array(
			'before' => false,
	        'middle' => false,
	        'after' => false,
		);
	}

	if ( $change === null ) {
		// Get the value
		return $places[$area];
	}else{
		// Set the value
		$places[$area] = $change;
		return true;
	}
}

function ldad_article_check_shortcodes( $content ) {
	$length = strlen( $content );

	$auto = array(
		'before' => get_field( 'automation_post_beginning', 'options' ),
		'middle' => get_field( 'automation_post_middle', 'options' ),
		'after' => get_field( 'automation_post_after', 'options' ),
	);

	// Scan each placement to see if it is valid. If it is, remember that using ldad_article_placement
	foreach( $auto as $placement => $d ) {
		if ( !$d ) continue;
		
		$ad_location = $d[0]['location'];
		if ( !$ad_location ) continue;

		$required_words = empty($d[0]['word_count']) ? 0 : absint($d[0]['word_count']) * 5; // 5.1 letters per word on average.

		$shortcode = '[ad location="'. $ad_location .'"]';

		// Do not place this ad if the word length is shorter than the required words.
		if ( $required_words > 0 && $length < $required_words ) continue;

		// Do not place this ad if the shortcode has been placed already
		if ( strstr( $content, $shortcode ) !== false ) continue;

		// Note that this ad placement should be used.
		ldad_article_placement( $placement, true );
	}

	return $content;
}

function ldad_article_content( $content ) {
	$auto = array();

	// Only deal with replacements if they are enabled by ldad_article_check_shortcodes().
	if ( ldad_article_placement( 'before' ) ) $auto['before'] = get_field( 'automation_post_beginning', 'options' );
	if ( ldad_article_placement( 'middle' ) ) $auto['middle'] = get_field( 'automation_post_middle', 'options' );
	if ( ldad_article_placement( 'after' ) ) $auto['after'] = get_field( 'automation_post_after', 'options' );

	// Nothing to do
	if ( empty($auto) ) return $content;

	foreach( $auto as $placement => $d ) {
		$ad_location = $d[0]['location'];
		$shortcode = '[ad location="'. $ad_location .'"]';

		// We already checked if this shortcode should be added within  ldad_article_check_shortcodes. Now we just need to process them.
		if ( $placement == 'before' ) $content = ldad_article_insert_before( $content, $shortcode );
		else if ( $placement == 'middle' ) $content = ldad_article_insert_middle( $content, $shortcode );
		else if ( $placement == 'after' ) $content = ldad_article_insert_after( $content, $shortcode );
	}

	return $content;
}

function ldad_article_insert_before( $content, $shortcode ) {
	$shortcode_expanded = do_shortcode( $shortcode );
	if ( !$shortcode_expanded ) return $content;

	$content = $shortcode_expanded . "\r\n\r\n" . $content;
	return $content;
}

function ldad_article_insert_middle( $content, $shortcode ) {
	$search = "</p>";

	$split_p = explode($search, $content);

	if ( $split_p && count($split_p) > 2 ) {
		$before_ad_index = (int) floor( count($split_p) / 2 ); // If 10 paragraphs, this would be 5. Note the array index of paragraph 5 is actually #4, corrected later.

		if ( $before_ad_index < 1 ) return $content;

		$before_ad_content = $split_p[$before_ad_index - 1] . $search;

		$shortcode_expanded = do_shortcode( $shortcode );
		if ( !$shortcode_expanded ) return $content;

		$new = $before_ad_content . "\r\n" . $shortcode_expanded;

		$pos = strpos($content, $before_ad_content);
		
		if ($pos !== false) {
			$content = substr_replace($content, $new, $pos, strlen($before_ad_content));
		}
	}

	return $content;
}

function ldad_article_insert_after( $content, $shortcode ) {
	$shortcode_expanded = do_shortcode( $shortcode );
	if ( !$shortcode_expanded ) return $content;

	$content = $content . "\r\n\r\n" . $shortcode_expanded;
	return $content;
}