<?php

/**
 * Главная: баннер «Доставка авто по Беларуси».
 *
 * Контент: ACF-группа «Главная», таб «Доставка-баннер».
 * Если поле «Заголовок» (home_delivery_title) не заполнено — секция не выводится.
 *
 * Один DOM, адаптив через `2xl:`-варианты (Figma 329:292 desktop / 2323:3782 mobile):
 *   • < 2xl  — flex-col, центрирование: заголовок 28px Bold (max-w-224) →
 *              лид 16px → жёлтая кнопка 160×55 (14px Medium) → фото BMW снизу,
 *              шириной до 330px, h-auto.
 *   • ≥ 2xl  — flex-row + justify-between: текст-колонка 642px слева
 *              (заголовок 74px + лид 30px + кнопка 300×65, gap-50 между текстом
 *              и кнопкой) ↔ фото справа фикс-шириной 680px, h-auto.
 *
 * Картинка — настоящий PNG автомобиля (1536×1024, ~3:2). Без overflow-кропа:
 * `object-contain` показывает машину целиком на обоих брейкпоинтах. Источник —
 * ACF home_delivery_image; если пусто — assets/images/delivery-bmw.png.
 *
 * Кнопка — ACF home_delivery_button_url; если пусто — архив /cars/.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field('home_delivery_title', $mrent_page_id);

if ($mrent_title === '') {
	return;
}

$mrent_lead        = (string) get_field('home_delivery_lead', $mrent_page_id);
$mrent_button_text = get_field('home_delivery_button_text', $mrent_page_id) ?: __('Выбрать авто', 'm-rent');
$mrent_button_url  = get_field('home_delivery_button_url', $mrent_page_id);
if (! $mrent_button_url) {
	$mrent_button_url = get_post_type_archive_link('car') ?: home_url('/cars/');
}

$mrent_image     = get_field('home_delivery_image', $mrent_page_id);
$mrent_image_url = $mrent_image['url'] ?? get_template_directory_uri() . '/assets/images/delivery-bmw.png';
$mrent_image_alt = $mrent_image['alt'] ?? '';
?>

<section class="bg-mrent-black px-3.75 py-15 2xl:px-25 2xl:py-25">
	<div class="max-w-430 mx-auto flex flex-col 2xl:flex-row items-center 2xl:justify-between gap-7.5 2xl:gap-10">

		<?php /* Текстовая колонка: на мобайле — центр на всю ширину; на ≥2xl — фикс 642px,
		         left-aligned, gap-50 между блоком текста и кнопкой (по Figma 326:261). */ ?>
		<div class="flex flex-col items-center 2xl:items-start gap-7.5 2xl:gap-12.5 w-full 2xl:w-160.5 2xl:shrink-0">
			<div class="flex flex-col items-center 2xl:items-start gap-3.75 2xl:gap-7.5 text-mrent-white text-center 2xl:text-left leading-[1.2] w-full">
				<h2 class="font-display font-bold text-[28px] 2xl:text-[74px] max-w-56 2xl:max-w-none">
					<?php echo esc_html($mrent_title); ?>
				</h2>
				<?php if ($mrent_lead) : ?>
					<p class="text-base 2xl:text-3xl w-full"><?php echo esc_html($mrent_lead); ?></p>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url($mrent_button_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-13.75 w-40 2xl:h-16.25 2xl:w-75 px-3.75 text-mrent-black font-medium text-sm 2xl:text-xl leading-none whitespace-nowrap transition-colors">
				<?php echo esc_html($mrent_button_text); ?>
			</a>
		</div>

		<?php /* Фото-блок. На мобайле — w-330 (упрётся в max-w-full на узких экранах),
		         высота по контенту картинки. На ≥2xl — flex-1 (забирает остаток ширины
		         после 642px текст-колонки) + self-stretch (override items-center → высота
		         = высота текст-колонки). Внутри img object-contain: машина целиком,
		         центрирована, без обрезки. */ ?>
		<div class="w-full shrink-0 2xl:w-auto 2xl:flex-1 2xl:self-stretch">
			<img
				src="<?php echo esc_url($mrent_image_url); ?>"
				alt="<?php echo esc_attr($mrent_image_alt); ?>"
				class="block w-full h-auto 2xl:h-full object-contain"
				loading="lazy"
				decoding="async"
			>
		</div>

	</div>
</section>
