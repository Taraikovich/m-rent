<?php

/**
 * Модальное окно «Оставьте заявку» (Figma 2269:2658 / 2353:4862 — форма,
 * 2269:2935 / 2353:4950 — success).
 *
 * Подключается один раз — из `footer.php`. Открывается JS-ом при клике по
 * любой ссылке `href="#booking-form"` (см. src/booking-modal.js). После
 * успешной отправки CF7 (`wpcf7mailsent`) модалка переключается в состояние
 * «Спасибо за доверие!» — обе верстки лежат рядом и переключаются через
 * `data-state` на корне `.mrent-booking-modal`.
 *
 * Форма: CF7 «Заявка (модальное окно)». Ищется по точному названию, fallback
 * на «Бронирование автомобиля» (та же схема полей). Если ни одной формы нет —
 * модалку рендерить бессмысленно: выходим, чтобы не было пустого диалога.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_cf7_posts = get_posts([
	'post_type'   => 'wpcf7_contact_form',
	'post_status' => 'publish',
	'title'       => 'Заявка (модальное окно)',
	'numberposts' => 1,
]);
$mrent_form_id = $mrent_cf7_posts ? (int) $mrent_cf7_posts[0]->ID : 0;

if (! $mrent_form_id) {
	$mrent_cf7_posts = get_posts([
		'post_type'   => 'wpcf7_contact_form',
		'post_status' => 'publish',
		'title'       => 'Бронирование автомобиля',
		'numberposts' => 1,
	]);
	$mrent_form_id = $mrent_cf7_posts ? (int) $mrent_cf7_posts[0]->ID : 0;
}

if (! $mrent_form_id) {
	return;
}

$mrent_phone     = mrent_options_get('contact_phone');
$mrent_phone_url = mrent_options_phone_url();
$mrent_email     = mrent_options_get('contact_email');
$mrent_email_url = mrent_options_email_url();
$mrent_socials   = mrent_options_socials();

$mrent_icons_url = get_template_directory_uri() . '/assets/icons/';

/* Соц-иконки success-стейта: Instagram, YouTube, TikTok, Telegram-канал
   (тот же набор и порядок, что в `sections/common/contact.php`). */
$mrent_social_icons = [
	'instagram' => 'contact-instagram.png',
	'youtube'   => 'contact-youtube.png',
	'tiktok'    => 'contact-tiktok.png',
	'telegram'  => 'contact-telegram-channel.png',
];
?>

<?php /* Внешний контейнер — fixed, без собственного скролла; flex-центрирование карточки.
        Оверлей — absolute inset-0 (= viewport, потому что родитель fixed) и слушает клик
        для закрытия. Скроллится только сама карточка (`overflow-y-auto` + `max-h-full`),
        благодаря чему подложка остаётся на месте при длинной форме. */ ?>
<div
	id="booking-modal"
	class="mrent-booking-modal fixed inset-0 z-[100] flex items-center justify-center p-[15px] xl:p-[100px]"
	data-state="form"
	role="dialog"
	aria-modal="true"
	aria-labelledby="booking-modal-title"
	hidden>

	<?php /* Затемнение + блюр страницы под модалкой. Клик закрывает. */ ?>
	<div class="mrent-booking-modal__overlay absolute inset-0 bg-[rgba(30,29,31,0.6)] backdrop-blur-[12px]" data-booking-modal-close aria-hidden="true"></div>

	<div class="mrent-booking-modal__card relative w-full max-w-[1720px] max-h-full overflow-y-auto bg-[rgba(30,29,31,0.95)] xl:bg-[rgba(30,29,31,0.8)] xl:backdrop-blur-[80px] rounded-[15px] p-[25px] xl:px-[100px] xl:py-[100px]">

			<?php /* ─── Состояние: форма ─── */ ?>
			<div class="mrent-booking-modal__form flex flex-col gap-[30px] xl:gap-[50px]">

				<?php /* Шапка: на мобайле — кнопка «Закрыть» сверху + заголовок ниже (по центру). На десктопе — заголовок слева, «Закрыть» справа в линию. */ ?>
				<div class="flex flex-col gap-[30px] xl:gap-0 xl:flex-row xl:items-start xl:justify-between">
					<a href="#" data-booking-modal-close class="order-1 xl:order-2 self-center xl:self-auto font-display font-medium text-mrent-yellow text-[18px] xl:text-[30px] leading-none underline underline-offset-2 hover:opacity-80 transition-opacity">
						<?php esc_html_e('Закрыть', 'm-rent'); ?>
					</a>
					<h2 id="booking-modal-title" class="order-2 xl:order-1 font-display font-bold text-mrent-white text-[28px] xl:text-[74px] leading-[1.2] text-center xl:text-left">
						<?php esc_html_e('Оставьте заявку', 'm-rent'); ?>
					</h2>
				</div>

				<div class="mrent-consultation-form w-full">
					<?php echo do_shortcode('[contact-form-7 id="' . $mrent_form_id . '"]'); ?>
				</div>
			</div>

			<?php /* ─── Состояние: успешная отправка ─── */ ?>
			<div class="mrent-booking-modal__success flex flex-col items-center gap-[30px] xl:gap-[60px] text-center">

				<div class="flex flex-col items-center gap-[15px] xl:gap-[30px] text-mrent-white">
					<p class="font-display font-bold text-[28px] xl:text-[74px] leading-[1.2]">
						<?php esc_html_e('Спасибо за доверие!', 'm-rent'); ?>
					</p>
					<p class="font-display text-[16px] xl:text-[30px] leading-[1.2] xl:max-w-[748px]">
						<?php esc_html_e('Благодарим за обращение. В течение часа с вами свяжется персональный менеджер, чтобы согласовать все детали', 'm-rent'); ?>
					</p>
				</div>

				<?php
				$mrent_success_socials = [];
				foreach ($mrent_social_icons as $mrent_slug => $mrent_file) {
					$mrent_url = $mrent_socials[$mrent_slug] ?? '';
					if ($mrent_url !== '') {
						$mrent_success_socials[$mrent_slug] = ['url' => $mrent_url, 'file' => $mrent_file];
					}
				}
				?>
				<?php if ($mrent_success_socials) : ?>
					<div class="flex flex-col items-center gap-[15px] xl:gap-[30px]">
						<p class="font-display font-[800] text-mrent-white text-[20px] xl:text-[35px] leading-[1.2]">
							<?php esc_html_e('Следите за нами в соцсетях', 'm-rent'); ?>
						</p>
						<div class="flex items-center gap-[10px]">
							<?php foreach ($mrent_success_socials as $mrent_slug => $mrent_data) : ?>
								<a href="<?php echo esc_url($mrent_data['url']); ?>" class="block size-[37px] xl:size-[50px] hover:opacity-80 transition-opacity shrink-0" aria-label="<?php echo esc_attr(ucfirst($mrent_slug)); ?>" target="_blank" rel="noopener noreferrer">
									<img src="<?php echo esc_url($mrent_icons_url . $mrent_data['file']); ?>" alt="" class="size-full object-contain" width="50" height="50" loading="lazy" decoding="async">
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($mrent_phone !== '' || $mrent_email !== '') : ?>
					<div class="flex flex-col items-center gap-[15px] xl:gap-[30px] w-full">
						<p class="font-display font-[800] text-mrent-white text-[20px] xl:text-[35px] leading-[1.2]">
							<?php esc_html_e('Если возникли вопросы, свяжитесь с нами', 'm-rent'); ?>
						</p>
						<div class="flex flex-col xl:flex-row items-center justify-center gap-[10px] xl:gap-[30px]">
							<?php if ($mrent_phone !== '') : ?>
								<a href="<?php echo esc_url($mrent_phone_url); ?>" class="flex items-center gap-[15px] group">
									<span class="inline-flex items-center justify-center size-[20px] xl:size-[25px] shrink-0 text-mrent-yellow">
										<?php echo mrent_icon('phone', ['width' => '14', 'height' => '20']); ?>
									</span>
									<span class="font-display text-mrent-white text-[16px] xl:text-[30px] leading-[1.2] group-hover:opacity-80 transition-opacity">
										<?php echo esc_html($mrent_phone); ?>
									</span>
								</a>
							<?php endif; ?>
							<?php if ($mrent_email !== '') : ?>
								<a href="<?php echo esc_url($mrent_email_url); ?>" class="flex items-center gap-[15px] group">
									<span class="inline-flex items-center justify-center size-[20px] xl:size-[25px] shrink-0 text-mrent-yellow">
										<?php echo mrent_icon('mail', ['width' => '20', 'height' => '17']); ?>
									</span>
									<span class="font-display text-mrent-white text-[16px] xl:text-[30px] leading-[1.2] group-hover:opacity-80 transition-opacity">
										<?php echo esc_html($mrent_email); ?>
									</span>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<a href="#" data-booking-modal-close class="font-display font-medium text-mrent-yellow text-[18px] xl:text-[30px] leading-none underline underline-offset-2 hover:opacity-80 transition-opacity">
					<?php esc_html_e('Закрыть', 'm-rent'); ?>
				</a>
			</div>
		</div>
</div>
