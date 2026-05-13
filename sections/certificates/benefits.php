<?php

/**
 * Секция «Преимущества» на странице подарочных сертификатов.
 *
 * Figma: 2178:3090 (desktop).
 *
 * Тонкая обёртка: читает ACF (certificates_benefits_*), сопоставляет
 * select-иконки с inline-SVG и рендерит универсальный sections/cards-grid.
 *
 * ACF (group_mrent_certificates):
 *   certificates_benefits_title  (text)
 *   certificates_benefits_items  (repeater: icon, title)
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = (string) get_field('certificates_benefits_title');
if ($mrent_title === '') {
	$mrent_title = __('Преимущества', 'm-rent');
}

$mrent_icons = [
	// prime:crown
	'crown'      => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 20l8 22h28l8-22-12 8-10-16-10 16-12-8z"/><path d="M16 46h28"/></svg>',
	// ph:sliders
	'sliders'    => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 18h28M44 18h6"/><circle cx="41" cy="18" r="4"/><path d="M10 30h10M26 30h24"/><circle cx="23" cy="30" r="4"/><path d="M10 42h22M38 42h12"/><circle cx="35" cy="42" r="4"/></svg>',
	// ion:car-sport-outline
	'car-sport'  => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 38v-4c0-1 1-2 2-2l3-1 5-9c1-2 3-3 5-3h14c2 0 4 1 5 3l5 9 3 1c1 0 2 1 2 2v4c0 1-1 2-2 2h-3"/><path d="M12 40h36"/><circle cx="17" cy="40" r="4"/><circle cx="43" cy="40" r="4"/><path d="M14 32l4-9h24l4 9"/></svg>',
	// lets-icons:form
	'form'       => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="14" y="10" width="32" height="40" rx="3"/><path d="M22 22h16M22 30h16M22 38h10"/></svg>',
];

$mrent_items_raw = get_field('certificates_benefits_items');
if (! is_array($mrent_items_raw) || ! $mrent_items_raw) {
	$mrent_items_raw = [
		['icon' => 'crown',     'title' => __('Красивый и статусный формат подарка', 'm-rent')],
		['icon' => 'sliders',   'title' => __('Возможность персонализации', 'm-rent')],
		['icon' => 'car-sport', 'title' => __('Выбор автомобиля под вкус получателя', 'm-rent')],
		['icon' => 'form',      'title' => __('Гибкие номиналы и форматы', 'm-rent')],
	];
}

$mrent_items = [];
foreach ($mrent_items_raw as $mrent_row) {
	$mrent_key = isset($mrent_row['icon']) ? (string) $mrent_row['icon'] : '';
	$mrent_items[] = [
		'icon_svg' => $mrent_icons[$mrent_key] ?? '',
		'title'    => isset($mrent_row['title']) ? (string) $mrent_row['title'] : '',
	];
}

get_template_part('sections/cards-grid', null, [
	'title'            => $mrent_title,
	'items'            => $mrent_items,
	'grid_cols'        => 4,
	'tall_cards'       => false,
	'pagination_after' => 4,
]);
