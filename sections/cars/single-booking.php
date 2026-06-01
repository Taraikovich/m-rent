<?php

/**
 * Single car: секция «Забронировать автомобиль» + Contact Form 7.
 *
 * Figma: desktop 2326:4144 / mobile 2326:4277.
 *
 * Раскладка:
 *   Desktop (xl+, max-w-1720): 2 колонки items-center justify-between.
 *     Колонки тянутся флексом (basis 639 / 845, grow+shrink, min-w-0) —
 *     до 1720px форма плавно ужимается, а не ломает раскладку фиксом w-845.
 *     Левая (h-full justify-between):
 *       Заголовок H1 74px Bold (w-577) + лид H3 30px Book (w-639)
 *       ─── alternatives (gap-40)
 *         «Либо свяжитесь…» 30px Bold (w-412)
 *         Телефон row (icon 20×30 + 30px text) и социалки 4×55px стек gap-30
 *         Subscribe-links flex-row gap-30, каждая = текст 30px + icon 30×30
 *     Правая: CF7 форма w-845 (стили `.mrent-consultation-form`).
 *   Mobile: 1 колонка gap-30. Заголовок 28px Bold (без лида).
 *     «Либо свяжитесь…» 20px Heavy, телефон row 16px (icon 13×20), социалки 40×40.
 *     Subscribe-links flex-col gap-10, текст 18px + icon 20×20. Форма full-width.
 *
 * CF7-форма: ACF `car_single_booking_form_id` → fallback на CF7 с заголовком
 * «Бронирование автомобиля» → первая доступная форма. Если ни одной формы нет —
 * секция не выводится.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_form_id = (int) get_field('car_single_booking_form_id');

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
	$mrent_cf7_any = get_posts([
		'post_type'   => 'wpcf7_contact_form',
		'post_status' => 'publish',
		'numberposts' => 1,
	]);
	$mrent_form_id = $mrent_cf7_any ? (int) $mrent_cf7_any[0]->ID : 0;
}

if (! $mrent_form_id) {
	return;
}

$mrent_messengers   = mrent_options_messengers();
$mrent_socials      = mrent_options_socials();
$mrent_phone        = mrent_options_get('contact_phone');
$mrent_phone_url    = mrent_options_phone_url();
$mrent_subscribe_ig = $mrent_socials['instagram'] ?? '';
$mrent_subscribe_tg = $mrent_socials['telegram'] ?? '';

$mrent_icons_url = get_template_directory_uri() . '/assets/icons/';

/* Иконки в ряду «Социалки» — порядок и файлы как в car-import/consultation. */
$mrent_messenger_icons = [
	'telegram'  => 'contact-telegram-chat.png',
	'whatsapp'  => 'contact-whatsapp.png',
	'viber'     => 'contact-viber.png',
	'messenger' => 'contact-messenger.png',
];
?>

<section id="booking" class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:my-[100px] xl:min-h-[calc(100vh-100px)] xl:flex xl:items-center">
	<div class="w-full max-w-[1720px] mx-auto flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] xl:gap-[40px] min-w-0">

		<?php /* Левая колонка: заголовок + контакты. На desktop тянется на всю высоту правой колонки и распределяется justify-between. */ ?>
		<div class="flex flex-col xl:grow xl:shrink xl:basis-[639px] xl:max-w-[639px] xl:min-w-0 xl:justify-between xl:self-stretch gap-[30px] xl:gap-0 xl:pb-8.75">

			<?php /* Заголовок (+ лид только на desktop). */ ?>
			<div class="flex flex-col gap-[20px] text-mrent-white leading-[1.2]">
				<h2 class="font-display font-bold text-[clamp(24px,17.69px+1.68vw,50px)] xl:max-w-[577px]">
					<?php esc_html_e('Забронировать автомобиль', 'm-rent'); ?>
				</h2>
				<p class="font-display text-[clamp(16px,15.03px+0.26vw,20px)] xl:max-w-[639px] hidden xl:block">
					<?php esc_html_e('Заполните форму, и наш специалист свяжется с вами, чтобы подробно проконсультировать и предложить оптимальное решение для ваших задач', 'm-rent'); ?>
				</p>
			</div>

			<?php /* Альтернативные способы связи. */ ?>
			<div class="flex flex-col gap-[20px] xl:gap-[40px]">

				<div class="flex flex-col gap-[15px] xl:gap-[30px]">
					<p class="font-display font-black xl:font-bold text-[clamp(16px,12.60px+0.91vw,30px)] text-mrent-white leading-[1.24] xl:w-[412px]">
						<?php esc_html_e('Либо свяжитесь с нами удобным для вас способом', 'm-rent'); ?>
					</p>

					<?php /* Стек: телефон, ниже — иконки мессенджеров. */ ?>
					<div class="flex flex-col gap-[20px] xl:gap-[30px]">

						<?php if ($mrent_phone !== '') : ?>
							<a href="<?php echo esc_url($mrent_phone_url); ?>" class="flex items-center gap-[15px] group self-start">
								<img
									src="<?php echo esc_url($mrent_icons_url . 'phone.svg'); ?>"
									alt=""
									class="w-[13px] h-[20px] xl:w-[20px] xl:h-[30px] shrink-0 object-contain"
									width="20" height="30"
									loading="lazy" decoding="async" aria-hidden="true">
								<span class="font-display text-[clamp(14px,12.54px+0.39vw,20px)] text-mrent-white leading-[1.2] whitespace-nowrap group-hover:opacity-80 transition-opacity">
									<?php echo esc_html($mrent_phone); ?>
								</span>
							</a>
						<?php endif; ?>

						<?php if ($mrent_messengers) : ?>
							<div class="flex items-center gap-[10px] xl:gap-[18.75px]">
								<?php foreach ($mrent_messengers as $mrent_slug => $mrent_url) : ?>
									<?php $mrent_file = $mrent_messenger_icons[$mrent_slug] ?? null; ?>
									<?php if (! $mrent_file || $mrent_url === '') continue; ?>
									<a
										href="<?php echo esc_url($mrent_url); ?>"
										class="block size-[40px] xl:size-[55px] hover:opacity-80 transition-opacity shrink-0"
										aria-label="<?php echo esc_attr(ucfirst($mrent_slug)); ?>"
										target="_blank" rel="noopener noreferrer">
										<img
											src="<?php echo esc_url($mrent_icons_url . $mrent_file); ?>"
											alt=""
											class="size-full object-contain"
											width="55" height="55"
											loading="lazy" decoding="async">
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					</div>
				</div>

				<?php /* «Подписаться» ссылки: текст + brand-иконка. На mobile колонкой, на desktop в ряд. */ ?>
				<?php if ($mrent_subscribe_ig || $mrent_subscribe_tg) : ?>
					<div class="flex flex-col xl:flex-row gap-[10px] xl:gap-[30px] xl:items-center">
						<?php if ($mrent_subscribe_ig) : ?>
							<a href="<?php echo esc_url($mrent_subscribe_ig); ?>" class="inline-flex items-center gap-[10px] xl:gap-[15px] self-start hover:opacity-80 transition-opacity" target="_blank" rel="noopener noreferrer">
								<span class="font-display font-medium text-[clamp(14px,13.03px+0.26vw,18px)] text-mrent-white underline underline-offset-2">
									<?php esc_html_e('Подписаться в Instagram', 'm-rent'); ?>
								</span>
								<img
									src="<?php echo esc_url($mrent_icons_url . 'instagram.svg'); ?>"
									alt=""
									class="size-[20px] xl:size-[30px] shrink-0 object-contain"
									width="30" height="30"
									loading="lazy" decoding="async" aria-hidden="true">
							</a>
						<?php endif; ?>
						<?php if ($mrent_subscribe_tg) : ?>
							<a href="<?php echo esc_url($mrent_subscribe_tg); ?>" class="inline-flex items-center gap-[10px] xl:gap-[15px] self-start hover:opacity-80 transition-opacity" target="_blank" rel="noopener noreferrer">
								<span class="font-display font-medium text-[clamp(14px,13.03px+0.26vw,18px)] text-mrent-white underline underline-offset-2">
									<?php esc_html_e('Подписаться в Telegram', 'm-rent'); ?>
								</span>
								<img
									src="<?php echo esc_url($mrent_icons_url . 'telegram-color.svg'); ?>"
									alt=""
									class="size-[20px] xl:size-[30px] shrink-0 object-contain"
									width="30" height="30"
									loading="lazy" decoding="async" aria-hidden="true">
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php /* Правая колонка: CF7 форма. Использует общие стили `.mrent-consultation-form`. */ ?>
		<div class="mrent-consultation-form w-full xl:grow xl:shrink xl:basis-[845px] xl:max-w-[845px] xl:min-w-0">
			<?php echo do_shortcode('[contact-form-7 id="' . $mrent_form_id . '"]'); ?>
		</div>

	</div>
</section>