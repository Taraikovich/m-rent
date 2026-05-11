<?php
/**
 * Cars archive: SEO-текстовый блок — обёртка над `sections/common/seo-text`.
 *
 * Поля редактируются в админке: «Автомобили → Настройки архива»
 * (ACF Options sub-page `mrent-cars-archive`).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_field' ) ) {
	return;
}

get_template_part(
	'sections/common/seo-text',
	null,
	[
		'title'      => (string) get_field( 'cars_archive_seo_title', 'option' ),
		'lead'       => (string) get_field( 'cars_archive_seo_lead', 'option' ),
		'more_label' => (string) get_field( 'cars_archive_seo_more_label', 'option' ),
		'less_label' => (string) get_field( 'cars_archive_seo_less_label', 'option' ),
	]
);
