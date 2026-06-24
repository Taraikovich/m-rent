<?php

/**
 * Страница «Контакты»: основной блок с контактами и CF7-формой «Бронирование автомобиля».
 *
 * Figma: desktop 2061:1298 / mobile 2324:3063.
 *
 * Раскладка:
 *   Desktop (xl+, max-w-1720): 2 колонки flex-row gap-70 items-stretch.
 *     Левая (w-805 flex-col justify-between):
 *       Верх — title-блок (gap-20): H1 «Контакты» 50px Bold + лид 20px Book (w-496),
 *       затем Contact Info: 2 колонки gap-50 (телефон+email+компания / адрес+часы),
 *       каждая ячейка — icon 30×30 + текст 18px Book.
 *       Низ — ряд соц-иконок 60×60 gap-14.
 *     Правая: CF7-форма w-845 в `.mrent-consultation-form`. Колонка тянется
 *       на высоту левой (`items-stretch`), форма — flex-столбец, кнопка
 *       отправки прижата к низу (`margin-top:auto`) — её нижний край на
 *       десктопе совпадает с рядом соц-иконок.
 *   Mobile (<xl): 1 колонка gap-30. H1 24px + лид 14px, контакты строкой
 *     (icon 20×20 + текст 14px), социалки 38×38, форма full-width.
 *
 * Тексты адресов, названия компании, УНП и часов работы захардкожены — те же
 * placeholder-значения, что в Figma (отдельных ACF-полей под них нет).
 *
 * CF7-форма: ищет форму с заголовком «Бронирование автомобиля», fallback на
 * первую доступную. Если форм нет — секция всё равно показывает контакты.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_cf7_posts = get_posts([
	'post_type'   => 'wpcf7_contact_form',
	'post_status' => 'publish',
	'title'       => 'Бронирование автомобиля',
	'numberposts' => 1,
]);
$mrent_form_id = $mrent_cf7_posts ? (int) $mrent_cf7_posts[0]->ID : 0;

if (! $mrent_form_id) {
	$mrent_cf7_any = get_posts([
		'post_type'   => 'wpcf7_contact_form',
		'post_status' => 'publish',
		'numberposts' => 1,
	]);
	$mrent_form_id = $mrent_cf7_any ? (int) $mrent_cf7_any[0]->ID : 0;
}

$mrent_phone     = mrent_options_get('contact_phone');
$mrent_phone_url = mrent_options_phone_url();
$mrent_email     = mrent_options_get('contact_email');
$mrent_email_url = mrent_options_email_url();

$mrent_messengers = mrent_options_messengers();
$mrent_socials    = mrent_options_socials();

$mrent_icons_url = get_template_directory_uri() . '/assets/icons/';

/* Иконки соц-ряда. По Figma — 5 брендов в строгом порядке:
   telegram, whatsapp, viber, messenger, instagram. Без youtube/tiktok. */
$mrent_brand_icons = [
	'telegram'  => 'contact-telegram-chat.png',
	'whatsapp'  => 'contact-whatsapp.png',
	'viber'     => 'contact-viber.png',
	'messenger' => 'contact-messenger.png',
	'instagram' => 'contact-instagram.png',
];

/* URL для каждого слота: telegram/whatsapp/viber/messenger — из мессенджеров,
   instagram — из соцсетей. */
$mrent_brand_links = [];
foreach ($mrent_brand_icons as $mrent_slug => $mrent_file) {
	$mrent_url = $mrent_slug === 'instagram'
		? ($mrent_socials['instagram'] ?? '')
		: ($mrent_messengers[$mrent_slug] ?? '');
	if ($mrent_url !== '') {
		$mrent_brand_links[$mrent_slug] = $mrent_url;
	}
}

/* Захардкоженные тексты (плейсхолдеры из Figma — ACF-полей нет). */
$mrent_company  = __('Индивидуальный предприниматель Бура А.П ', 'm-rent');
$mrent_unp      = __('УНП 291913690', 'm-rent');
$mrent_addr_1   = __('г. Брест, Варшавское шоссе 43 Бизнес- центр „IQ”', 'm-rent');
$mrent_addr_2   = __('г. Минск, Пимена Панченко 7', 'm-rent');
$mrent_hours    = __('Мы работаем круглосуточно', 'm-rent');
?>

<section class="bg-mrent-black px-[15px] xl:px-[40px] 2xl:px-[100px] pt-[30px] xl:pt-[70px] pb-[40px] xl:pb-[100px]">
	<div class="max-w-[1720px] mx-auto flex flex-col xl:flex-row xl:items-stretch gap-[30px] xl:gap-[70px] min-w-0">

		<?php /* Левая колонка: контакты + соцсети. Растягивается под высоту формы (justify-between). */ ?>
		<div class="flex flex-col xl:w-[805px] xl:min-w-0 xl:shrink xl:justify-between gap-[30px] xl:gap-0">

			<?php /* Верхняя часть: заголовок + контакт-инфо. */ ?>
			<div class="flex flex-col gap-[30px] xl:gap-[60px]">

				<?php /* Заголовок + лид. */ ?>
				<div class="flex flex-col gap-[15px] xl:gap-[20px] text-mrent-white leading-[1.2]">
					<h1 class="font-display font-bold text-[clamp(24px,17.69px+1.68vw,50px)]">
						<?php esc_html_e('Контакты', 'm-rent'); ?>
					</h1>
					<p class="font-display text-[clamp(14px,12.54px+0.39vw,20px)] xl:max-w-[496px]">
						<?php esc_html_e('Нужна аренда авто в Беларуси? Свяжитесь с нами — поможем выбрать авто и быстро оформить бронирование', 'm-rent'); ?>
					</p>
				</div>

				<?php /* Контакт-инфо. Mobile — flex-col gap-10. Desktop — 2 колонки gap-50. */ ?>
				<div class="flex flex-col xl:flex-row gap-[10px] xl:gap-[50px] xl:items-start">

					<?php /* Колонка 1: телефон, email, компания. */ ?>
					<div class="flex flex-col gap-[10px] xl:gap-[20px]">

						<?php if ($mrent_phone !== '') : ?>
							<a href="<?php echo esc_url($mrent_phone_url); ?>" class="flex items-center gap-[15px] group">
								<svg class="size-[20px] xl:w-[30px] xl:h-[34px] shrink-0 text-mrent-yellow" viewBox="0 0 14 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
									<path d="M9.87 0.75H3.63C2.03942 0.75 0.75 2.05684 0.75 3.66892V15.8311C0.75 17.4432 2.03942 18.75 3.63 18.75H9.87C11.4606 18.75 12.75 17.4432 12.75 15.8311V3.66892C12.75 2.05684 11.4606 0.75 9.87 0.75Z" />
									<path d="M5.79004 15.3446H7.71004" />
								</svg>
								<span class="font-display text-[clamp(14px,13.03px+0.26vw,18px)] text-mrent-white leading-[1.2] whitespace-nowrap group-hover:opacity-80 transition-opacity">
									<?php echo esc_html($mrent_phone); ?>
								</span>
							</a>
						<?php endif; ?>

						<?php if ($mrent_email !== '') : ?>
							<a href="<?php echo esc_url($mrent_email_url); ?>" class="flex items-center gap-[15px] group">
								<svg class="size-[20px] xl:size-[30px] shrink-0 text-mrent-yellow" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
									<path d="M4 20C3.45 20 2.97933 19.8043 2.588 19.413C2.19667 19.0217 2.00067 18.5507 2 18V6C2 5.45 2.196 4.97933 2.588 4.588C2.98 4.19667 3.45067 4.00067 4 4H20C20.55 4 21.021 4.196 21.413 4.588C21.805 4.98 22.0007 5.45067 22 6V18C22 18.55 21.8043 19.021 21.413 19.413C21.0217 19.805 20.5507 20.0007 20 20H4ZM12 13L20 8V6L12 11L4 6V8L12 13Z" />
								</svg>
								<span class="font-display text-[clamp(14px,13.03px+0.26vw,18px)] text-mrent-white leading-[1.2] whitespace-nowrap group-hover:opacity-80 transition-opacity">
									<?php echo esc_html($mrent_email); ?>
								</span>
							</a>
						<?php endif; ?>

						<div class="flex items-center gap-[15px]">
							<svg class="size-[20px] xl:size-[30px] shrink-0 text-mrent-yellow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
								<path d="M14 2v6h6" />
								<path d="M8 13h8M8 17h5" />
							</svg>
							<span class="font-display text-[clamp(14px,13.03px+0.26vw,18px)] text-mrent-white leading-[1.2] whitespace-nowrap">
								<?php echo esc_html($mrent_company); ?><br>
								<?php echo esc_html($mrent_unp); ?>
							</span>
						</div>
					</div>

					<?php /* Колонка 2: адреса + часы работы. */ ?>
					<div class="flex flex-col gap-[10px] xl:gap-[20px] xl:w-[400px]">

						<div class="flex items-center gap-[15px]">
							<svg class="size-[20px] xl:size-[30px] shrink-0 text-mrent-yellow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
								<circle cx="12" cy="10" r="3" />
							</svg>
							<span class="font-display text-[clamp(14px,13.03px+0.26vw,18px)] text-mrent-white leading-[1.2] whitespace-nowrap">
								<?php echo esc_html($mrent_addr_1); ?><br>
								<?php echo esc_html($mrent_addr_2); ?>
							</span>
						</div>

						<div class="flex items-center gap-[15px]">
							<svg class="size-[20px] xl:size-[30px] shrink-0 text-mrent-yellow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<circle cx="12" cy="12" r="9" />
								<path d="M12 7v5l3 2" />
							</svg>
							<span class="font-display text-[clamp(14px,13.03px+0.26vw,18px)] text-mrent-white leading-[1.2] whitespace-nowrap">
								<?php echo esc_html($mrent_hours); ?>
							</span>
						</div>
					</div>
				</div>
			</div>

			<?php /* Соц-иконки. Mobile 38×38 / desktop 70×70. */ ?>
			<?php if ($mrent_brand_links) : ?>
				<div class="flex items-center gap-[10px] xl:gap-[14px] mb-8.5">
					<?php foreach ($mrent_brand_links as $mrent_slug => $mrent_url) : ?>
						<?php $mrent_file = $mrent_brand_icons[$mrent_slug] ?? null; ?>
						<?php if (! $mrent_file) continue; ?>
						<a
							href="<?php echo esc_url($mrent_url); ?>"
							class="block size-[38px] xl:size-[60px] hover:opacity-80 transition-opacity shrink-0"
							aria-label="<?php echo esc_attr(ucfirst($mrent_slug)); ?>"
							target="_blank" rel="noopener noreferrer">
							<img
								src="<?php echo esc_url($mrent_icons_url . $mrent_file); ?>"
								alt=""
								class="size-full object-contain"
								width="60" height="60"
								loading="lazy" decoding="async">
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php /* Правая колонка: CF7 форма «Бронирование автомобиля». */ ?>
		<?php if ($mrent_form_id) : ?>
			<div class="mrent-consultation-form mrent-consultation-form--stretch w-full xl:w-[845px] xl:flex xl:flex-col xl:min-w-0 xl:shrink xl:max-w-full">
				<?php echo do_shortcode('[contact-form-7 id="' . $mrent_form_id . '"]'); ?>
			</div>
		<?php endif; ?>

	</div>
</section>