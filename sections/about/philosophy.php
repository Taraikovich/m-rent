<?php

/**
 * About: секция «Наша философия» — карточка с фоновой фотографией.
 *
 * Контент: ACF-группа «О нас», таб «Философия».
 * Если поле «Заголовок» (about_philosophy_title) не заполнено — секция не выводится.
 *
 * Один DOM, адаптив через `xl:`-варианты (Figma 2218:3276 desktop / 2345:3820 mobile).
 *
 *   < xl  — карточка rounded-15 c фото-фоном. py-30 px-25, gap-30. Заголовок 28px Bold,
 *            лид 16px Book, кнопка во всю ширину h-55. Чтобы фото снизу было видно,
 *            карточка имеет `aspect-[330/618]` ≈ пропорция мобильного макета.
 *
 *   ≥ xl  — pt-100 pb-422 (большое поле снизу — там видны машины), px-440 для контента
 *            (845px по центру). Заголовок 74px, лид 30px, кнопка 300×65, gap-50.
 *
 * Градиент-оверлей: rgba(30,29,31,0.81) от 26.8% сверху → прозрачный к 68.7% снизу
 * (стопы из Figma — затемняет верх под текст, оставляя нижнюю часть фото видимой).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field('about_philosophy_title', $mrent_page_id);

if ($mrent_title === '') {
	return;
}

$mrent_lead      = (string) get_field('about_philosophy_lead', $mrent_page_id);
$mrent_cta_label = (string) get_field('about_philosophy_cta_label', $mrent_page_id);
$mrent_cta_url   = (string) get_field('about_philosophy_cta_url', $mrent_page_id);
$mrent_image     = get_field('about_philosophy_image', $mrent_page_id);

$mrent_image_url = $mrent_image['url'] ?? '';
$mrent_image_alt = $mrent_image['alt'] ?? '';
?>

<section class="bg-mrent-black pb-[60px] xl:pb-[100px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto">

			<div class="relative rounded-[15px] overflow-hidden aspect-[330/618] xl:aspect-auto">

				<?php if ($mrent_image_url) : ?>
					<img
						src="<?php echo esc_url($mrent_image_url); ?>"
						alt="<?php echo esc_attr($mrent_image_alt); ?>"
						class="absolute inset-0 size-full object-cover"
						loading="lazy"
						decoding="async">
				<?php endif; ?>

				<?php /* Градиент: тёмный сверху → прозрачный снизу. Стопы из Figma. */ ?>
				<span class="absolute inset-0 bg-gradient-to-b from-[rgba(30,29,31,0.81)] from-[26.827%] to-[rgba(30,29,31,0)] to-[67.05%]" aria-hidden="true"></span>

				<?php /* Контент. На mobile — full-width, на xl — max-w-845, центр.
				         Padding: mobile py-30 px-25, desktop pt-100 pb-422 (большое
				         поле снизу для фото). */ ?>
				<div class="relative flex flex-col items-center gap-[30px] xl:gap-[50px] text-center text-mrent-white px-[25px] py-[30px] xl:px-[100px] xl:pt-25 xl:pb-[422px]">
					<div class="flex flex-col gap-[15px] xl:gap-[30px] items-center w-full xl:max-w-[845px]">
						<h2 class="font-display font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] w-full">
							<?php echo esc_html($mrent_title); ?>
						</h2>
						<?php if ($mrent_lead !== '') : ?>
							<p class="text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.2] w-full">
								<?php echo esc_html($mrent_lead); ?>
							</p>
						<?php endif; ?>
					</div>

					<?php if ($mrent_cta_label !== '') : ?>
						<a
							href="<?php echo esc_url($mrent_cta_url ?: '#'); ?>"
							class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full xl:w-[300px] px-[15px] text-mrent-black font-display font-medium text-[clamp(14px,13.03px+0.26vw,18px)] leading-none whitespace-nowrap transition-colors">
							<?php echo esc_html($mrent_cta_label); ?>
						</a>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>
</section>