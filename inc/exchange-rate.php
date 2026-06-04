<?php

/**
 * Курс USD → BYN.
 *
 * Раз в 5 часов wp_cron тянет официальный курс НБРБ (ISO-код 431) и кладёт в
 * wp_options. Админ-страница «Курс валют» показывает курс, дату обновления и
 * позволяет задать наценку в %. Хелпер `mrent_usd_to_byn()` используется в
 * шаблонах для пересчёта цен из USD в BYN с наценкой.
 *
 * Опции:
 *   • mrent_usd_rate         — float, BYN за 1 USD (учтён Cur_Scale)
 *   • mrent_rub_rate         — float, BYN за 1 RUB (учтён Cur_Scale)
 *   • mrent_usd_rate_updated — int,   unix-timestamp последнего успеха
 *   • mrent_usd_markup       — float, наценка в процентах
 */

if (! defined('ABSPATH')) {
	exit;
}

const MRENT_EXCHANGE_HOOK        = 'mrent_update_usd_rate';
const MRENT_EXCHANGE_SCHEDULE    = 'mrent_every_5h';
const MRENT_EXCHANGE_API_URL_USD = 'https://api.nbrb.by/exrates/rates/431';
const MRENT_EXCHANGE_API_URL_RUB = 'https://api.nbrb.by/exrates/rates/456';

/* ---------- cron ---------- */

add_filter('cron_schedules', function (array $schedules): array {
	$schedules[MRENT_EXCHANGE_SCHEDULE] = [
		'interval' => 5 * HOUR_IN_SECONDS,
		'display'  => __('Каждые 5 часов', 'm-rent'),
	];
	return $schedules;
});

function mrent_exchange_schedule_cron(): void
{
	if (! wp_next_scheduled(MRENT_EXCHANGE_HOOK)) {
		wp_schedule_event(time() + 60, MRENT_EXCHANGE_SCHEDULE, MRENT_EXCHANGE_HOOK);
	}
}
add_action('after_switch_theme', 'mrent_exchange_schedule_cron');
add_action('init', 'mrent_exchange_schedule_cron');

add_action('switch_theme', function (): void {
	wp_clear_scheduled_hook(MRENT_EXCHANGE_HOOK);
});

add_action(MRENT_EXCHANGE_HOOK, 'mrent_exchange_fetch_and_store');

/* ---------- fetch ---------- */

/**
 * Тянет курс одной валюты с НБРБ. Возвращает BYN за 1 единицу (с учётом
 * Cur_Scale) или null при любой ошибке.
 */
function mrent_exchange_fetch_rate(string $url): ?float
{
	$response = wp_remote_get($url, [
		'timeout' => 10,
		'headers' => ['Accept' => 'application/json'],
	]);

	if (is_wp_error($response)) {
		error_log('[mrent_exchange] fetch failed: ' . $response->get_error_message());
		return null;
	}

	$code = wp_remote_retrieve_response_code($response);
	if ($code !== 200) {
		error_log('[mrent_exchange] HTTP ' . $code);
		return null;
	}

	$body = json_decode(wp_remote_retrieve_body($response), true);
	if (! is_array($body) || ! isset($body['Cur_OfficialRate'], $body['Cur_Scale'])) {
		error_log('[mrent_exchange] unexpected payload: ' . wp_remote_retrieve_body($response));
		return null;
	}

	$scale = (float) $body['Cur_Scale'];
	if ($scale <= 0) {
		return null;
	}

	$rate = (float) $body['Cur_OfficialRate'] / $scale;
	if ($rate <= 0) {
		return null;
	}

	return $rate;
}

/**
 * Тянет курсы USD и RUB с НБРБ и сохраняет в опции. Возвращает true, если
 * получен основной курс (USD); курс RUB обновляется независимо при наличии.
 */
function mrent_exchange_fetch_and_store(): bool
{
	$usd = mrent_exchange_fetch_rate(MRENT_EXCHANGE_API_URL_USD);
	$rub = mrent_exchange_fetch_rate(MRENT_EXCHANGE_API_URL_RUB);

	if ($rub !== null) {
		update_option('mrent_rub_rate', $rub);
	}

	if ($usd === null) {
		return false;
	}

	update_option('mrent_usd_rate', $usd);
	update_option('mrent_usd_rate_updated', time());
	return true;
}

/* ---------- helpers ---------- */

function mrent_usd_rate(): float
{
	return (float) get_option('mrent_usd_rate', 0);
}

function mrent_rub_rate(): float
{
	return (float) get_option('mrent_rub_rate', 0);
}

function mrent_usd_rate_updated(): int
{
	return (int) get_option('mrent_usd_rate_updated', 0);
}

function mrent_usd_markup(): float
{
	return (float) get_option('mrent_usd_markup', 0);
}

/**
 * Пересчёт USD → BYN с применением наценки. 0.0 если курс ещё не получен.
 */
function mrent_usd_to_byn(float $usd): float
{
	$rate = mrent_usd_rate();
	if ($rate <= 0) {
		return 0.0;
	}
	return $usd * $rate * (1 + mrent_usd_markup() / 100);
}

/**
 * Готовая строка цены в исходных долларах вида «$120».
 * $suffix добавляется в конец без пробела: передайте '/сутки' для «$120/сутки».
 */
function mrent_usd_price(float $usd, string $suffix = ''): string
{
	return '$' . mrent_format_price($usd) . $suffix;
}

/**
 * Готовая строка вида «335.4 руб.» — округляется до десятых.
 * $suffix добавляется в конец без пробела: передайте '/сутки' для «335.4 руб./сутки».
 */
function mrent_byn_price(float $usd, string $suffix = ''): string
{
	$byn = round(mrent_usd_to_byn($usd), -1);
	return mrent_format_price($byn) . ' ' . __('руб.', 'm-rent') . $suffix;
}

/**
 * Пересчёт USD → RUB (российский рубль) с применением той же наценки.
 * Считается через BYN: USD→BYN по курсу НБРБ, затем BYN→RUB.
 * 0.0 если какой-либо из курсов ещё не получен.
 */
function mrent_usd_to_rub(float $usd): float
{
	$rubRate = mrent_rub_rate();
	if ($rubRate <= 0) {
		return 0.0;
	}
	$byn = mrent_usd_to_byn($usd);
	if ($byn <= 0) {
		return 0.0;
	}
	return $byn / $rubRate;
}

/**
 * Готовая строка вида «3 350 ₽» — округляется до десятков.
 * $suffix добавляется в конец без пробела: передайте '/сутки' для «3 350 ₽/сутки».
 */
function mrent_rub_price(float $usd, string $suffix = ''): string
{
	$rub = round(mrent_usd_to_rub($usd), -1);
	return mrent_format_price($rub) . ' ' . __('₽', 'm-rent') . $suffix;
}

/**
 * Конвертация цен прямо в произвольном тексте/HTML (WYSIWYG-поля ACF).
 * Ищет последовательности вида «120$», «99.5$», «1 200$», «120 $» и заменяет
 * каждую на результат $formatter($usd). Остальной HTML не трогает.
 */
function mrent_convert_usd_in_text_with(string $html, callable $formatter): string
{
	return (string) preg_replace_callback(
		'/(\d{1,3}(?:[ \xC2\xA0]\d{3})*|\d+)(?:[.,](\d+))?\s*\$/u',
		static function (array $m) use ($formatter): string {
			$int  = preg_replace('/[ \xC2\xA0]/u', '', $m[1]);
			$frac = $m[2] ?? '';
			$usd  = (float) ($frac !== '' ? $int . '.' . $frac : $int);
			return (string) $formatter($usd);
		},
		$html
	);
}

/**
 * Конвертация USD-цен в тексте в BYN через mrent_byn_price().
 * Пример: «Цена 120$ в сутки» → «Цена 335 руб. в сутки».
 */
function mrent_convert_usd_in_text(string $html): string
{
	return mrent_convert_usd_in_text_with($html, 'mrent_byn_price');
}

/**
 * Конвертация USD-цен в тексте в российские рубли через mrent_rub_price().
 * Пример: «Цена 120$ в сутки» → «Цена 9 000 ₽ в сутки».
 */
function mrent_convert_usd_in_text_rub(string $html): string
{
	return mrent_convert_usd_in_text_with($html, 'mrent_rub_price');
}

/**
 * Нормализация USD-цен в тексте к единому виду «$120» через mrent_usd_price().
 * Пример: «Цена 120$ в сутки» → «Цена $120 в сутки».
 */
function mrent_convert_usd_in_text_usd(string $html): string
{
	return mrent_convert_usd_in_text_with($html, 'mrent_usd_price');
}

/* ---------- admin page ---------- */

add_action('admin_menu', function (): void {
	add_menu_page(
		__('Курс валют', 'm-rent'),
		__('Курс валют', 'm-rent'),
		'manage_options',
		'mrent-exchange',
		'mrent_exchange_render_page',
		'dashicons-money-alt',
		31
	);
});

function mrent_exchange_render_page(): void
{
	if (! current_user_can('manage_options')) {
		return;
	}

	$rate    = mrent_usd_rate();
	$rubRate = mrent_rub_rate();
	$updated = mrent_usd_rate_updated();
	$markup  = mrent_usd_markup();
	$final   = $rate * (1 + $markup / 100);
	// USD → RUB с наценкой (для наглядности): сколько RUB за 1 USD.
	$usdRub  = $rubRate > 0 ? $final / $rubRate : 0.0;

	$notice = '';
	if (isset($_GET['updated'])) {
		$notice = __('Сохранено.', 'm-rent');
	} elseif (isset($_GET['refreshed'])) {
		$notice = __('Курс обновлён.', 'm-rent');
	} elseif (isset($_GET['error'])) {
		$notice = __('Не удалось получить курс с НБРБ. Подробности в debug.log.', 'm-rent');
	}
?>
	<div class="wrap">
		<h1><?php esc_html_e('Курс валют', 'm-rent'); ?></h1>

		<?php if ($notice !== '') : ?>
			<div class="notice <?php echo isset($_GET['error']) ? 'notice-error' : 'notice-success'; ?> is-dismissible">
				<p><?php echo esc_html($notice); ?></p>
			</div>
		<?php endif; ?>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th><?php esc_html_e('Курс НБРБ (BYN за 1 USD)', 'm-rent'); ?></th>
					<td><strong><?php echo $rate > 0 ? esc_html(number_format($rate, 4, '.', ' ')) : '—'; ?></strong></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Курс НБРБ (BYN за 1 RUB)', 'm-rent'); ?></th>
					<td><strong><?php echo $rubRate > 0 ? esc_html(number_format($rubRate, 4, '.', ' ')) : '—'; ?></strong></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Последнее обновление', 'm-rent'); ?></th>
					<td><?php echo $updated > 0 ? esc_html(wp_date('d.m.Y H:i', $updated)) : '—'; ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Курс с наценкой', 'm-rent'); ?></th>
					<td><strong><?php echo $rate > 0 ? esc_html(number_format($final, 4, '.', ' ')) : '—'; ?></strong></td>
				</tr>
				<tr>
					<th><?php esc_html_e('1 USD в RUB (с наценкой)', 'm-rent'); ?></th>
					<td><strong><?php echo $usdRub > 0 ? esc_html(number_format($usdRub, 2, '.', ' ')) : '—'; ?></strong></td>
				</tr>
			</tbody>
		</table>

		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<?php wp_nonce_field('mrent_exchange'); ?>
			<input type="hidden" name="action" value="mrent_exchange">

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th><label for="mrent_usd_markup"><?php esc_html_e('Наценка, %', 'm-rent'); ?></label></th>
						<td>
							<input type="number" step="0.01" min="0" name="markup" id="mrent_usd_markup"
								value="<?php echo esc_attr((string) $markup); ?>" class="regular-text">
						</td>
					</tr>
				</tbody>
			</table>

			<p class="submit">
				<button type="submit" name="op" value="save" class="button button-primary">
					<?php esc_html_e('Сохранить наценку', 'm-rent'); ?>
				</button>
				<button type="submit" name="op" value="refresh" class="button">
					<?php esc_html_e('Обновить курс сейчас', 'm-rent'); ?>
				</button>
			</p>
		</form>
	</div>
<?php
}

add_action('admin_post_mrent_exchange', function (): void {
	if (! current_user_can('manage_options')) {
		wp_die(__('Недостаточно прав.', 'm-rent'));
	}
	check_admin_referer('mrent_exchange');

	$op       = isset($_POST['op']) ? sanitize_key($_POST['op']) : '';
	$redirect = admin_url('admin.php?page=mrent-exchange');

	if ($op === 'save') {
		$markup = isset($_POST['markup']) ? (float) $_POST['markup'] : 0.0;
		if ($markup < 0) {
			$markup = 0.0;
		}
		update_option('mrent_usd_markup', $markup);
		wp_safe_redirect(add_query_arg('updated', '1', $redirect));
		exit;
	}

	if ($op === 'refresh') {
		$ok = mrent_exchange_fetch_and_store();
		wp_safe_redirect(add_query_arg($ok ? 'refreshed' : 'error', '1', $redirect));
		exit;
	}

	wp_safe_redirect($redirect);
	exit;
});
