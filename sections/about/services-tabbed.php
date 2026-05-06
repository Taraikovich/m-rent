<?php
/**
 * About: секция «Услуги по категориям» — обёртка над `sections/common/services-tabbed`.
 *
 * Читает ACF-поля:
 *   • about_services_tabs_title       — заголовок секции
 *   • about_services_tabs_button_text — лейбл CTA («Все услуги»)
 *   • about_services_tabs_button_url  — ссылка CTA (по умолчанию архив `service`)
 *   • about_services_tabs_terms       — выбранные термы таксономии `service_category`
 *
 * Для каждого выбранного терма берёт до 4 услуг (по `menu_order` + дате),
 * формирует массив табов и передаёт в общий шаблон. Если ни в одной категории
 * нет услуг — секция не выводится (общий шаблон сам сделает return).
 *
 * Изображение карточки: каскад service_hero_image → service_card_image →
 * featured (как в `sections/home/services.php` и hero на single-странице услуги).
 *
 * Figma: 2169:2145 (desktop), 2345:3873 (mobile).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();

$mrent_title       = (string) get_field( 'about_services_tabs_title', $mrent_page_id );
$mrent_button_text = (string) get_field( 'about_services_tabs_button_text', $mrent_page_id );
if ( $mrent_button_text === '' ) {
	$mrent_button_text = __( 'Все услуги', 'm-rent' );
}
$mrent_button_url = (string) get_field( 'about_services_tabs_button_url', $mrent_page_id );
if ( $mrent_button_url === '' ) {
	$mrent_button_url = get_post_type_archive_link( 'service' ) ?: home_url( '/services/' );
}

$mrent_terms_raw = get_field( 'about_services_tabs_terms', $mrent_page_id );
$mrent_term_ids  = [];
if ( is_array( $mrent_terms_raw ) ) {
	foreach ( $mrent_terms_raw as $mrent_term_value ) {
		if ( is_object( $mrent_term_value ) && isset( $mrent_term_value->term_id ) ) {
			$mrent_term_ids[] = (int) $mrent_term_value->term_id;
		} elseif ( is_numeric( $mrent_term_value ) ) {
			$mrent_term_ids[] = (int) $mrent_term_value;
		}
	}
}

// Если ничего не выбрано — секция не выводится.
if ( ! $mrent_term_ids ) {
	return;
}

$mrent_tabs = [];
foreach ( $mrent_term_ids as $mrent_term_id ) {
	$mrent_term = get_term( $mrent_term_id, 'service_category' );
	if ( ! $mrent_term || is_wp_error( $mrent_term ) ) {
		continue;
	}

	$mrent_query = new WP_Query( [
		'post_type'      => 'service',
		'posts_per_page' => 4,
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		'no_found_rows'  => true,
		'tax_query'      => [
			[
				'taxonomy' => 'service_category',
				'field'    => 'term_id',
				'terms'    => $mrent_term_id,
			],
		],
	] );

	$mrent_services = [];
	while ( $mrent_query->have_posts() ) {
		$mrent_query->the_post();

		// Каскад изображений: hero → card → featured.
		$mrent_image_url = '';
		foreach ( [ 'service_hero_image', 'service_card_image' ] as $mrent_field ) {
			$mrent_value = get_field( $mrent_field );
			if ( is_array( $mrent_value ) && ! empty( $mrent_value['url'] ) ) {
				$mrent_image_url = $mrent_value['url'];
				break;
			}
		}
		if ( $mrent_image_url === '' ) {
			$mrent_image_url = get_the_post_thumbnail_url( get_post(), 'large' ) ?: '';
		}

		$mrent_services[] = [
			'url'       => get_permalink(),
			'title'     => get_the_title(),
			'image_url' => $mrent_image_url,
		];
	}
	wp_reset_postdata();

	if ( ! $mrent_services ) {
		continue;
	}

	$mrent_tabs[] = [
		'id'       => $mrent_term->slug,
		'name'     => $mrent_term->name,
		'services' => $mrent_services,
	];
}

get_template_part(
	'sections/common/services-tabbed',
	null,
	[
		'title'       => $mrent_title,
		'button_text' => $mrent_button_text,
		'button_url'  => $mrent_button_url,
		'tabs'        => $mrent_tabs,
	]
);
