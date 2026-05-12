<?php

/**
 * ACF Options Page «Контакты и соцсети».
 *
 * Глобальные контакты и ссылки на соцсети, которые используются в нескольких
 * местах темы: шапка, общий блок «Есть вопросы?» (sections/common/contact.php)
 * и в перспективе — подвал. Поля лежат в группе `group_mrent_options`
 * (acf-json/group_mrent_options.json), привязка location: options_page=mrent-options.
 *
 * Хелперы:
 *   • `mrent_options_get('phone_display')` — обёртка над `get_field(..., 'option')`,
 *     возвращает строку (никогда не null), чтобы шаблоны не делали `?? ''`.
 *   • `mrent_options_phone_url()` / `mrent_options_email_url()` — формируют
 *     `tel:` / `mailto:` из contact-полей.
 *   • `mrent_options_socials()` — массив [slug => url] только из непустых ссылок,
 *     в нужном для рендера порядке.
 *
 * Если ACF Pro не активен — функция регистрации просто не выполнится,
 * хелперы вернут пустые значения, и шаблоны корректно деградируют (секция
 * не выведется, в шапке останутся плейсхолдеры).
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action('acf/init', function () {
	if (! function_exists('acf_add_options_page')) {
		return;
	}

	acf_add_options_page([
		'page_title' => __('Контакты и соцсети', 'm-rent'),
		'menu_title' => __('Контакты и соцсети', 'm-rent'),
		'menu_slug'  => 'mrent-options',
		'capability' => 'edit_theme_options',
		'icon_url'   => 'dashicons-phone',
		'position'   => 30,
		'redirect'   => false,
	]);

	acf_add_options_sub_page([
		'page_title'  => __('Архив услуг — настройки', 'm-rent'),
		'menu_title'  => __('Настройки архива', 'm-rent'),
		'menu_slug'   => 'mrent-services-archive',
		'parent_slug' => 'edit.php?post_type=service',
		'capability'  => 'edit_theme_options',
		'redirect'    => false,
	]);

	acf_add_options_sub_page([
		'page_title'  => __('Архив автомобилей — настройки', 'm-rent'),
		'menu_title'  => __('Настройки архива', 'm-rent'),
		'menu_slug'   => 'mrent-cars-archive',
		'parent_slug' => 'edit.php?post_type=car',
		'capability'  => 'edit_theme_options',
		'redirect'    => false,
	]);
});

/**
 * Возвращает строковое значение ACF-поля из Options. Никогда не null.
 */
function mrent_options_get(string $field): string
{
	if (! function_exists('get_field')) {
		return '';
	}
	$value = get_field($field, 'option');
	return is_string($value) ? trim($value) : '';
}

/**
 * Готовый `tel:`-URL телефона. Если поле пустое — '' (шаблон скроет ссылку).
 * Очищаем от пробелов/скобок, сохраняем ведущий «+».
 */
function mrent_options_phone_url(): string
{
	$raw = mrent_options_get('contact_phone');
	if ($raw === '') {
		return '';
	}
	$digits = preg_replace('/[^\d+]/', '', $raw);
	return $digits ? 'tel:' . $digits : '';
}

/**
 * Готовый `mailto:`-URL email-а.
 */
function mrent_options_email_url(): string
{
	$raw = mrent_options_get('contact_email');
	if ($raw === '' || ! is_email($raw)) {
		return '';
	}
	return 'mailto:' . $raw;
}

/**
 * Карта соцсетей: [slug => url] в порядке для рендера.
 *
 * Возвращаются только непустые. Slug совпадает с именем иконки в `mrent_icon`
 * (`brand-youtube`, `brand-tiktok` и т.п. — без префикса `brand-` тут).
 */
function mrent_options_socials(): array
{
	$map = [
		'youtube'   => mrent_options_get('social_youtube'),
		'tiktok'    => mrent_options_get('social_tiktok'),
		'instagram' => mrent_options_get('social_instagram'),
		'telegram'  => mrent_options_get('social_telegram'),
	];
	return array_filter($map, static fn(string $url) => $url !== '');
}

/**
 * URL iframe Яндекс.Карт, собранный из координат и зума из ACF Options.
 * Метка ставится в той же точке (pt=lng,lat,pm2rdm — красный «капля»).
 * Если координаты не заданы — '' (шаблон сам решит, что показать).
 */
function mrent_options_map_iframe_src(): string
{
	$lat  = mrent_options_get('map_lat');
	$lng  = mrent_options_get('map_lng');
	$zoom = mrent_options_get('map_zoom');

	if ($lat === '' || $lng === '') {
		return '';
	}

	$zoom_int = $zoom !== '' ? max(0, min(19, (int) $zoom)) : 16;

	return add_query_arg(
		[
			'll' => $lng . ',' . $lat,
			'z'  => $zoom_int,
			'pt' => $lng . ',' . $lat . ',pm2rdm',
		],
		'https://yandex.ru/map-widget/v1/'
	);
}

/**
 * Карта мессенджеров: [slug => url] в порядке для рендера.
 */
function mrent_options_messengers(): array
{
	$map = [
		'whatsapp'  => mrent_options_get('contact_whatsapp'),
		'viber'     => mrent_options_get('contact_viber'),
		'messenger' => mrent_options_get('contact_messenger'),
		'telegram'  => mrent_options_get('contact_telegram'),
	];
	return array_filter($map, static fn(string $url) => $url !== '');
}
