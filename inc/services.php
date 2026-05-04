<?php
/**
 * Раздел «Услуги»: CPT `service` + таксономия `service_category`.
 *
 * URL-схема:
 *   • /services/                            — общий архив (archive-service.php)
 *   • /services/?cat={slug}                 — архив, отфильтрованный JS/PHP по slug категории
 *   • /services/category/{slug}/            — архив одной категории (taxonomy-service_category.php)
 *   • /services/{post-slug}/                — страница услуги (single-service.php)
 *
 * Polylang: оба объекта помечены через `pll_get_post_types` / `pll_get_taxonomies`.
 *
 * NB: фильтрация на архиве идёт через GET-параметр `cat` (одиночный выбор), а не
 * через переход на taxonomy-archive — чтобы все категории отображались на одной
 * странице секциями (по дизайну Figma 5095:7367).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Регистрация CPT и таксономии. Таксономия раньше CPT — иначе wildcard-правило
 * CPT перехватывает /services/category/{slug}/ (см. cars.php, та же ловушка).
 */
add_action( 'init', function () {
	register_taxonomy( 'service_category', [ 'service' ], [
		'labels'            => [
			'name'              => __( 'Категории услуг', 'm-rent' ),
			'singular_name'     => __( 'Категория', 'm-rent' ),
			'menu_name'         => __( 'Категории', 'm-rent' ),
			'all_items'         => __( 'Все категории', 'm-rent' ),
			'edit_item'         => __( 'Редактировать категорию', 'm-rent' ),
			'view_item'         => __( 'Посмотреть категорию', 'm-rent' ),
			'update_item'       => __( 'Обновить категорию', 'm-rent' ),
			'add_new_item'      => __( 'Добавить категорию', 'm-rent' ),
			'new_item_name'     => __( 'Название новой категории', 'm-rent' ),
			'search_items'      => __( 'Найти категорию', 'm-rent' ),
			'not_found'         => __( 'Не найдено', 'm-rent' ),
		],
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => [ 'slug' => 'services/category', 'with_front' => false ],
	] );

	register_post_type( 'service', [
		'labels'             => [
			'name'                  => __( 'Услуги', 'm-rent' ),
			'singular_name'         => __( 'Услуга', 'm-rent' ),
			'menu_name'             => __( 'Услуги', 'm-rent' ),
			'all_items'             => __( 'Все услуги', 'm-rent' ),
			'add_new'               => __( 'Добавить', 'm-rent' ),
			'add_new_item'          => __( 'Добавить услугу', 'm-rent' ),
			'edit_item'             => __( 'Редактировать услугу', 'm-rent' ),
			'new_item'              => __( 'Новая услуга', 'm-rent' ),
			'view_item'             => __( 'Посмотреть услугу', 'm-rent' ),
			'view_items'            => __( 'Посмотреть услуги', 'm-rent' ),
			'search_items'          => __( 'Найти услугу', 'm-rent' ),
			'not_found'             => __( 'Не найдено', 'm-rent' ),
			'not_found_in_trash'    => __( 'В корзине нет услуг', 'm-rent' ),
			'featured_image'        => __( 'Главное фото', 'm-rent' ),
			'set_featured_image'    => __( 'Установить главное фото', 'm-rent' ),
			'remove_featured_image' => __( 'Убрать главное фото', 'm-rent' ),
			'use_featured_image'    => __( 'Использовать как главное фото', 'm-rent' ),
		],
		'public'             => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-portfolio',
		'menu_position'      => 21,
		'has_archive'        => 'services',
		'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
		'rewrite'            => [ 'slug' => 'services', 'with_front' => false ],
		'hierarchical'       => false,
	] );
} );

/**
 * Polylang: помечаем `service` и `service_category` как переводимые.
 */
add_filter( 'pll_get_post_types', function ( $types ) {
	$types['service'] = 'service';
	return $types;
} );
add_filter( 'pll_get_taxonomies', function ( $tax ) {
	$tax['service_category'] = 'service_category';
	return $tax;
} );

/**
 * Сидируем базовый набор категорий услуг (Figma 5095:7367 — пилюли категорий).
 *
 * Гард `mrent_services_categories_seeded` ставится после первой попытки —
 * не имеет значения, добавились ли термы (могли быть созданы вручную).
 * Чтобы пересоздать удалённый терм — сбросить опцию:
 *   wp option delete mrent_services_categories_seeded
 */
add_action( 'init', function () {
	if ( get_option( 'mrent_services_categories_seeded' ) ) {
		return;
	}
	if ( ! taxonomy_exists( 'service_category' ) ) {
		return;
	}

	$terms = [
		'events'      => 'Для мероприятий',
		'photoshoot'  => 'Фотосессия под ключ',
		'rental'      => 'Услуги по аренде',
		'transfer'    => 'Трансфер',
		'bmw-import'  => 'Импорт BMW',
	];

	foreach ( $terms as $slug => $name ) {
		if ( term_exists( $slug, 'service_category' ) ) {
			continue;
		}
		wp_insert_term( $name, 'service_category', [ 'slug' => $slug ] );
	}

	update_option( 'mrent_services_categories_seeded', 1, true );
}, 20 );

/**
 * Все опубликованные категории `service_category` в порядке term_id.
 *
 * Polylang-переводимые термы фильтруются `get_terms` автоматически по текущему
 * языку. На русском получим один набор, на других — переводы.
 *
 * @return WP_Term[]
 */
function mrent_get_service_categories(): array {
	$terms = get_terms( [
		'taxonomy'   => 'service_category',
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	] );
	return is_wp_error( $terms ) ? [] : $terms;
}

/**
 * На archive-service показываем все услуги разом — каталог одностраничный по
 * дизайну (несколько секций по категориям). Если в будущем услуг станет много —
 * убрать или заменить на конкретный лимит.
 *
 * Для taxonomy-service_category пагинация — стандартная (по умолчанию posts_per_page).
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'service' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
} );

/**
 * Колонки в админке: миниатюра, заголовок, категория (через show_admin_column),
 * порядок (menu_order для drag-n-drop через page-attributes), дата.
 */
add_filter( 'manage_service_posts_columns', function ( $columns ) {
	$new = [];
	foreach ( $columns as $key => $label ) {
		if ( $key === 'title' ) {
			$new['mrent_thumb'] = __( 'Фото', 'm-rent' );
		}
		$new[ $key ] = $label;
	}
	$new['menu_order'] = __( 'Порядок', 'm-rent' );
	return $new;
} );

add_action( 'manage_service_posts_custom_column', function ( $column, $post_id ) {
	if ( $column === 'mrent_thumb' ) {
		$thumb = get_the_post_thumbnail( $post_id, [ 60, 60 ], [ 'style' => 'border-radius:6px;object-fit:cover;' ] );
		echo $thumb ?: '<span style="color:#999;">—</span>';
	} elseif ( $column === 'menu_order' ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}, 10, 2 );

add_filter( 'manage_edit-service_sortable_columns', function ( $columns ) {
	$columns['menu_order'] = 'menu_order';
	return $columns;
} );

/**
 * По умолчанию сортируем список услуг в админке по menu_order ASC, чтобы
 * drag-n-drop через page-attributes сразу отражался.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->post_type !== 'service' ) {
		return;
	}
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );
	}
} );

/**
 * На фронте в архиве и таксономии тоже сортируем по menu_order, потом по дате.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'service' ) || $query->is_tax( 'service_category' ) ) {
		$query->set( 'orderby', [ 'menu_order' => 'ASC', 'date' => 'DESC' ] );
	}
} );
