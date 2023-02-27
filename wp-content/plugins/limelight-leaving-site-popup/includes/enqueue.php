<?php

function ld_leavingsite_enqueue_scripts() {
	if ( ld_leavingsite_has_closed_popup() ) return;

	wp_enqueue_script( 'ouibounce', LDleavingsite_URL . '/assets/ouibounce.js', array( 'jquery' ), LDleavingsite_VERSION );

	wp_enqueue_style( 'lsp', LDleavingsite_URL . '/assets/lsp.css', array(), LDleavingsite_VERSION );
	wp_enqueue_script( 'lsp', LDleavingsite_URL . '/assets/lsp.js', array( 'jquery' ), LDleavingsite_VERSION );
}
add_action( 'wp_enqueue_scripts', 'ld_leavingsite_enqueue_scripts' );

function ld_leavingsite_enqueue_admin_scripts() {
	if ( ld_leavingsite_has_closed_popup() ) return;

	wp_enqueue_style( 'lsp-admin', LDleavingsite_URL . '/assets/lsp-admin.css', array(), LDleavingsite_VERSION );
}
add_action( 'admin_enqueue_scripts', 'ld_leavingsite_enqueue_admin_scripts' );