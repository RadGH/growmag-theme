<?php
// this file increments views and clicks using ajax
// does not load all of wordpress for to make faster and more efficiency
// $post_ids is an array of ad IDs to increment
// $action is either track_view or track_click

define( 'SHORTINIT', true );

// load minimal wordpress
$wp_load = $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
if ( !file_exists( $wp_load ) ) $wp_load = $_SERVER['DOCUMENT_ROOT'] . '/.wordpress/wp-load.php';

if ( !file_exists( $wp_load ) ) {
	exit( "wp_load.php not found." );
}else{
	require_once( $wp_load );
}

// valid post ID?
$post_ids = (array)$_POST['post_ids'];
if ( !$post_ids ) {
	exit( "invalid post ID" );
}

// valid action?
$action = isset( $_POST['ldad'] ) ? stripslashes( $_POST['ldad'] ) : null;
if ( $action == 'track_view' ) {
	ld_ads_track_stats( $post_ids, 'views' );
}elseif ( $action == 'track_click' ){
	ld_ads_track_stats( $post_ids, 'clicks' );
}else{
	exit( "invalid action" );
}

exit( "success" );


/*******************************************
 * FUNCTION DEFS
 * (does not rely on advanced WP functions)
 *******************************************/


// records a view or a click on an ad
function ld_ads_track_stats( $post_ids, $stat ) {
	$meta_name = 'ld_ads_' . $stat;
	foreach ( $post_ids as $post_id ) {
		$post_id = (int)$post_id;
		if ( !$post_id ) {
			continue;
		}
		$count = ld_ads_get_post_meta( $post_id, $meta_name );
		if ( is_null( $count ) ) { // insert new row
			ld_ads_insert_post_meta( $post_id, $meta_name, 1 );
		}else{ // update existing row
			if ( $count < 1 ) {
				$count = 0;
			}
			$count++;
			ld_ads_update_post_meta( $post_id, $meta_name, $count );
		}
	}
}


// returns existing meta value, or NULL if not found
function ld_ads_get_post_meta( $post_id, $meta_key ) {
	global $wpdb;
	$sql = <<<SQL
SELECT meta_value
FROM {$wpdb->postmeta}
WHERE post_id = %d
AND meta_key = %s
SQL;
	$query = $wpdb->prepare( $sql, $post_id, $meta_key );
	$meta_val = $wpdb->get_var( $query );

	return $meta_val;
}

// creates new meta value
function ld_ads_insert_post_meta( $post_id, $meta_key, $meta_val ) {
	global $wpdb;

	// Verify the ad exists
	$sql = <<<SQL
SELECT ID
FROM {$wpdb->posts}
WHERE post_type = "ld_ad" AND post_status = "publish" AND ID = %d
LIMIT 1;
SQL;
	$query = $wpdb->prepare($sql, $post_id);

	if ( !$wpdb->get_var($query) ) {
		echo 'post id does not correspond to an ad, or ad invalid.';
		exit;
	}

	$sql = <<<SQL
INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES (%d, %s, %d);
SQL;
	$query = $wpdb->prepare( $sql, $post_id, $meta_key, $meta_val, $post_id );
	$wpdb->query( $query );
}

// updates existing meta value
function ld_ads_update_post_meta( $post_id, $meta_key, $meta_val ) {
	global $wpdb;
	$sql = <<<SQL
UPDATE {$wpdb->postmeta} 
SET meta_value = %d
WHERE (
	post_id = %d
	AND
	meta_key = %s
	AND
	"ld_ad" = (SELECT p.post_type FROM {$wpdb->posts} p WHERE p.ID = %d AND p.post_status = "publish" LIMIT 1)
)
SQL;
	$query = $wpdb->prepare( $sql, $meta_val, $post_id, $meta_key, $post_id );
	$wpdb->query( $query );
}