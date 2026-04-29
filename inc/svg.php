<?php

/**
 * Разрешаем SVG в медиатеке.
 *
 * WP по умолчанию блокирует загрузку SVG как потенциально опасный формат
 * (XSS через <script> внутри файла). Включаем только потому, что админка
 * закрыта от внешних пользователей. Если потом откроется регистрация —
 * добавить sanitize через плагин типа Safe SVG.
 */

if (! defined('ABSPATH')) {
	exit;
}

add_filter('upload_mimes', function (array $mimes): array {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
});

/**
 * WP 5.0.1+ повторно проверяет MIME через `finfo_file`, и SVG-файлы без
 * XML-декларации могут отдавать `text/plain` — фильтр выше не поможет.
 * Принудительно ставим image/svg+xml для расширения .svg.
 */
add_filter('wp_check_filetype_and_ext', function (array $data, string $file, string $filename): array {
	if (substr($filename, -4) === '.svg') {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}, 10, 3);
