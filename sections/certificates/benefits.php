<?php

/**
 * Секция «Преимущества» на странице подарочных сертификатов.
 *
 * Figma: 2178:3090 (desktop).
 *
 * Тонкая обёртка: читает ACF (certificates_benefits_*) и рендерит
 * универсальный sections/cards-grid. Иконка карточки загружается картинкой
 * через медиабиблиотеку (ACF image-поле, return_format «array»).
 *
 * ACF (group_mrent_certificates):
 *   certificates_benefits_title  (text)
 *   certificates_benefits_items  (repeater: icon (image), title)
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = (string) get_field('certificates_benefits_title');
if ($mrent_title === '') {
	$mrent_title = __('Преимущества', 'm-rent');
}

$mrent_items_raw = get_field('certificates_benefits_items');
if (! is_array($mrent_items_raw) || ! $mrent_items_raw) {
	// Фолбэк-заголовки для свежей установки — иконки добавляются в админке.
	$mrent_items_raw = [
		['title' => __('Красивый и статусный формат подарка', 'm-rent')],
		['title' => __('Возможность персонализации', 'm-rent')],
		['title' => __('Выбор автомобиля под вкус получателя', 'm-rent')],
		['title' => __('Гибкие номиналы и форматы', 'm-rent')],
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
	'tall_cards'       => false,
	'pagination_after' => 4,
]);
