<?php
/**
 * Service single: SEO-текстовый блок — обёртка над `sections/common/seo-text`.
 *
 * Достаёт ACF-поля `service_seo_*` и передаёт в общий шаблон.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_post_id = get_the_ID();

get_template_part(
	'sections/common/seo-text',
	null,
	[
		'title'      => (string) get_field( 'service_seo_title', $mrent_post_id ),
		'lead'       => (string) get_field( 'service_seo_lead', $mrent_post_id ),
		'more_label' => (string) get_field( 'service_seo_more_label', $mrent_post_id ),
		'less_label' => (string) get_field( 'service_seo_less_label', $mrent_post_id ),
	]
);
