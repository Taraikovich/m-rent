<?php

/**
 * Секция «Кому подойдет» на странице подарочных сертификатов.
 *
 * Figma: 2178:2824 (desktop), 2345:4429 (mobile).
 *
 * Тонкая обёртка: читает ACF-поля (certificates_audience_*), сопоставляет
 * select-иконки с inline-SVG и рендерит универсальный sections/cards-grid.
 *
 * ACF (group_mrent_certificates):
 *   certificates_audience_title  (text)
 *   certificates_audience_items  (repeater: icon, title)
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = (string) get_field('certificates_audience_title');
if ($mrent_title === '') {
	$mrent_title = __('Кому подойдет', 'm-rent');
}

// Inline-SVG: ключ совпадает со значением select-поля ACF.
// Внешние URL не используем — Figma-ассеты живут 7 дней.
$mrent_icons = [
	'birthday-cake' => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 30h40v20H10z"/><path d="M10 40c4 0 4 3 8 3s4-3 8-3 4 3 8 3 4-3 8-3 4 3 8 3"/><path d="M15 30v-7h30v7"/><path d="M30 23v-6"/><path d="M30 17c-2-2-2-4 0-6 2 2 2 4 0 6z" fill="currentColor"/></svg>',
	'car'           => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 38v-6l4-2 4-10c1-2 3-3 5-3h18c2 0 4 1 5 3l4 10 4 2v6c0 1-1 2-2 2h-3"/><path d="M11 40h38"/><circle cx="17" cy="40" r="4"/><circle cx="43" cy="40" r="4"/><path d="M14 30h32"/></svg>',
	'couple'        => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="22" cy="20" r="6"/><circle cx="38" cy="20" r="6"/><path d="M12 44c0-5 4-10 10-10s10 5 10 10"/><path d="M28 44c0-5 4-10 10-10s10 5 10 10"/></svg>',
	'suitcase'      => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="10" y="20" width="40" height="28" rx="3"/><path d="M22 20v-5c0-1 1-2 2-2h12c1 0 2 1 2 2v5"/><path d="M10 32h40"/></svg>',
	'present'       => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="10" y="22" width="40" height="8" rx="1"/><path d="M13 30v20h34V30"/><path d="M30 22v28"/><path d="M30 22c-3-6-9-6-9-2 0 3 4 4 9 2zM30 22c3-6 9-6 9-2 0 3-4 4-9 2z"/></svg>',
	'audience'      => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="30" cy="20" r="6"/><circle cx="15" cy="24" r="5"/><circle cx="45" cy="24" r="5"/><path d="M20 44c0-5 4-10 10-10s10 5 10 10"/><path d="M6 44c0-4 3-8 9-8M54 44c0-4-3-8-9-8"/></svg>',
	'people'        => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="22" cy="22" r="6"/><circle cx="40" cy="24" r="5"/><path d="M10 46c0-6 5-12 12-12s12 6 12 12"/><path d="M34 38c1-3 4-6 6-6 5 0 10 4 10 10"/></svg>',
	'anniversary'   => '<svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="10" y="14" width="40" height="36" rx="3"/><path d="M10 24h40"/><path d="M20 10v8M40 10v8"/><path d="M30 38c-3-3-6-3-6 0 0 3 3 5 6 7 3-2 6-4 6-7 0-3-3-3-6 0z"/></svg>',
];

$mrent_items_raw = get_field('certificates_audience_items');
if (! is_array($mrent_items_raw) || ! $mrent_items_raw) {
	$mrent_items_raw = [
		['icon' => 'birthday-cake', 'title' => __('На день рождения', 'm-rent')],
		['icon' => 'car',           'title' => __('Для автолюбителя', 'm-rent')],
		['icon' => 'couple',        'title' => __('Для пары', 'm-rent')],
		['icon' => 'suitcase',      'title' => __('На выходные', 'm-rent')],
		['icon' => 'present',       'title' => __('Как необычный подарок-впечатление', 'm-rent')],
		['icon' => 'audience',      'title' => __('В качестве корпоративного подарка', 'm-rent')],
		['icon' => 'people',        'title' => __('В подарок мужчине или девушке', 'm-rent')],
		['icon' => 'anniversary',   'title' => __('На годовщину', 'm-rent')],
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
	'tall_cards'       => true,
	'pagination_after' => 4,
]);
