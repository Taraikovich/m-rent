<?php
/**
 * About: секция «Услуги по категориям» — тонкая обёртка над
 * `sections/common/services-tabbed-block`.
 *
 * Читает ACF-поля страницы:
 *   • about_services_tabs_title       — заголовок
 *   • about_services_tabs_button_text — лейбл CTA
 *   • about_services_tabs_button_url  — ссылка CTA
 *   • about_services_tabs_terms       — выбранные термы `service_category`
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();

get_template_part(
	'sections/common/services-tabbed-block',
	null,
	[
		'title'       => (string) get_field( 'about_services_tabs_title', $mrent_page_id ),
		'button_text' => (string) get_field( 'about_services_tabs_button_text', $mrent_page_id ),
		'button_url'  => (string) get_field( 'about_services_tabs_button_url', $mrent_page_id ),
		'term_ids'    => get_field( 'about_services_tabs_terms', $mrent_page_id ),
	]
);
