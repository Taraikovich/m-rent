<?php
/**
 * About: секция «Преимущества» — обёртка над `sections/common/benefits`.
 *
 * Достаёт ACF-репитер `about_benefits` и фото `about_benefits_image`,
 * передаёт в общий шаблон. Сам рендер — в `sections/common/benefits.php`.
 *
 * Figma: 2169:2101.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();

get_template_part(
	'sections/common/benefits',
	null,
	[
		'benefits' => (array) get_field( 'about_benefits', $mrent_page_id ),
		'image'    => get_field( 'about_benefits_image', $mrent_page_id ) ?: [],
		'title'    => (string) get_field( 'about_benefits_title', $mrent_page_id ),
	]
);
