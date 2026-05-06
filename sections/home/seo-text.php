<?php
/**
 * Главная: SEO-текстовый блок — обёртка над `sections/common/seo-text`.
 *
 * Достаёт ACF-поля `home_seo_*` и передаёт в общий шаблон.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();

get_template_part(
	'sections/common/seo-text',
	null,
	[
		'title'      => (string) get_field( 'home_seo_title', $mrent_page_id ),
		'lead'       => (string) get_field( 'home_seo_lead', $mrent_page_id ),
		'more_label' => (string) get_field( 'home_seo_more_label', $mrent_page_id ),
		'less_label' => (string) get_field( 'home_seo_less_label', $mrent_page_id ),
	]
);
