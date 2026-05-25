<?php

/**
 * Секция «Кому подойдет» на странице подарочных сертификатов.
 *
 * Figma: 2178:2824 (desktop), 2345:4429 (mobile).
 *
 * Тонкая обёртка: читает ACF-поля (certificates_audience_*) и рендерит
 * универсальный sections/cards-grid. Иконка карточки загружается картинкой
 * через медиабиблиотеку (ACF image-поле, return_format «array»).
 *
 * ACF (group_mrent_certificates):
 *   certificates_audience_title  (text)
 *   certificates_audience_items  (repeater: icon (image), title)
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = (string) get_field('certificates_audience_title');
if ($mrent_title === '') {
	$mrent_title = __('Кому подойдет', 'm-rent');
}

$mrent_items_raw = get_field('certificates_audience_items');
if (! is_array($mrent_items_raw) || ! $mrent_items_raw) {
	// Фолбэк-заголовки для свежей установки — иконки добавляются в админке.
	$mrent_items_raw = [
		['title' => __('На день рождения', 'm-rent')],
		['title' => __('Для автолюбителя', 'm-rent')],
		['title' => __('Для пары', 'm-rent')],
		['title' => __('На выходные', 'm-rent')],
		['title' => __('Как необычный подарок-впечатление', 'm-rent')],
		['title' => __('В качестве корпоративного подарка', 'm-rent')],
		['title' => __('В подарок мужчине или девушке', 'm-rent')],
		['title' => __('На годовщину', 'm-rent')],
	];
}

$mrent_items = [];
foreach ($mrent_items_raw as $mrent_row) {
	$mrent_items[] = [
		'icon'  => isset($mrent_row['icon']) ? $mrent_row['icon'] : '',
		'title' => isset($mrent_row['title']) ? (string) $mrent_row['title'] : '',
	];
}

get_template_part('sections/cards-grid', null, [
	'title'            => $mrent_title,
	'items'            => $mrent_items,
	'grid_cols'        => 4,
	'tall_cards'       => true,
	'pagination_after' => 4,
]);
