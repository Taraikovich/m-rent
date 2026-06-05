<?php

/**
 * Модальное окно «Оставьте заявку» (Figma 2269:2658 / 2353:4862 — форма,
 * 2269:2935 / 2353:4950 — success; короткая форма — 2459:4159).
 *
 * Подключается из `footer.php` — можно несколько раз с разными `$args`. Триггер
 * открытия — любая ссылка `href="#<id>"`, где `<id>` совпадает с DOM-id модалки
 * (см. src/booking-modal.js). После успешной отправки CF7 (`wpcf7mailsent`)
 * модалка переключается в состояние «Спасибо за доверие!» — обе верстки лежат
 * рядом и переключаются через `data-state` на корне `.mrent-booking-modal`.
 *
 * Параметры через `get_template_part('sections/common/booking-modal', null, [...])`:
 *   • `id`          — DOM-id модалки (по умолчанию `booking-form`; триггер `<a href="#booking-form">`).
 *   • `form_titles` — список названий CF7-форм в порядке приоритета. Первая
 *                     найденная используется; если ничего не найдено — модалка
 *                     не рендерится.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_args = is_array($args ?? null) ? $args : [];
$mrent_id   = isset($mrent_args['id']) && $mrent_args['id'] !== '' ? (string) $mrent_args['id'] : 'booking-form';
$mrent_form_titles = $mrent_args['form_titles'] ?? ['Заявка (модальное окно)', 'Бронирование автомобиля'];

$mrent_form_id = 0;
foreach ((array) $mrent_form_titles as $mrent_title) {
	$mrent_cf7_posts = get_posts([
		'post_type'   => 'wpcf7_contact_form',
		'post_status' => 'publish',
		'title'       => $mrent_title,
		'numberposts' => 1,
	]);
	if ($mrent_cf7_posts) {
		$mrent_form_id = (int) $mrent_cf7_posts[0]->ID;
		break;
	}
}

if (! $mrent_form_id) {
	return;
}

$mrent_phone     = mrent_options_get('contact_phone');
$mrent_phone_url = mrent_options_phone_url();
$mrent_email     = mrent_options_get('contact_email');
$mrent_email_url = mrent_options_email_url();
$mrent_socials    = mrent_options_socials();
$mrent_messengers = mrent_options_messengers();

?>

<?php /* Внешний контейнер — fixed, без собственного скролла; flex-центрирование карточки.
        Оверлей — absolute inset-0 (= viewport, потому что родитель fixed) и слушает клик
        для закрытия. Скроллится только сама карточка (`overflow-y-auto` + `max-h-full`),
        благодаря чему подложка остаётся на месте при длинной форме. */ ?>
<div
	id="<?php echo esc_attr($mrent_id); ?>"
	class="mrent-booking-modal fixed inset-0 z-[100] flex items-center justify-center p-[8px] xl:p-[50px]"
	data-state="form"
	role="dialog"
	aria-modal="true"
	aria-labelledby="<?php echo esc_attr($mrent_id); ?>-title"
	hidden>

	<?php /* Затемнение + блюр страницы под модалкой. Клик закрывает. */ ?>
	<div class="mrent-booking-modal__overlay absolute inset-0 bg-[rgba(30,29,31,0.6)] backdrop-blur-[12px]" data-booking-modal-close aria-hidden="true"></div>

	<div class="mrent-booking-modal__card relative w-full max-w-[860px] max-h-full overflow-y-auto bg-[rgba(30,29,31,0.95)] xl:bg-[rgba(30,29,31,0.8)] xl:backdrop-blur-[40px] rounded-[8px] p-[13px] xl:px-[50px] xl:py-[50px]">

		<?php /* ─── Состояние: форма ─── */ ?>
		<div class="mrent-booking-modal__form flex flex-col gap-[15px] xl:gap-[25px]">

			<?php /* Шапка: на мобайле — кнопка «Закрыть» сверху + заголовок ниже (по центру). На десктопе — заголовок слева, «Закрыть» справа в линию. */ ?>
			<div class="flex flex-col gap-[15px] xl:gap-0 xl:flex-row xl:items-start xl:justify-between">
				<a href="#" data-booking-modal-close class="order-1 xl:order-2 self-center xl:self-auto font-display font-medium text-mrent-yellow text-[12px] xl:text-[15px] leading-none underline underline-offset-2 hover:opacity-80 transition-opacity">
					<?php esc_html_e('Назад', 'm-rent'); ?>
				</a>
				<h2 id="<?php echo esc_attr($mrent_id); ?>-title" class="order-2 xl:order-1 font-display font-bold text-mrent-white text-[21px] xl:text-[50px] leading-[1.2] text-center xl:text-left">
					<?php esc_html_e('Оставьте заявку', 'm-rent'); ?>
				</h2>
			</div>

			<div class="mrent-consultation-form w-full">
				<?php echo do_shortcode('[contact-form-7 id="' . $mrent_form_id . '"]'); ?>
			</div>
		</div>

		<?php /* ─── Состояние: успешная отправка ─── */ ?>
		<div class="mrent-booking-modal__success flex flex-col items-center gap-[23px] xl:gap-[45px] text-center">

			<div class="flex flex-col items-center gap-[11px] xl:gap-[23px] text-mrent-white">
				<p class="font-display font-bold text-[21px] xl:text-[56px] leading-[1.2]">
					<?php esc_html_e('Спасибо за доверие!', 'm-rent'); ?>
				</p>
				<p class="font-display text-[12px] xl:text-[23px] leading-[1.2] xl:max-w-[561px]">
					<?php esc_html_e('Благодарим за обращение. В течение часа с вами свяжется персональный менеджер, чтобы согласовать все детали', 'm-rent'); ?>
				</p>
			</div>

			<?php if ($mrent_socials) : ?>
				<div class="flex flex-col items-center gap-[11px] xl:gap-[23px]">
					<p class="font-display font-[800] text-mrent-white text-[15px] xl:text-[26px] leading-[1.2]">
						<?php esc_html_e('Следите за нами в соцсетях', 'm-rent'); ?>
					</p>
					<div class="flex items-center gap-[10px]">
						<?php foreach ($mrent_socials as $mrent_slug => $mrent_url) : ?>
							<?php $mrent_custom_icon = mrent_options_social_icon($mrent_slug); ?>
							<a href="<?php echo esc_url($mrent_url); ?>" class="text-mrent-white inline-flex items-center justify-center size-[28px] xl:size-[38px] hover:opacity-80 transition-opacity shrink-0" aria-label="<?php echo esc_attr(ucfirst($mrent_slug)); ?>" target="_blank" rel="noopener noreferrer">
								<?php if ($mrent_custom_icon) : ?>
									<img src="<?php echo esc_url($mrent_custom_icon); ?>" alt="" class="size-full object-contain" width="50" height="50" loading="lazy" decoding="async">
								<?php else : ?>
									<?php echo mrent_icon($mrent_slug, ['class' => 'size-full']); ?>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($mrent_messengers) : ?>
				<div class="flex flex-col items-center gap-[11px] xl:gap-[23px]">
					<p class="font-display font-[800] text-mrent-white text-[15px] xl:text-[26px] leading-[1.2]">
						<?php esc_html_e('Свяжитесь с нами в мессенджерах', 'm-rent'); ?>
					</p>
					<div class="flex items-center gap-[10px]">
						<?php foreach ($mrent_messengers as $mrent_slug => $mrent_url) : ?>
							<?php $mrent_custom_icon = mrent_options_messenger_icon($mrent_slug); ?>
							<a href="<?php echo esc_url($mrent_url); ?>" class="text-mrent-white inline-flex items-center justify-center size-[28px] xl:size-[38px] hover:opacity-80 transition-opacity shrink-0" aria-label="<?php echo esc_attr(ucfirst($mrent_slug)); ?>" target="_blank" rel="noopener noreferrer">
								<?php if ($mrent_custom_icon) : ?>
									<img src="<?php echo esc_url($mrent_custom_icon); ?>" alt="" class="size-full object-contain" width="50" height="50" loading="lazy" decoding="async">
								<?php else : ?>
									<?php echo mrent_icon($mrent_slug, ['class' => 'size-full']); ?>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($mrent_phone !== '' || $mrent_email !== '') : ?>
				<div class="flex flex-col items-center gap-[11px] xl:gap-[23px] w-full">
					<p class="font-display font-[800] text-mrent-white text-[15px] xl:text-[26px] leading-[1.2]">
						<?php esc_html_e('Если возникли вопросы, свяжитесь с нами', 'm-rent'); ?>
					</p>
					<div class="flex flex-col xl:flex-row items-center justify-center gap-[8px] xl:gap-[23px]">
						<?php if ($mrent_phone !== '') : ?>
							<a href="<?php echo esc_url($mrent_phone_url); ?>" class="flex items-center gap-[11px] group">
								<span class="inline-flex items-center justify-center size-[15px] xl:size-[19px] shrink-0 text-mrent-yellow">
									<?php echo mrent_icon('phone', ['class' => 'size-full']); ?>
								</span>
								<span class="font-display text-mrent-white text-[12px] xl:text-[23px] leading-[1.2] group-hover:opacity-80 transition-opacity">
									<?php echo esc_html($mrent_phone); ?>
								</span>
							</a>
						<?php endif; ?>
						<?php if ($mrent_email !== '') : ?>
							<a href="<?php echo esc_url($mrent_email_url); ?>" class="flex items-center gap-[11px] group">
								<span class="inline-flex items-center justify-center size-[15px] xl:size-[19px] shrink-0 text-mrent-yellow">
									<?php echo mrent_icon('mail', ['width' => '20', 'height' => '17']); ?>
								</span>
								<span class="font-display text-mrent-white text-[12px] xl:text-[23px] leading-[1.2] group-hover:opacity-80 transition-opacity">
									<?php echo esc_html($mrent_email); ?>
								</span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<a href="#" data-booking-modal-close class="font-display font-medium text-mrent-yellow text-[12px] xl:text-[15px] leading-none underline underline-offset-2 hover:opacity-80 transition-opacity">
				<?php esc_html_e('Назад', 'm-rent'); ?>
			</a>
		</div>
	</div>
</div>