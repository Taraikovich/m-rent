<?php
/**
 * Vite-интеграция для темы m-rent.
 *
 * - В dev-режиме (есть файл assets/build/hot): подключает Vite-клиент и точку входа
 *   с http://localhost:5173, стили Vite инжектит сам.
 * - В prod-режиме: читает assets/build/.vite/manifest.json и подключает хешированные
 *   ассеты как обычные wp_enqueue_script/style с правильным ver=md5(file).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const MRENT_VITE_DEV_URL  = 'http://localhost:5173';
const MRENT_VITE_BUILD_DIR = '/assets/build';

function mrent_vite_is_dev(): bool {
	return file_exists( get_stylesheet_directory() . MRENT_VITE_BUILD_DIR . '/hot' );
}

function mrent_vite_dev_url(): string {
	$file = get_stylesheet_directory() . MRENT_VITE_BUILD_DIR . '/hot';
	if ( file_exists( $file ) ) {
		$url = trim( (string) file_get_contents( $file ) );
		if ( $url !== '' ) {
			return rtrim( $url, '/' );
		}
	}
	return MRENT_VITE_DEV_URL;
}

/**
 * Подключает entry-точку Vite ($entry — путь относительно корня проекта, например 'src/main.js').
 */
function mrent_vite_enqueue( string $entry ): void {
	if ( mrent_vite_is_dev() ) {
		mrent_vite_enqueue_dev( $entry );
		return;
	}
	mrent_vite_enqueue_prod( $entry );
}

function mrent_vite_enqueue_dev( string $entry ): void {
	$base = mrent_vite_dev_url();

	// wp_enqueue_scripts срабатывает внутри wp_head, поэтому печатаем теги напрямую.
	add_action( 'wp_head', function () use ( $base, $entry ) {
		printf(
			'<script type="module" src="%s/@vite/client"></script>' . "\n",
			esc_url( $base )
		);
		printf(
			'<script type="module" src="%s/%s"></script>' . "\n",
			esc_url( $base ),
			esc_attr( ltrim( $entry, '/' ) )
		);
	}, 99 );
}

function mrent_vite_enqueue_prod( string $entry ): void {
	$theme_dir  = get_stylesheet_directory();
	$theme_uri  = get_stylesheet_directory_uri();
	$manifest_path = $theme_dir . MRENT_VITE_BUILD_DIR . '/.vite/manifest.json';

	if ( ! file_exists( $manifest_path ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[m-rent] Vite manifest не найден: ' . $manifest_path );
		}
		return;
	}

	$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );
	$key      = ltrim( $entry, '/' );

	if ( ! is_array( $manifest ) || ! isset( $manifest[ $key ] ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[m-rent] Vite manifest не содержит entry: ' . $key );
		}
		return;
	}

	$item       = $manifest[ $key ];
	$build_uri  = $theme_uri . MRENT_VITE_BUILD_DIR;
	$handle     = 'mrent-' . sanitize_title( pathinfo( $key, PATHINFO_FILENAME ) );

	// CSS
	if ( ! empty( $item['css'] ) && is_array( $item['css'] ) ) {
		foreach ( $item['css'] as $i => $css_file ) {
			$abs = $theme_dir . MRENT_VITE_BUILD_DIR . '/' . $css_file;
			$ver = file_exists( $abs ) ? substr( md5_file( $abs ), 0, 8 ) : null;
			wp_enqueue_style( $handle . '-' . $i, $build_uri . '/' . $css_file, [], $ver );
		}
	}

	// JS
	if ( ! empty( $item['file'] ) ) {
		$abs = $theme_dir . MRENT_VITE_BUILD_DIR . '/' . $item['file'];
		$ver = file_exists( $abs ) ? substr( md5_file( $abs ), 0, 8 ) : null;
		wp_enqueue_script( $handle, $build_uri . '/' . $item['file'], [], $ver, true );

		// Добавляем type="module" к нашему script-тегу
		add_filter( 'script_loader_tag', function ( $tag, $h ) use ( $handle ) {
			if ( $h !== $handle ) {
				return $tag;
			}
			if ( strpos( $tag, 'type=' ) !== false ) {
				$tag = preg_replace( '/type=("|\')[^"\']+("|\')/', 'type="module"', $tag );
			} else {
				$tag = str_replace( '<script ', '<script type="module" ', $tag );
			}
			return $tag;
		}, 10, 2 );
	}
}
