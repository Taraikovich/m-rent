<?php
/**
 * Главная: вторая секция «Преимущества» — обёртка над `sections/common/benefits`.
 *
 * Аналог `sections/home/benefits.php`, но с независимым набором ACF-полей
 * `home_benefits_2*` (вкладка «Преимущества 2» в группе «Главная»). Рендер —
 * общий, в `sections/common/benefits.php`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();

get_template_part(
	'sections/common/benefits',
	null,
	[
		'benefits' => (array) get_field( 'home_benefits_2', $mrent_page_id ),
		'image'    => get_field( 'home_benefits_2_image', $mrent_page_id ) ?: [],
		'title'    => (string) get_field( 'home_benefits_2_title', $mrent_page_id ),
	]
);
