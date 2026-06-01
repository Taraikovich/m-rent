<?php

/**
 * Интеграция кастомного переключателя языков в шапке с плагином GTranslate (free).
 *
 * Как это работает:
 *   • GTranslate переводит страницу через Google Translate и хранит выбор в cookie
 *     `googtrans` формата `/{src}/{dst}` (например `/ru/en`). На каждой загрузке
 *     скрипт плагина (js/float.js) читает cookie и, если язык ≠ дефолтного,
 *     подгружает Google-движок и переводит страницу.
 *   • Плагин настроен в режиме «float», но плавающий виджет отключён
 *     (floating_language_selector = "no"), поэтому на фронте он сам ничего не
 *     выводит — и его JS-машинерии (window.doGTranslate, авто-применение cookie)
 *     на странице нет.
 *
 * Поэтому мы:
 *   1) Выводим виджет плагина СКРЫТО в подвале (mrent_gtranslate_boot) — это
 *      подключает js/float.js + настройки и включает авто-применение cookie.
 *   2) Рендерим свой переключатель в шапке (mrent_lang_switcher), который просто
 *      ставит cookie `googtrans` и перезагружает страницу — перевод делает плагин.
 *      См. src/lang-switcher.js.
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Короткий код (для кнопки) + родное название (для списка) по коду языка.
 * Источник состава/порядка — настройки GTranslate (incl_langs), дефолт идёт первым.
 *
 * @return array<string, array{code:string, name:string}>
 */
function mrent_gtranslate_languages()
{
	$labels = [
		'ru'    => ['РУ', 'Русский'],
		'en'    => ['EN', 'English'],
		'es'    => ['ES', 'Español'],
		'it'    => ['IT', 'Italiano'],
		'pt'    => ['PT', 'Português'],
		'de'    => ['DE', 'Deutsch'],
		'fr'    => ['FR', 'Français'],
		'nl'    => ['NL', 'Nederlands'],
		'ar'    => ['AR', 'العربية'],
		'zh-CN' => ['ZH', '中文'],
	];

	$opt     = get_option('GTranslate');
	$incl    = (is_array($opt) && ! empty($opt['incl_langs'])) ? (array) $opt['incl_langs'] : array_keys($labels);
	$default = mrent_gtranslate_default_lang();

	// Дефолт первым, затем остальные в порядке incl_langs.
	$ordered = array_values(array_unique(array_merge([$default], $incl)));

	$out = [];
	foreach ($ordered as $code) {
		if (! isset($labels[$code])) {
			continue;
		}
		$out[$code] = [
			'code' => $labels[$code][0],
			'name' => $labels[$code][1],
		];
	}

	return $out;
}

/**
 * Язык-источник сайта (дефолт GTranslate).
 */
function mrent_gtranslate_default_lang()
{
	$opt = get_option('GTranslate');
	return (is_array($opt) && ! empty($opt['default_language'])) ? $opt['default_language'] : 'ru';
}

/**
 * Текущий выбранный язык из cookie `googtrans` (или дефолт, если не задан/неизвестен).
 * Читаем на сервере, чтобы кнопка сразу показывала верный код без мигания.
 */
function mrent_gtranslate_current_lang()
{
	$default = mrent_gtranslate_default_lang();
	if (empty($_COOKIE['googtrans'])) {
		return $default;
	}

	$parts = explode('/', trim((string) $_COOKIE['googtrans'], '/')); // «ru/en» → ['ru','en']
	$lang  = end($parts);
	$langs = mrent_gtranslate_languages();

	return isset($langs[$lang]) ? $lang : $default;
}

/**
 * Скрытый бутстрап плагина: выводим виджет GTranslate в подвале, чтобы подключить
 * его JS (window.doGTranslate + авто-применение cookie googtrans). Сам виджет скрыт
 * через CSS (.mrent-gt-hidden / .gtranslate_wrapper) — UI у нас свой в шапке.
 *
 * Приоритет 5 < 20 (wp_print_footer_scripts), чтобы скрипт плагина успел встать
 * в очередь и попасть в подвал.
 */
function mrent_gtranslate_boot()
{
	echo '<div class="mrent-gt-hidden" aria-hidden="true">' . do_shortcode('[gtranslate]') . '</div>';
}
add_action('wp_footer', 'mrent_gtranslate_boot', 5);

/**
 * Разметка переключателя языков для шапки.
 *
 * @param bool $compact true — компактная мобильная кнопка (фикс. высота 30px).
 * @return string
 */
function mrent_lang_switcher($compact = false)
{
	$langs   = mrent_gtranslate_languages();
	$default = mrent_gtranslate_default_lang();
	$current = mrent_gtranslate_current_lang();
	$cur     = isset($langs[$current]) ? $langs[$current] : reset($langs);

	$btn_h = $compact ? 'h-[30px] ' : '';

	ob_start(); ?>
	<div class="relative notranslate" translate="no" data-mrent-lang data-default="<?php echo esc_attr($default); ?>">
		<button type="button"
			class="border border-white <?php echo $btn_h; ?>px-[7px] py-[5px] rounded-[5px] inline-flex items-center"
			aria-haspopup="true" aria-expanded="false"
			aria-label="<?php esc_attr_e('Сменить язык', 'm-rent'); ?>"
			data-mrent-lang-toggle>
			<span class="mrent-stack-text text-[clamp(14px,calc(13.03px+0.26vw),18px)] leading-none" data-text="<?php echo esc_attr($cur['code']); ?>" data-mrent-lang-current><?php echo esc_html($cur['code']); ?></span>
		</button>
		<ul class="mrent-lang-menu" role="menu" data-mrent-lang-menu hidden>
			<?php foreach ($langs as $code => $l) : ?>
				<li role="none">
					<button type="button" role="menuitem"
						class="mrent-lang-option<?php echo $code === $current ? ' is-active' : ''; ?>"
						data-lang="<?php echo esc_attr($code); ?>"
						data-code="<?php echo esc_attr($l['code']); ?>">
						<?php echo esc_html($l['name']); ?>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php
	return ob_get_clean();
}
