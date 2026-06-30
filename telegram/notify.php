<?php

/**
 * Пересылка заявок Contact Form 7 в Telegram-канал.
 *
 * Это не «живой» бот (нет polling/webhook) — при каждом успешном сабмите формы
 * PHP делает один HTTPS-запрос к Telegram Bot API `sendMessage`. Покрывает все
 * CF7-формы темы (бронирование, консультация, короткие заявки, контактная,
 * отзыв) одним обработчиком через карту «поле → подпись».
 *
 * Криды читаются из окружения (проброшены в контейнер из `.env` через
 * docker-compose): `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`. Если пусто —
 * интеграция молча отключается (с одной записью в debug.log).
 *
 * Подготовка: создать бота у @BotFather, добавить его админом в канал, узнать
 * chat_id (числовой -100… для приватного / @username для публичного).
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Достаёт значение конфига источник-независимо. Порядок:
 *   1. PHP-константа из wp-config.php — основной способ на обычном хостинге
 *      (ispmanager и т.п.), где нет проброса окружения. `.php` исполняется,
 *      а не отдаётся как текст, поэтому секрет не утекает по URL.
 *   2. Переменная окружения — её прокидывает docker-compose в dev.
 * Если нигде не задано — пустая строка (интеграция молча отключается).
 */
function mrent_tg_cfg(string $key): string {
	if (defined($key) && constant($key) !== '') {
		return (string) constant($key);
	}
	$env = getenv($key);
	return $env !== false ? (string) $env : '';
}

/** Токен бота. */
function mrent_tg_token(): string {
	return mrent_tg_cfg('TELEGRAM_BOT_TOKEN');
}

/** ID канала. */
function mrent_tg_chat(): string {
	return mrent_tg_cfg('TELEGRAM_CHAT_ID');
}

/**
 * Карта полей CF7 → человекочитаемые подписи. Покрывает все формы темы:
 * поля, которых в конкретной форме нет, просто пропускаются. Порядок ключей
 * задаёт порядок строк в сообщении. Поле согласия `acceptance-consent` и любые
 * неизвестные поля намеренно не выводим.
 *
 * @return array<string,string>
 */
function mrent_tg_field_labels(): array {
	return [
		'your-name'       => 'Имя',
		'your-phone'      => 'Телефон',
		'your-email'      => 'Email',
		'your-subject'    => 'Тема',
		'contact-method'  => 'Способ связи',
		'rent-start'      => 'Начало аренды',
		'rent-end'        => 'Конец аренды',
		'your-profession' => 'Профессия',
		'your-car'        => 'Авто',
		'your-rating'     => 'Оценка',
		'your-review'     => 'Отзыв',
		'your-message'    => 'Сообщение',
	];
}

/**
 * Экранирует значение для Telegram parse_mode=HTML (только < > &).
 */
function mrent_tg_esc(string $value): string {
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Приводит значение поля CF7 (строка или массив, напр. чекбоксы/мультиселект)
 * к одной строке.
 *
 * @param mixed $value
 */
function mrent_tg_stringify($value): string {
	if (is_array($value)) {
		$value = implode(', ', array_map('strval', $value));
	}
	return trim((string) $value);
}

/**
 * Хук CF7 после валидации, до отправки письма. Выбран вместо `wpcf7_mail_sent`,
 * чтобы доставка в Telegram не зависела от успешности SMTP.
 *
 * @param WPCF7_ContactForm $contact_form
 */
function mrent_tg_notify($contact_form): void {
	$token = mrent_tg_token();
	$chat  = mrent_tg_chat();

	if ($token === '' || $chat === '') {
		error_log('[mrent_tg] TELEGRAM_BOT_TOKEN/TELEGRAM_CHAT_ID не заданы — пропуск.');
		return;
	}

	if (! class_exists('WPCF7_Submission')) {
		return;
	}
	$submission = WPCF7_Submission::get_instance();
	if (! $submission) {
		return;
	}
	$data = $submission->get_posted_data();

	$title = method_exists($contact_form, 'title') ? (string) $contact_form->title() : '';

	$lines = [];
	$lines[] = '🚗 <b>Новая заявка</b>' . ($title !== '' ? ' — ' . mrent_tg_esc($title) : '');
	$lines[] = '';

	foreach (mrent_tg_field_labels() as $field => $label) {
		if (! isset($data[$field])) {
			continue;
		}
		$value = mrent_tg_stringify($data[$field]);
		if ($value === '') {
			continue;
		}
		$lines[] = '<b>' . mrent_tg_esc($label) . ':</b> ' . mrent_tg_esc($value);
	}

	$source = mrent_tg_stringify($submission->get_meta('url'));
	if ($source !== '') {
		$lines[] = '';
		$lines[] = '🔗 ' . mrent_tg_esc($source);
	}

	$text = implode("\n", $lines);

	$response = wp_remote_post('https://api.telegram.org/bot' . $token . '/sendMessage', [
		'timeout' => 8,
		'body'    => [
			'chat_id'                  => $chat,
			'text'                     => $text,
			'parse_mode'               => 'HTML',
			'disable_web_page_preview' => true,
		],
	]);

	if (is_wp_error($response)) {
		error_log('[mrent_tg] send failed: ' . $response->get_error_message());
		return;
	}

	$code = (int) wp_remote_retrieve_response_code($response);
	if ($code !== 200) {
		error_log('[mrent_tg] Telegram HTTP ' . $code . ': ' . wp_remote_retrieve_body($response));
	}
}

add_action('wpcf7_before_send_mail', 'mrent_tg_notify');
