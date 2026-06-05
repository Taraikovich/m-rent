<?php
/**
 * Главная: секция «Преимущества» — обёртка над `sections/common/benefits`.
 *
 * Достаёт ACF-репитер `home_benefits` и фото `home_benefits_image`, передаёт
 * в общий шаблон. Сам рендер — в `sections/common/benefits.php`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();

get_template_part(
	'sections/common/benefits',
	null,
	[
		'benefits' => (array) get_field( 'home_benefits', $mrent_page_id ),
		'image'    => get_field( 'home_benefits_image', $mrent_page_id ) ?: [],
		'title'    => (string) get_field( 'home_benefits_title', $mrent_page_id ),
	]
);
