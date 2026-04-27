<?php
/**
 * m-rent theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/vite.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

	register_nav_menus( [
		'primary' => __( 'Primary Menu', 'm-rent' ),
	] );
} );

add_action( 'wp_enqueue_scripts', function () {
	mrent_vite_enqueue( 'main.js' );
} );
