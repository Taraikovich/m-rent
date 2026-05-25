<?php

/**
 * «Аренда для юр. лиц»: секция «Что входит в стоимость» (Figma 5296:7370 / 2846:7032).
 *
 * Раскладка:
 *   Desktop (xl+, фрейм 1720px): 2-кол row.
 *     • Текст-колонка flex-1 (~1037px): h2 74px → list 30px, gap-[60px], items gap-[15px].
 *     • Фото справа 577×580, rounded-[15px], shrink-0; gap между колонками 100px.
 *   Mobile (фрейм 330): col, текст → фото.
 *     • h2 28px → ul 14px, gap-[15px], items gap-[10px].
 *     • Фото h-[331px], rounded-[15px], во всю ширину контейнера.
 *
 * ACF (group_mrent_biz_services):
 *   • biz_included_title  — Text; default «В стоимость аренды для юридических лиц включены»
 *   • biz_included_items  — Repeater (text); если пусто — fallback из 7 пунктов дизайна
 *   • biz_included_image  — Image; fallback assets/images/business-included-wheel.png
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = (string) get_field('biz_included_title');
if ($mrent_title === '') {
	$mrent_title = __('В стоимость аренды для юридических лиц включены', 'm-rent');
}

$mrent_items_raw = (array) get_field('biz_included_items');
$mrent_items     = [];
foreach ($mrent_items_raw as $mrent_row) {
	$mrent_text = (string) ($mrent_row['text'] ?? '');
	if ($mrent_text !== '') {
		$mrent_items[] = $mrent_text;
	}
}
if (! $mrent_items) {
	$mrent_items = [
		__('предоставление автомобиля на согласованный срок аренды', 'm-rent'),
		__('бесплатная доставка автомобиля по городу', 'm-rent'),
		__('установленный лимит пробега в зависимости от выбранной модели', 'm-rent'),
		__('подготовка автомобиля перед выдачей', 'm-rent'),
		__('оформление договора аренды и акта приема-передачи', 'm-rent'),
		__('фото- и видеофиксация состояния автомобиля при выдаче и возврате', 'm-rent'),
		__('сопровождение менеджером по всем организационным вопросам аренды 24/7', 'm-rent'),
	];
}

$mrent_image_acf = get_field('biz_included_image');
$mrent_image_url = '';
$mrent_image_alt = '';
if (is_array($mrent_image_acf)) {
	if (! empty($mrent_image_acf['url'])) {
		$mrent_image_url = (string) $mrent_image_acf['url'];
	}
	if (! empty($mrent_image_acf['alt'])) {
		$mrent_image_alt = (string) $mrent_image_acf['alt'];
	}
}
if ($mrent_image_url === '') {
	$mrent_image_url = get_template_directory_uri() . '/assets/images/business-included-wheel.png';
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[30px] xl:pt-[60px]">
	<div class="max-w-[1720px] mx-auto flex flex-col xl:flex-row gap-[30px] xl:gap-[100px] items-stretch">

		<div class="flex flex-col gap-[15px] xl:gap-[60px] xl:flex-1 xl:min-w-0">
			<h2 class="font-display font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] text-mrent-white">
				<?php echo esc_html($mrent_title); ?>
			</h2>
			<ul class="flex flex-col gap-[10px] xl:gap-[15px] font-[400] text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.2] text-mrent-white">
				<?php foreach ($mrent_items as $mrent_item) : ?>
					<li class="list-disc ms-[21px] xl:ms-[45px]">
						<?php echo esc_html($mrent_item); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="relative shrink-0 w-full aspect-square xl:w-[577px] xl:h-[580px] xl:aspect-auto rounded-[15px] overflow-hidden">
			<img
				src="<?php echo esc_url($mrent_image_url); ?>"
				alt="<?php echo esc_attr($mrent_image_alt); ?>"
				class="absolute inset-0 size-full object-cover object-center"
				loading="lazy"
				decoding="async">
		</div>

	</div>
</section>