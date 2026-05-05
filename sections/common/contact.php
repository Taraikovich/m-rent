<?php

/**
 * Общий блок «Есть вопросы?» (Figma 2269:2659 desktop / 2324:2900 mobile).
 *
 * Контакты + соцсети + фото-баннер. ACF берётся из Options («Контакты и соцсети»):
 *   • контакты   — `mrent_options_messengers()` (whatsapp, viber, messenger, telegram_personal)
 *   • соцсети    — `mrent_options_socials()` (youtube, tiktok, instagram, telegram_channel)
 *   • Подписаться — отдельные ссылки на Instagram и Telegram-канал.
 * Заголовок, лид, кнопка «Оставить заявку», фото — захардкожены (содержимое
 * блока не редактируется через админку, только контактные ссылки).
 *
 * Один DOM, адаптив через `2xl:`-варианты:
 *   • < 2xl  — flex-col gap-30: контактная колонка → фото 330×165 rounded-15.
 *              h2 28px Bold (white) + лид 16px Book → жёлтая ссылка 18px Medium
 *              underline. Соц-секция: 2 колонки (соцсети / мессенджеры) друг под
 *              другом, каждая gap-15 (заголовок 20px + ряд иконок 40×40, gap-10).
 *              Затем «Подписаться в Instagram/Telegram» — flex-col gap-15, 18px
 *              Medium underline white.
 *   • ≥ 2xl  — flex-row justify-between items-center: слева контакт-колонка
 *              gap-60 (между title-блоком и соц-секцией). Title: gap-30 (h2 74px
 *              + лид 30px max-w-499 → жёлтая ссылка 30px Medium underline).
 *              Соц-секция gap-40: две колонки иконок 50×50 рядом (gap-40),
 *              каждая gap-30 (heading 30px Bold + ряд gap-10). Затем «Подписаться»-
 *              ссылки flex-row gap-30, 24px Book underline white.
 *              Справа фото 990px, `self-stretch` — высота равняется левой колонке.
 *
 * Иконки — цветные брендовые (`mrent_icon('brand-*')`) — отличаются от
 * монохромных в шапке. Слаги полей в options совпадают с суффиксами
 * иконок (youtube, tiktok, instagram, telegram, whatsapp, viber, messenger).
 *
 * Если ACF Options пустые — соответствующие колонки/ссылки скрываются. Если
 * пусты обе группы и обе «Подписаться» ссылки — соц-секция всё равно держит
 * вертикальный gap, но визуально остаётся только текст-блок и фото.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_socials      = mrent_options_socials();
$mrent_messengers   = mrent_options_messengers();
$mrent_subscribe_ig = $mrent_socials['instagram'] ?? '';
$mrent_subscribe_tg = $mrent_socials['telegram'] ?? '';

$mrent_image_url = get_template_directory_uri() . '/assets/images/contact-cars.jpg';
$mrent_icons_url = get_template_directory_uri() . '/assets/icons/';

/* Map slug → файл иконки. У соцсетей и мессенджеров одинаковый ключ
   `telegram`, но визуально это разные иконки в Figma — канал (channel)
   и личный чат (chat), поэтому файлы разные. */
$mrent_social_icons    = [
	'youtube'   => 'contact-youtube.png',
	'tiktok'    => 'contact-tiktok.png',
	'instagram' => 'contact-instagram.png',
	'telegram'  => 'contact-telegram-channel.png',
];
$mrent_messenger_icons = [
	'whatsapp'  => 'contact-whatsapp.png',
	'viber'     => 'contact-viber.png',
	'messenger' => 'contact-messenger.png',
	'telegram'  => 'contact-telegram-chat.png',
];

$mrent_icon_class = 'block size-10 2xl:size-[50px]';
?>

<section class="bg-mrent-black px-3.75 py-15 2xl:px-25 2xl:py-25">
	<div class="max-w-430 mx-auto flex flex-col gap-7.5 2xl:flex-row 2xl:items-stretch 2xl:justify-between 2xl:gap-10">

		<?php /* Контакт-колонка. Mobile gap-30 / desktop gap-60 между title-блоком и соц-секцией. */ ?>
		<div class="flex flex-col gap-7.5 2xl:gap-15 2xl:shrink-0">

			<?php /* Title + лид + жёлтая ссылка. Mobile gap-20 / desktop gap-30. */ ?>
			<div class="flex flex-col gap-5 2xl:gap-7.5">
				<div class="flex flex-col gap-3.75 2xl:gap-5 text-mrent-white leading-[1.2]">
					<h2 class="font-display font-bold text-[28px] 2xl:text-[74px]">
						<?php esc_html_e('Есть вопросы?', 'm-rent'); ?>
					</h2>
					<p class="font-display text-base 2xl:text-3xl 2xl:max-w-[499px]">
						<?php esc_html_e('Оставьте заявку, и мы свяжемся с вами, чтобы помочь выбрать лучший вариант.', 'm-rent'); ?>
					</p>
				</div>
				<a href="#" class="self-start font-display font-medium text-mrent-yellow text-lg 2xl:text-3xl underline underline-offset-2 hover:opacity-80 transition-opacity">
					<?php esc_html_e('Оставить заявку', 'm-rent'); ?>
				</a>
			</div>

			<?php /* Соц-секция. Mobile flex-col gap-20 / desktop flex-col gap-40 (внутри две колонки иконок + ряд ссылок-«Подписаться»). */ ?>
			<div class="flex flex-col gap-5 2xl:gap-10">

				<?php /* Две колонки: соцсети (Мы в соцсетях) + мессенджеры (Свяжитесь с нами). На <2xl — друг под другом, на ≥2xl — рядом. */ ?>
				<div class="flex flex-col 2xl:flex-row gap-5 2xl:gap-10">

					<?php if ($mrent_socials) : ?>
						<div class="flex flex-col gap-3.75 2xl:gap-7.5">
							<p class="font-display font-bold text-xl 2xl:text-3xl text-mrent-white leading-[1.2]">
								<?php esc_html_e('Мы в соцсетях:', 'm-rent'); ?>
							</p>
							<div class="flex gap-2.5 items-center">
								<?php foreach ($mrent_socials as $mrent_slug => $mrent_url) : ?>
									<?php $mrent_file = $mrent_social_icons[$mrent_slug] ?? null; ?>
									<?php if (! $mrent_file) continue; ?>
									<a href="<?php echo esc_url($mrent_url); ?>" class="<?php echo esc_attr($mrent_icon_class); ?> hover:opacity-80 transition-opacity" aria-label="<?php echo esc_attr(ucfirst($mrent_slug)); ?>" target="_blank" rel="noopener">
										<img src="<?php echo esc_url($mrent_icons_url . $mrent_file); ?>" alt="" class="size-full object-contain" width="50" height="50" loading="lazy" decoding="async">
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($mrent_messengers) : ?>
						<div class="flex flex-col gap-3.75 2xl:gap-7.5">
							<p class="font-display font-bold text-xl 2xl:text-3xl text-mrent-white leading-[1.2]">
								<?php esc_html_e('Свяжитесь с нами:', 'm-rent'); ?>
							</p>
							<div class="flex gap-2.5 items-center">
								<?php foreach ($mrent_messengers as $mrent_slug => $mrent_url) : ?>
									<?php $mrent_file = $mrent_messenger_icons[$mrent_slug] ?? null; ?>
									<?php if (! $mrent_file) continue; ?>
									<a href="<?php echo esc_url($mrent_url); ?>" class="<?php echo esc_attr($mrent_icon_class); ?> hover:opacity-80 transition-opacity" aria-label="<?php echo esc_attr(ucfirst($mrent_slug)); ?>" target="_blank" rel="noopener">
										<img src="<?php echo esc_url($mrent_icons_url . $mrent_file); ?>" alt="" class="size-full object-contain" width="50" height="50" loading="lazy" decoding="async">
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

				</div>

				<?php /* «Подписаться» — ссылки на канал/профиль. Mobile flex-col gap-15 / desktop flex-row gap-30. */ ?>
				<?php if ($mrent_subscribe_ig || $mrent_subscribe_tg) : ?>
					<div class="flex flex-col 2xl:flex-row gap-3.75 2xl:gap-7.5">
						<?php if ($mrent_subscribe_ig) : ?>
							<a href="<?php echo esc_url($mrent_subscribe_ig); ?>" class="self-start font-display font-medium 2xl:font-normal text-lg 2xl:text-2xl text-mrent-white underline underline-offset-2 hover:opacity-80 transition-opacity" target="_blank" rel="noopener">
								<?php esc_html_e('Подписаться в Instagram', 'm-rent'); ?>
							</a>
						<?php endif; ?>
						<?php if ($mrent_subscribe_tg) : ?>
							<a href="<?php echo esc_url($mrent_subscribe_tg); ?>" class="self-start font-display font-medium 2xl:font-normal text-lg 2xl:text-2xl text-mrent-white underline underline-offset-2 hover:opacity-80 transition-opacity" target="_blank" rel="noopener">
								<?php esc_html_e('Подписаться в Telegram', 'm-rent'); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php /* Фото. Mobile 330×165 (~aspect 2:1) full-width.
		         ≥2xl: фикс w-247.5 (990px). На десктопе `<img>` позиционируется
		         абсолютно, чтобы НЕ задавать собственную высоту блоку — иначе img
		         с натуральным аспектом 3:2 при ширине 990 диктовал бы 660px и
		         перебивал текстовую колонку. Без intrinsic-высоты flex `items-stretch`
		         тянет блок ровно до высоты текстовой колонки, `object-cover` режет
		         фото по высоте без искажений. На мобилке всё проще — фиксированный
		         aspect 2:1 + относительный img. */ ?>
		<div class="relative w-full aspect-[2/1] 2xl:w-247.5 2xl:aspect-auto 2xl:shrink-0 rounded-[15px] overflow-hidden">
			<img
				src="<?php echo esc_url($mrent_image_url); ?>"
				alt=""
				class="absolute inset-0 block w-full h-full object-cover"
				loading="lazy"
				decoding="async">
		</div>

	</div>
</section>
