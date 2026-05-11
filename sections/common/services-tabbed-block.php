<?php
/**
 * Общий блок «Услуги по категориям» (загрузка данных + рендер).
 *
 * Тонкие обёртки на страницах (`sections/{page}/services-tabbed.php`) читают
 * ACF — заголовок, кнопку и список term_id таксономии `service_category` —
 * и передают сюда:
 *
 *     get_template_part( 'sections/common/services-tabbed-block', null, [
 *         'title'       => 'Аренда авто под ваш запрос',
 *         'button_text' => 'Все услуги',
 *         'button_url'  => '/services/',
 *         'term_ids'    => [ 12, 34, 56 ],
 *     ] );
 *
 * Для каждого терма берём до 4 услуг (menu_order ASC, date DESC), собираем
 * массив табов и делегируем рендер в `sections/common/services-tabbed`.
 * Если массив term_ids пуст или ни в одной категории нет услуг — return.
 *
 * Изображение карточки: каскад service_hero_image → service_card_image →
 * featured (как в `sections/home/services.php` и hero на single-странице услуги).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_title       = isset( $args['title'] ) ? (string) $args['title'] : '';
$mrent_button_text = isset( $args['button_text'] ) ? (string) $args['button_text'] : '';
if ( $mrent_button_text === '' ) {
	$mrent_button_text = __( 'Все услуги', 'm-rent' );
}
$mrent_button_url = isset( $args['button_url'] ) ? (string) $args['button_url'] : '';
if ( $mrent_button_url === '' ) {
	$mrent_button_url = get_post_type_archive_link( 'service' ) ?: home_url( '/services/' );
}

$mrent_term_ids_raw = isset( $args['term_ids'] ) && is_array( $args['term_ids'] ) ? $args['term_ids'] : [];
$mrent_term_ids     = [];
foreach ( $mrent_term_ids_raw as $mrent_term_value ) {
	if ( is_object( $mrent_term_value ) && isset( $mrent_term_value->term_id ) ) {
		$mrent_term_ids[] = (int) $mrent_term_value->term_id;
	} elseif ( is_numeric( $mrent_term_value ) ) {
		$mrent_term_ids[] = (int) $mrent_term_value;
	}
}

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
