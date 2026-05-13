<?php

/**
 * Модальное окно «Оставьте отзыв».
 *
 * Подключается один раз — из `footer.php`. Открывается JS-ом при клике по
 * любой ссылке `href="#reviews-form"` (см. src/review-modal.js). После
 * успешной отправки CF7 (`wpcf7mailsent`) модалка переключается в состояние
 * «Спасибо за отзыв!». По структуре повторяет `booking-modal.php`, но без
 * блока контактов/соцсетей в success-стейте.
 *
 * Форма: CF7 «Отзыв (модальное окно)». Если форма не найдена — модалка
 * не рендерится.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_cf7_posts = get_posts([
	'post_type'   => 'wpcf7_contact_form',
	'post_status' => 'publish',
	'title'       => 'Отзыв (модальное окно)',
	'numberposts' => 1,
]);
$mrent_form_id = $mrent_cf7_posts ? (int) $mrent_cf7_posts[0]->ID : 0;

if (! $mrent_form_id) {
	return;
}
?>

<div
	id="review-modal"
	class="mrent-booking-modal fixed inset-0 z-[100] flex items-center justify-center p-[15px] xl:p-[100px]"
	data-state="form"
	role="dialog"
	aria-modal="true"
	aria-labelledby="review-modal-title"
	hidden>

	<div class="mrent-booking-modal__overlay absolute inset-0 bg-[rgba(30,29,31,0.6)] backdrop-blur-[12px]" data-review-modal-close aria-hidden="true"></div>

	<div class="mrent-booking-modal__card relative w-full max-w-[1720px] max-h-full overflow-y-auto bg-[rgba(30,29,31,0.95)] xl:bg-[rgba(30,29,31,0.8)] xl:backdrop-blur-[80px] rounded-[15px] p-[25px] xl:px-[100px] xl:py-[100px]">

		<?php /* ─── Состояние: форма ─── */ ?>
		<div class="mrent-booking-modal__form flex flex-col gap-[30px] xl:gap-[50px]">

			<div class="flex flex-col gap-[30px] xl:gap-0 xl:flex-row xl:items-start xl:justify-between">
				<a href="#" data-review-modal-close class="order-1 xl:order-2 self-center xl:self-auto font-display font-medium text-mrent-yellow text-[18px] xl:text-[30px] leading-none underline underline-offset-2 hover:opacity-80 transition-opacity">
					<?php esc_html_e('Закрыть', 'm-rent'); ?>
				</a>
				<h2 id="review-modal-title" class="order-2 xl:order-1 font-display font-bold text-mrent-white text-[28px] xl:text-[74px] leading-[1.2] text-center xl:text-left">
					<?php esc_html_e('Оставьте отзыв', 'm-rent'); ?>
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
					<?php esc_html_e('Спасибо за отзыв!', 'm-rent'); ?>
				</p>
				<p class="font-display text-[16px] xl:text-[30px] leading-[1.2] xl:max-w-[748px]">
					<?php esc_html_e('Мы опубликуем ваш отзыв после модерации. Спасибо, что делитесь впечатлениями.', 'm-rent'); ?>
				</p>
			</div>

			<a href="#" data-review-modal-close class="font-display font-medium text-mrent-yellow text-[18px] xl:text-[30px] leading-none underline underline-offset-2 hover:opacity-80 transition-opacity">
				<?php esc_html_e('Закрыть', 'm-rent'); ?>
			</a>
		</div>
	</div>
</div>
