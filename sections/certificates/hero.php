<?php

/**
 * Hero-секция страницы «Подарочные сертификаты».
 *
 * Figma: desktop 2218:3284 / mobile 2345:4350.
 * ACF: group_mrent_certificates (page_template = certificates-page.php).
 *
 * Поля:
 *   certificates_hero_title (text, default «Подарочные сертификаты»)
 *   certificates_hero_text  (textarea, default — лид из дизайна)
 *   certificates_hero_cta_text (text, default «Заказать»)
 *   certificates_hero_cta_url  (text, default «#booking-form»)
 *   certificates_hero_image (image, fallback на bundled certificates-stack.png)
 *
 * CTA по умолчанию — триггер модалки бронирования (href="#booking-form").
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title    = get_field('certificates_hero_title');
$mrent_text     = get_field('certificates_hero_text');
$mrent_cta_text = get_field('certificates_hero_cta_text');
$mrent_cta_url  = get_field('certificates_hero_cta_url');
$mrent_image    = get_field('certificates_hero_image');

$mrent_title    = $mrent_title    !== '' && $mrent_title    !== null ? $mrent_title    : __('Подарочные сертификаты', 'm-rent');
$mrent_text     = $mrent_text     !== '' && $mrent_text     !== null ? $mrent_text     : __('Подарочный сертификат M-RENT — это стильный способ подарить эмоции, впечатление и возможность выбрать автомобиль по настроению', 'm-rent');
$mrent_cta_text = $mrent_cta_text !== '' && $mrent_cta_text !== null ? $mrent_cta_text : __('Заказать', 'm-rent');
$mrent_cta_url  = $mrent_cta_url  !== '' && $mrent_cta_url  !== null ? $mrent_cta_url  : '#booking-form';

if (is_array($mrent_image) && ! empty($mrent_image['url'])) {
	$mrent_img_src = $mrent_image['url'];
	$mrent_img_alt = $mrent_image['alt'] !== '' ? $mrent_image['alt'] : $mrent_title;
	$mrent_img_w   = ! empty($mrent_image['width'])  ? (int) $mrent_image['width']  : 938;
	$mrent_img_h   = ! empty($mrent_image['height']) ? (int) $mrent_image['height'] : 444;
} else {
	$mrent_img_src = get_template_directory_uri() . '/assets/images/certificates-stack.png';
	$mrent_img_alt = $mrent_title;
	$mrent_img_w   = 938;
	$mrent_img_h   = 444;
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[30px] xl:pt-[70px] pb-[40px] xl:pb-[100px]">
	<div class="max-w-[1720px] mx-auto flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] xl:gap-[40px]">

		<?php /* Левая колонка: заголовок, лид, CTA. */ ?>
		<div class="flex flex-col gap-[30px] xl:gap-[50px] xl:w-[665px] xl:shrink-0">
			<div class="flex flex-col gap-[1px] xl:gap-[30px] text-mrent-white leading-[1.2]">
				<h1 class="font-display font-bold text-[clamp(24px,17.69px+1.68vw,50px)]">
					<?php echo esc_html($mrent_title); ?>
				</h1>
				<p class="font-display text-[clamp(14px,12.54px+0.39vw,20px)]">
					<?php echo nl2br(esc_html($mrent_text)); ?>
				</p>
			</div>

			<a
				href="<?php echo esc_url($mrent_cta_url); ?>"
				class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center w-full xl:w-[300px] h-[55px] xl:h-[65px] rounded-[15px] font-display font-medium text-mrent-black text-[clamp(14px,13.03px+0.26vw,18px)] leading-none transition-colors">
				<?php echo esc_html($mrent_cta_text); ?>
			</a>
		</div>

		<?php /* Правая колонка: единое изображение трёх сертификатов. */ ?>
		<div>
			<img
				src="<?php echo esc_url($mrent_img_src); ?>"
				alt="<?php echo esc_attr($mrent_img_alt); ?>"
				class="self-center shrink-0 w-full max-w-[444px] xl:max-w-[938px] h-auto"
				width="<?php echo esc_attr($mrent_img_w); ?>"
				height="<?php echo esc_attr($mrent_img_h); ?>"
				loading="lazy" decoding="async">
		</div>

	</div>
</section>