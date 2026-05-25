<?php

/**
 * Hero страницы «Клубные карты» — фото-фон + центрированный заголовок/подзаголовок/CTA.
 *
 * Контент берётся из ACF-группы «Клубные карты» (group_mrent_club), таб «Hero».
 * Если поле «Заголовок» (club_hero_title) не заполнено — секция не выводится.
 *
 * Раскладка идентична hero «О нас» (см. sections/about/hero.php).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = get_field('club_hero_title', $mrent_page_id);

if (! $mrent_title) {
	return;
}

$mrent_hero_image = get_field('club_hero_image', $mrent_page_id);
$mrent_lead       = (string) get_field('club_hero_lead', $mrent_page_id);
$mrent_cta_url    = get_field('club_hero_cta_url', $mrent_page_id);
$mrent_cta_label  = get_field('club_hero_cta_label', $mrent_page_id);

$mrent_hero_url = $mrent_hero_image['url'] ?? '';
$mrent_hero_alt = $mrent_hero_image['alt'] ?? '';
?>

<section class="relative bg-mrent-black">

	<div class="relative h-[450px] xl:h-[900px] overflow-hidden">
		<?php if ($mrent_hero_url) : ?>
			<img
				src="<?php echo esc_url($mrent_hero_url); ?>"
				alt="<?php echo esc_attr($mrent_hero_alt); ?>"
				class="absolute inset-0 size-full object-cover"
				loading="eager"
				decoding="async"
				fetchpriority="high">
		<?php endif; ?>
		<span class="absolute inset-0 bg-gradient-to-b from-transparent from-[49.666%] to-mrent-black to-[86.653%]" aria-hidden="true"></span>
	</div>

	<?php /* Текст-overlay (h1 + p + кнопка).
	         Mobile: в потоке, поднят `-mt-[125px]` — попадает на тёмную часть
	         градиента (450 - 125 = 325, как top-[325px] из макета) и автоматически
	         расширяет высоту секции по контенту. Spacer не нужен.
	         Desktop (≥xl): абсолютное позиционирование, `top-[515px]` внутри
	         900px-картинки. */ ?>
	<div class="relative z-10 -mt-[125px] px-[15px] pb-[40px] xl:absolute xl:inset-x-0 xl:top-[515px] xl:mt-0 xl:pb-0 xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex justify-center">
			<div class="w-full max-w-[330px] xl:w-[1013px] xl:max-w-none flex flex-col gap-[30px] xl:gap-[50px] items-center">
				<div class="flex flex-col gap-[15px] xl:gap-[20px] items-center text-center text-white w-full">
					<h1 class="font-display font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] xl:max-w-[913px]">
						<?php echo esc_html($mrent_title); ?>
					</h1>
					<?php if ($mrent_lead !== '') : ?>
						<p class="text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.2] w-full">
							<?php echo esc_html($mrent_lead); ?>
						</p>
					<?php endif; ?>
				</div>
				<?php if ($mrent_cta_label) : ?>
					<a
						href="<?php echo esc_url($mrent_cta_url ?: '#'); ?>"
						class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full xl:w-[300px] px-[15px] text-mrent-black font-display font-medium text-[clamp(14px,13.03px+0.26vw,18px)] leading-none whitespace-nowrap transition-colors">
						<?php echo esc_html($mrent_cta_label); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

</section>