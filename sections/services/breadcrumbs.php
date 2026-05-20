<?php
/**
 * Хлебные крошки раздела «Услуги».
 *
 * Уровни строятся по queried object, разметку рисует sections/common/breadcrumbs:
 *   • archive-service              → Главная > Услуги
 *   • taxonomy-service_category    → Главная > Услуги > {Категория}
 *   • single-service               → Главная > Услуги > {Название}
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_crumbs = [
	[ 'label' => __( 'Главная', 'm-rent' ), 'url' => home_url( '/' ) ],
	[ 'label' => __( 'Услуги', 'm-rent' ), 'url' => get_post_type_archive_link( 'service' ) ?: home_url( '/services/' ) ],
];

if ( is_tax( 'service_category' ) ) {
	$mrent_crumbs[] = [ 'label' => single_term_title( '', false ), 'url' => '' ];
} elseif ( is_singular( 'service' ) ) {
	$mrent_crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];
}

get_template_part( 'sections/common/breadcrumbs', null, [ 'crumbs' => $mrent_crumbs ] );
