<?php

/**
 * Hero страницы услуги (Figma 2083:1816 / 2535:5988).
 *
 * Раскладка по макету (точные top/sизы):
 *
 *   Десктоп (1920px wide):
 *     • Картинка 1920×805, full-bleed, top:0
 *     • Градиент → #1E1D1F (60%..91.7%)
 *     • Текст-блок (h1 + p), w:915, абсолютно, top:574 (≈71% высоты), text-center
 *     • Кнопка 300×65, top:805 — прикреплена к нижнему краю картинки и
 *       свешивается под неё на всю свою высоту
 *
 *   Мобайл (360px wide):
 *     • Картинка 360×450, top:0
 *     • Градиент 38%..80.6%
 *     • Текст-блок w-[330px] absolute top:325 (≈72%)
 *     • Кнопка w-full × 55, идёт в потоке после картинки с gap-30 от текста
 *
 * Источники данных:
 *   • картинка — ACF service_card_image, иначе featured image
 *   • подзаголовок — ACF service_subtitle (если пусто, p не выводится)
 *   • CTA-текст — ACF service_cta_text, default «Забронировать услугу»
 *   • CTA-ссылка — ACF service_cta_url, default «#booking» (заглушка)
 */

if (! defined('ABSPATH')) {
	exit;
}

global $post;

$mrent_subtitle = (string) get_field('service_subtitle');
$mrent_cta_text = get_field('service_cta_text');
if (! $mrent_cta_text) {
	$mrent_cta_text = __('Забронировать услугу', 'm-rent');
}
$mrent_cta_url = get_field('service_cta_url') ?: '#booking';

// Источник картинки hero: service_hero_image → service_card_image → featured.
$mrent_image_url = '';
foreach (['service_hero_image', 'service_card_image'] as $mrent_field) {
	$mrent_value = get_field($mrent_field);
	if (is_array($mrent_value) && ! empty($mrent_value['url'])) {
		$mrent_image_url = $mrent_value['url'];
		break;
	}
}
if ($mrent_image_url === '') {
	$mrent_image_url = get_the_post_thumbnail_url($post, 'full') ?: '';
}
?>

<section class="relative bg-mrent-black">

	<?php /* Картинка hero. overflow-hidden — клипает <img> по краям блока,
	         но не клипает текст-overlay (он сиблинг этому блоку, а не ребёнок). */ ?>
	<div class="relative w-full h-[450px] xl:h-[805px] overflow-hidden">
		<?php if ($mrent_image_url) : ?>
			<img
				src="<?php echo esc_url($mrent_image_url); ?>"
				alt="<?php echo esc_attr(get_the_title()); ?>"
				class="absolute inset-0 size-full object-cover"
				loading="eager"
				decoding="async">
		<?php endif; ?>
		<?php /* Градиент → #1E1D1F. На мобайле начало плавнее (38%), на десктопе
		         позже (60%) — чтобы не съедать центр картинки. */ ?>
		<span class="absolute inset-0 bg-gradient-to-b from-transparent from-[38%] xl:from-[60%] to-mrent-black to-[80%] xl:to-[92%]" aria-hidden="true"></span>
	</div>

	<?php /* Текст-overlay в потоке после картинки с отрицательным mt — визуально
	         сидит поверх низа картинки (как в макете: top:325 на мобайле,
	         top:574 на десктопе), но занимает реальную высоту, поэтому кнопка
	         ниже не подъезжает под текст при длинном подзаголовке.
	         Мобайл: 450 - 325 = 125. Десктоп: 805 - 574 = 231. */ ?>
	<div class="relative -mt-[125px] xl:-mt-[231px] px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex justify-center">
			<div class="w-full max-w-[330px] xl:w-[915px] xl:max-w-none flex flex-col gap-[15px] xl:gap-[20px] items-center text-center">
				<h1 class="text-mrent-white font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] w-full">
					<?php the_title(); ?>
				</h1>
				<?php if ($mrent_subtitle !== '') : ?>
					<p class="text-mrent-white font-[400] text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.2] w-full">
						<?php echo esc_html($mrent_subtitle); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php /* CTA-кнопка идёт в потоке после текста, mt-[30px] = гарантированный
	         отступ от низа текста. В макете на десктопе кнопка ровно у низа
	         картинки (top:805), но реальная вертикальная позиция теперь зависит
	         от высоты подзаголовка — это сознательный компромисс, чтобы кнопка
	         никогда не уезжала под текст. */ ?>
	<div class="relative px-[15px] xl:px-[100px] mt-[30px]">
		<div class="max-w-[1720px] mx-auto flex justify-center">
			<a
				href="<?php echo esc_url($mrent_cta_url); ?>"
				class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full max-w-[330px] xl:w-[300px] xl:max-w-none px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] whitespace-nowrap transition-colors">
				<?php echo esc_html($mrent_cta_text); ?>
			</a>
		</div>
	</div>

	<?php /* Нижний отступ section. На десктопе кнопка свешивается на 65px ниже
	         картинки → дальше нужен ещё небольшой gap до следующей секции. */ ?>
	<div class="h-[40px]"></div>

</section>