<?php
/**
 * Клубные карты: секция «Почему выбирают нас».
 *
 * Контент:
 *   1. Если на странице заполнен ACF-репитер `club_why_us` — берём его.
 *   2. Иначе — fallback на `home_why_us` со страницы с шаблоном home-page.php.
 *
 * Это позволяет редактору либо оставить общий контент с главной (поле пустое),
 * либо задать карточки отдельно.
 *
 * Разметку рендерит общий шаблон `sections/common/why-us`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_items = (array) get_field( 'club_why_us', get_queried_object_id() );

if ( ! $mrent_items ) {
	$mrent_home_pages = get_pages( [
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'home-page.php',
		'number'     => 1,
	] );

	if ( empty( $mrent_home_pages ) ) {
		return;
	}

	$mrent_items = (array) get_field( 'home_why_us', $mrent_home_pages[0]->ID );
}

if ( ! $mrent_items ) {
	return;
}

get_template_part(
	'sections/common/why-us',
	null,
	[ 'items' => $mrent_items ]
);
