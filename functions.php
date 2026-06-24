<?php
/**
 * m-rent theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/vite.php';
require_once __DIR__ . '/inc/icons.php';
require_once __DIR__ . '/inc/class-mrent-nav-walker.php';
require_once __DIR__ . '/inc/svg.php';
require_once __DIR__ . '/inc/cars.php';
require_once __DIR__ . '/inc/services.php';
require_once __DIR__ . '/inc/options.php';
require_once __DIR__ . '/inc/exchange-rate.php';
require_once __DIR__ . '/inc/gtranslate.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

	// Блочный редактор: широкие/полноширинные блоки, адаптивные эмбеды и
	// дефолтные стили блоков ядра (подписи, галереи и т.п.). Нужно, чтобы
	// выравнивание/размеры картинок из Гутенберга реально переносились на фронт.
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );

	register_nav_menus( [
		'primary' => __( 'Главное меню', 'm-rent' ),
		'footer'  => __( 'Меню в подвале', 'm-rent' ),
	] );
} );

/**
 * Преобразует WP-меню локации `footer` в массив колонок для подвала.
 *
 * Каждый пункт верхнего уровня меню становится колонкой (его title — заголовок
 * колонки), а его дочерние пункты — ссылками внутри. Пустые верхние пункты
 * (без детей) тоже валидны: получится колонка только с заголовком-ссылкой.
 *
 * Чтобы скрыть конкретный пункт на мобильной/десктопной раскладке, в админке
 * добавьте к нему CSS-класс `desktop-only` или `mobile-only`.
 *
 * Возвращает [] если меню не назначено или пустое — шаблон в этом случае
 * пропускает блок колонок.
 *
 * @return array<int, array{title:string,url:string,items:array<int, array{label:string,url:string,classes:string[]}>,classes:string[]}>
 */
function mrent_get_footer_columns(): array {
	$locations = get_nav_menu_locations();
	if ( empty( $locations['footer'] ) ) {
		return [];
	}

	$items = wp_get_nav_menu_items( (int) $locations['footer'] );
	if ( empty( $items ) ) {
		return [];
	}

	$columns = [];
	foreach ( $items as $item ) {
		if ( (int) $item->menu_item_parent !== 0 ) {
			continue;
		}
		$columns[ (int) $item->ID ] = [
			'title'   => $item->title,
			'url'     => $item->url,
			'classes' => array_filter( (array) $item->classes ),
			'items'   => [],
		];
	}
	foreach ( $items as $item ) {
		$parent = (int) $item->menu_item_parent;
		if ( $parent === 0 || ! isset( $columns[ $parent ] ) ) {
			continue;
		}
		$columns[ $parent ]['items'][] = [
			'label'   => $item->title,
			'url'     => $item->url,
			'classes' => array_filter( (array) $item->classes ),
		];
	}

	return array_values( $columns );
}

add_action( 'wp_enqueue_scripts', function () {
	mrent_vite_enqueue( 'main.js' );
} );

/**
 * Архив блога — Swiper листает карточки в пределах одной WP-страницы.
 * Бампим `posts_per_page` до 12, чтобы Swiper охватывал больше записей до
 * перехода на /page/2/. На taxonomy-архивах (`category_*`, `tag_*`) и поиске —
 * тоже, чтобы пользователь редко натыкался на «WP-пагинацию» под свайпером.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date() ) {
		$query->set( 'posts_per_page', 12 );
	}
} );
