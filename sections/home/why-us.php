<?php
/**
 * Главная: секция «Почему выбирают нас» (после popular).
 *
 * Тонкая обёртка над `sections/common/why-us`: читает ACF-репитер
 * `home_why_us` (ровно 6 карточек: иконка + заголовок + описание) со страницы
 * с шаблоном home-page.php.
 *
 * Если репитер пустой — секция не выводится.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_items = (array) get_field( 'home_why_us', get_queried_object_id() );

if ( ! $mrent_items ) {
	return;
}

get_template_part(
	'sections/common/why-us',
	null,
	[ 'items' => $mrent_items ]
);
