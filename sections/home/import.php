<?php

/**
 * Главная: баннер «Импорт BMW».
 *
 * Контент: ACF-группа «Главная», таб «Импорт BMW».
 * Если поле «Заголовок» (home_import_title) не заполнено — секция не выводится.
 *
 * Один DOM, адаптив через `2xl:`-варианты (Figma 2786:7366 desktop / 2994:9162 mobile):
 *   • < 2xl  — flex-col gap-30: текст-блок (заголовок 28px Bold + лид 16px Book,
 *              gap-30 внутри) → жёлтая кнопка full-width h-55 (14px Medium) →
 *              фото снизу w-full h-148 rounded-15, object-cover (кроп по Figma:
 *              h-166.92% top-[-43.27%]).
 *   • ≥ 2xl  — flex-row justify-between: слева текст-колонка 642px
 *              (gap-50 между текст-блоком и кнопкой; gap-30 между заголовком 74px
 *              и лидом 30px); кнопка 300×65; справа фото 960×431 rounded-15
 *              object-cover.
 *
 * Источник фото — ACF home_import_image; если пусто — assets/images/import-bmw.jpg.
 * Кнопка — ACF home_import_button_url; если пусто — `#`.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field('home_import_title', $mrent_page_id);

if ($mrent_title === '') {
	return;
}

$mrent_lead        = (string) get_field('home_import_lead', $mrent_page_id);
$mrent_button_text = get_field('home_import_button_text', $mrent_page_id) ?: __('Подробнее', 'm-rent');
$mrent_button_url  = get_field('home_import_button_url', $mrent_page_id) ?: '#';

$mrent_image     = get_field('home_import_image', $mrent_page_id);
$mrent_image_url = $mrent_image['url'] ?? get_template_directory_uri() . '/assets/images/import-bmw.jpg';
$mrent_image_alt = $mrent_image['alt'] ?? '';
?>

<section class="bg-mrent-black px-3.75 py-15 2xl:px-25 2xl:py-25">
	<div class="max-w-430 mx-auto flex flex-col gap-7.5 2xl:flex-row 2xl:items-center 2xl:justify-between 2xl:gap-10">

		<?php /* Текстовая колонка. На мобайле — w-full, gap-30 внутри текст-блока,
		         кнопка full-width. На ≥2xl — фикс w-160.5 (642px) по Figma 2786:7367,
		         gap-50 между текст-блоком и кнопкой. */ ?>
		<div class="flex flex-col gap-7.5 2xl:gap-12.5 w-full 2xl:w-160.5 2xl:shrink-0">
			<div class="flex flex-col gap-7.5 text-mrent-white leading-[1.2]">
				<h2 class="font-display font-bold text-[28px] 2xl:text-[74px]">
					<?php echo esc_html($mrent_title); ?>
				</h2>
				<?php if ($mrent_lead) : ?>
					<p class="text-base 2xl:text-3xl"><?php echo esc_html($mrent_lead); ?></p>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url($mrent_button_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-13.75 w-full 2xl:h-16.25 2xl:w-75 px-3.75 text-mrent-black font-medium text-sm 2xl:text-xl leading-none whitespace-nowrap transition-colors">
				<?php echo esc_html($mrent_button_text); ?>
			</a>
		</div>

		<?php /* Фото. На мобайле — w-full h-148 (≈ Figma 330×148, object-cover кроп).
		         На ≥2xl — фикс 960×431 по Figma 2786:7363. rounded-15 + object-cover
		         повторяет Figma-кроп (h-166.92% top-[-43.27%]). */ ?>
		<div class="w-full h-37 2xl:flex-1 2xl:min-w-0 2xl:max-w-240 2xl:h-107.75 rounded-[15px] overflow-hidden">
			<img
				src="<?php echo esc_url($mrent_image_url); ?>"
				alt="<?php echo esc_attr($mrent_image_alt); ?>"
				class="block w-full h-full object-cover"
				loading="lazy"
				decoding="async">
		</div>

	</div>
</section>
