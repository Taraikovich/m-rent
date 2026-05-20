<?php
/**
 * Хлебные крошки раздела «Блог».
 *
 * Уровни строятся по queried object, разметку рисует sections/common/breadcrumbs:
 *   • home (страница записей) / archive  → Главная > Блог
 *   • category / tag / tax               → Главная > Блог > {терм}
 *   • single                             → Главная > Блог > {заголовок}
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_blog_url = '';
$mrent_posts_page_id = (int) get_option( 'page_for_posts' );
if ( $mrent_posts_page_id > 0 ) {
	$mrent_blog_url = get_permalink( $mrent_posts_page_id );
}
if ( ! $mrent_blog_url ) {
	$mrent_blog_url = home_url( '/blog/' );
}

$mrent_crumbs = [
	[ 'label' => __( 'Главная', 'm-rent' ), 'url' => home_url( '/' ) ],
	[ 'label' => __( 'Блог', 'm-rent' ), 'url' => $mrent_blog_url ],
];

if ( is_category() || is_tag() || is_tax() ) {
	$mrent_crumbs[] = [ 'label' => single_term_title( '', false ), 'url' => '' ];
} elseif ( is_singular( 'post' ) ) {
	$mrent_crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];
}

get_template_part( 'sections/common/breadcrumbs', null, [ 'crumbs' => $mrent_crumbs ] );
