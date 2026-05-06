<?php

/**
 * Hero страницы «О нас» — фото-фон + центрированный заголовок/подзаголовок/CTA.
 *
 * Контент берётся из ACF-группы «О нас» (group_mrent_about), таб «Hero».
 * Если поле «Заголовок» (about_hero_title) не заполнено — секция не выводится.
 *
 * Раскладка по макету (Figma 2169:2084 desktop / 2345:3634 mobile):
 *
 *   Десктоп (≥ 2xl):
 *     • Картинка 1920×900, full-bleed, top:0
 *     • Градиент → #1E1D1F (49.666%..86.653%)
 *     • Текст-блок (h1 + p + button), w-1013, абсолютно, top:515 (≈57% высоты),
 *       gap-50 между текстом и кнопкой, gap-20 между h1 и p.
 *     • Кнопка 300×65 центрирована.
 *
 *   Мобайл (< 2xl):
 *     • Картинка 360×450, top:0
 *     • Градиент с теми же стопами
 *     • Текст-блок w-330 absolute top:325 (≈72%), gap-30 / gap-15
 *     • Кнопка во всю ширину контейнера, h:55. Контент свешивается ~120px
 *       ниже картинки — компенсируется нижним spacer'ом.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = get_field('about_hero_title', $mrent_page_id);

if (! $mrent_title) {
	return;
}

$mrent_hero_image = get_field('about_hero_image', $mrent_page_id);
$mrent_lead       = (string) get_field('about_hero_lead', $mrent_page_id);
$mrent_cta_url    = get_field('about_hero_cta_url', $mrent_page_id);
$mrent_cta_label  = get_field('about_hero_cta_label', $mrent_page_id);

$mrent_hero_url = $mrent_hero_image['url'] ?? '';
$mrent_hero_alt = $mrent_hero_image['alt'] ?? '';
?>

<section class="relative bg-mrent-black">

	<?php /* Картинка hero в потоке: задаёт высоту секции снизу.
	         overflow-hidden клипает <img> по краям блока.
	         Текст-overlay — сиблинг этому блоку, не клипается. */ ?>
	<div class="relative h-[450px] 2xl:h-[900px] overflow-hidden">
		<?php if ($mrent_hero_url) : ?>
			<img
				src="<?php echo esc_url($mrent_hero_url); ?>"
				alt="<?php echo esc_attr($mrent_hero_alt); ?>"
				class="absolute inset-0 size-full object-cover"
				loading="eager"
				decoding="async"
				fetchpriority="high"
			>
		<?php endif; ?>
		<?php /* Градиент → #1E1D1F. Стопы 49.666%..86.653% одинаковые
		         для мобайла и десктопа (по Figma). */ ?>
		<span class="absolute inset-0 bg-gradient-to-b from-transparent from-[49.666%] to-mrent-black to-[86.653%]" aria-hidden="true"></span>
	</div>

	<?php /* Текст-overlay (h1 + p + кнопка). Абсолютное позиционирование
	         относительно <section>. На мобайле блок может выходить за низ
	         картинки — поэтому ниже стоит spacer, добавляющий высоту секции. */ ?>
	<div class="absolute inset-x-0 top-[325px] 2xl:top-[515px] px-[15px] 2xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex justify-center">
			<div class="w-full max-w-[330px] 2xl:w-[1013px] 2xl:max-w-none flex flex-col gap-[30px] 2xl:gap-[50px] items-center">
				<div class="flex flex-col gap-[15px] 2xl:gap-[20px] items-center text-center text-white w-full">
					<h1 class="font-display font-[700] text-[28px] 2xl:text-[74px] leading-[1.2] 2xl:max-w-[913px]">
						<?php echo esc_html($mrent_title); ?>
					</h1>
					<?php if ($mrent_lead !== '') : ?>
						<p class="text-[16px] 2xl:text-[30px] leading-[1.2] w-full">
							<?php echo esc_html($mrent_lead); ?>
						</p>
					<?php endif; ?>
				</div>
				<?php if ($mrent_cta_label) : ?>
					<a
						href="<?php echo esc_url($mrent_cta_url ?: '#'); ?>"
						class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] 2xl:h-[65px] w-full 2xl:w-[300px] px-[15px] text-mrent-black font-display font-medium text-[14px] 2xl:text-[20px] leading-none whitespace-nowrap transition-colors"
					>
						<?php echo esc_html($mrent_cta_label); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php /* Spacer под картинкой — на мобайле текст+кнопка свешиваются
	         ниже картинки на ~120px. На десктопе блок умещается внутри
	         900px — spacer не нужен. */ ?>
	<div class="h-[120px] 2xl:h-0"></div>

</section>
