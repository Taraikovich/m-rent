<?php
/**
 * Хлебные крошки раздела «Автопарк».
 *
 * Уровни строятся по queried object, разметку рисует sections/common/breadcrumbs:
 *   • archive-car             → Главная > Все автомобили
 *   • taxonomy-car_category   → Главная > Все автомобили > {Категория}
 *   • single-car              → Главная > Все автомобили > {Название модели}
 *
 * Figma: 388:356 (archive), 392:637 (single).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_crumbs = [
	[ 'label' => __( 'Главная', 'm-rent' ), 'url' => home_url( '/' ) ],
	[ 'label' => __( 'Все автомобили', 'm-rent' ), 'url' => get_post_type_archive_link( 'car' ) ?: home_url( '/cars/' ) ],
];

if ( is_tax( 'car_category' ) ) {
	$mrent_crumbs[] = [ 'label' => single_term_title( '', false ), 'url' => '' ];
} elseif ( is_singular( 'car' ) ) {
	$mrent_crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];
}

get_template_part( 'sections/common/breadcrumbs', null, [ 'crumbs' => $mrent_crumbs ] );
