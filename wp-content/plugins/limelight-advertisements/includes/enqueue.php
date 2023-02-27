<?php

function ld_ads_enqueue() {
	wp_enqueue_style( 'limelight-ads', LDAds_URL . '/assets/limelight-ads.css', array(), LDAds_VERSION );
	wp_register_script( 'limelight-ads', LDAds_URL . '/assets/limelight-ads.js', array( 'jquery' ), filemtime(LDAds_PATH."/assets/limelight-ads.js") );
        $url = parse_url( LDAds_URL );
        wp_localize_script( 'limelight-ads', 'ad_ajax_url', array(
            "ajax_url"=>$url['path']."/includes/ajax.php"
        ));
        wp_enqueue_script('limelight-ads');
}
add_action( 'wp_enqueue_scripts', 'ld_ads_enqueue' );

function ld_ads_enqueue_admin() {
	wp_enqueue_style( 'limelight-ads-admin', LDAds_URL . '/assets/limelight-ads-admin.css', array(), LDAds_VERSION );
}
add_action( 'admin_enqueue_scripts', 'ld_ads_enqueue_admin' );