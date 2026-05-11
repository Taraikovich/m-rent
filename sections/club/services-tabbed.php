<?php
/**
 * Club: секция «Услуги по категориям» — тонкая обёртка над
 * `sections/common/services-tabbed-block`.
 *
 * Читает ACF-поля страницы:
 *   • club_services_tabs_title       — заголовок
 *   • club_services_tabs_button_text — лейбл CTA
 *   • club_services_tabs_button_url  — ссылка CTA
 *   • club_services_tabs_terms       — выбранные термы `service_category`
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_field' ) ) {
	return;
}

$mrent_page_id = get_queried_object_id();

get_template_part(
	'sections/common/services-tabbed-block',
	null,
	[
		'title'       => (string) get_field( 'club_services_tabs_title', $mrent_page_id ),
		'button_text' => (string) get_field( 'club_services_tabs_button_text', $mrent_page_id ),
		'button_url'  => (string) get_field( 'club_services_tabs_button_url', $mrent_page_id ),
		'term_ids'    => get_field( 'club_services_tabs_terms', $mrent_page_id ),
	]
);
