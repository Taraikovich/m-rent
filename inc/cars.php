<?php
/**
 * Раздел «Автопарк»: CPT `car` + таксономия `car_category`.
 *
 * URL-схема:
 *   • /cars/                     — общий каталог (archive-car.php)
 *   • /cars/category/{slug}/     — каталог по категории (taxonomy-car_category.php)
 *   • /cars/{post-slug}/         — страница модели (single-car.php)
 *
 * Для совместимости с Polylang оба объекта помечены translatable через
 * `pll-supported-post-types` / `pll-supported-taxonomies` (фильтры применяются
 * в админке Polylang при первом обращении). Если плагин неактивен — ничего
 * не ломается, просто фильтры не сработают.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Регистрация CPT и таксономии.
 *
 * Префикс slug'а таксономии — `cars/category`, чтобы все URL автопарка жили
 * под общим корнем /cars/. `with_front => false` критично: иначе WP добавит
 * глобальный фронт-префикс (если он есть) перед нашим путём.
 */
add_action( 'init', function () {
	// ВАЖНО: таксономию регистрируем РАНЬШЕ CPT, иначе wildcard-правило CPT
	// `cars/[^/]+/([^/]+)/?$` (для attachment'ов) перехватывает /cars/category/{slug}/
	// и роутит на attachment вместо taxonomy-archive → 404.
	register_taxonomy( 'car_category', [ 'car' ], [
		'labels'            => [
			'name'              => __( 'Категории', 'm-rent' ),
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
		'rewrite'           => [ 'slug' => 'cars/category', 'with_front' => false ],
	] );

	register_post_type( 'car', [
		'labels'             => [
			'name'                  => __( 'Автопарк', 'm-rent' ),
			'singular_name'         => __( 'Автомобиль', 'm-rent' ),
			'menu_name'             => __( 'Автопарк', 'm-rent' ),
			'all_items'             => __( 'Все автомобили', 'm-rent' ),
			'add_new'               => __( 'Добавить', 'm-rent' ),
			'add_new_item'          => __( 'Добавить автомобиль', 'm-rent' ),
			'edit_item'             => __( 'Редактировать автомобиль', 'm-rent' ),
			'new_item'              => __( 'Новый автомобиль', 'm-rent' ),
			'view_item'             => __( 'Посмотреть автомобиль', 'm-rent' ),
			'view_items'            => __( 'Посмотреть автомобили', 'm-rent' ),
			'search_items'          => __( 'Найти автомобиль', 'm-rent' ),
			'not_found'             => __( 'Не найдено', 'm-rent' ),
			'not_found_in_trash'    => __( 'В корзине нет автомобилей', 'm-rent' ),
			'featured_image'        => __( 'Главное фото', 'm-rent' ),
			'set_featured_image'    => __( 'Установить главное фото', 'm-rent' ),
			'remove_featured_image' => __( 'Убрать главное фото', 'm-rent' ),
			'use_featured_image'    => __( 'Использовать как главное фото', 'm-rent' ),
		],
		'public'             => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-car',
		'menu_position'      => 20,
		'has_archive'        => 'cars',
		'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
		'rewrite'            => [ 'slug' => 'cars', 'with_front' => false ],
		'hierarchical'       => false,
	] );
} );

/**
 * Polylang: помечаем `car` и `car_category` как переводимые.
 * Фильтры срабатывают только если Polylang активен — иначе no-op.
 */
add_filter( 'pll_get_post_types', function ( $types ) {
	$types['car'] = 'car';
	return $types;
} );
add_filter( 'pll_get_taxonomies', function ( $tax ) {
	$tax['car_category'] = 'car_category';
	return $tax;
} );

/**
 * Один раз сидим базовый набор категорий, чтобы редактору сразу было что фильтровать.
 *
 * Список зашит по требованию (Городской премиум, Кабриолеты, Бизнес, MINI JCW,
 * M Sport, M Power, Внедорожники). «Все автомобили» — это виртуальный пункт
 * в фильтрах (ссылка на /cars/), а не термин таксономии.
 *
 * Гард `mrent_cars_categories_seeded` ставится после первой попытки — не имеет
 * значения, добавились ли термы (могли быть созданы вручную раньше). Чтобы
 * пересоздать удалённый терм — сбросить опцию: wp option delete mrent_cars_categories_seeded
 */
add_action( 'init', function () {
	if ( get_option( 'mrent_cars_categories_seeded' ) ) {
		return;
	}
	if ( ! taxonomy_exists( 'car_category' ) ) {
		return;
	}

	$terms = [
		'gorodskoy-premium' => 'Городской премиум',
		'cabriolet'         => 'Кабриолеты',
		'business'          => 'Бизнес',
		'mini-jcw'          => 'MINI JCW',
		'm-sport'           => 'M Sport',
		'm-power'           => 'M Power',
		'suv'               => 'Внедорожники',
	];

	$order = 0;
	foreach ( $terms as $slug => $name ) {
		$order++;
		if ( term_exists( $slug, 'car_category' ) ) {
			continue;
		}
		wp_insert_term( $name, 'car_category', [ 'slug' => $slug ] );
	}

	update_option( 'mrent_cars_categories_seeded', 1, true );
}, 20 );

/**
 * Все опубликованные категории `car_category` в порядке term_id (порядок создания).
 *
 * Используется фильтрами в archive/taxonomy. Если терм Polylang-переводимый —
 * `get_terms` сам отдаст уже отфильтрованный по текущему языку список.
 *
 * @return WP_Term[]
 */
function mrent_get_car_categories(): array {
	$terms = get_terms( [
		'taxonomy'   => 'car_category',
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	] );
	return is_wp_error( $terms ) ? [] : $terms;
}

/**
 * Форматирует цену для вывода без копеек, если число целое; иначе с одним знаком.
 * 120.0 → "120", 99.5 → "99.5". Используется в карточках и прайс-таблице.
 */
/**
 * На archive-car и taxonomy-car_category показываем все машины разом —
 * каталог одностраничный по дизайну. Если в будущем потребуется пагинация,
 * убрать или заменить на конкретное число.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'car' ) || $query->is_tax( 'car_category' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
} );

function mrent_format_price( float $value ): string {
	if ( $value <= 0 ) {
		return '0';
	}
	if ( floor( $value ) === $value ) {
		return (string) (int) $value;
	}
	return rtrim( rtrim( number_format( $value, 2, '.', '' ), '0' ), '.' );
}
