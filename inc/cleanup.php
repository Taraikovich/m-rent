<?php
/**
 * Чистка <head> от служебных тегов ядра, которые теме не нужны.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function () {
	// Автоссылки на RSS-ленты (<link rel="alternate" type="application/rss+xml">).
	// feed_links_extra печатает контекстную ленту страницы безусловно (без оглядки
	// на theme support), feed_links — общую ленту сайта. Снимаем оба.
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
} );
