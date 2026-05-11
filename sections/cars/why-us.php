<?php
/**
 * Карточка авто: секция «Почему выбирают нас» (после services-tabbed).
 *
 * Тонкая обёртка над `sections/common/why-us`: читает ACF-репитер
 * `car_why_us` с текущего поста авто. Структура подполей идентична
 * `home_why_us` (`why_us_icon` / `why_us_title` / `why_us_description`).
 *
 * Если репитер пустой — секция не выводится.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_items = (array) get_field( 'car_why_us', get_queried_object_id() );

if ( ! $mrent_items ) {
	return;
}

get_template_part(
	'sections/common/why-us',
	null,
	[ 'items' => $mrent_items ]
);
