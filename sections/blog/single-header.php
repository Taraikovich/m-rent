<?php

/**
 * Шапка одиночной записи блога: заголовок + дата + лид (Figma 2253:2829 / 2345:3401).
 *
 * Десктоп: title 74 (Bold) слева до 1066px, дата 30 (Book, white/50) справа,
 *          ниже — лид-параграф 30 (Book, white). gap-30 между блоком title/дата
 *          и лидом.
 * Мобайл:  дата 16 сверху, заголовок 28 ниже (gap-10), лид 16 (Book, white)
 *          ниже всего блока (gap-15).
 *
 * Источник лида — `the_excerpt` (если задан в редакторе). Если пусто — не выводим.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_date = get_the_date('j F Y');
$mrent_lead = has_excerpt() ? wp_strip_all_tags(get_the_excerpt()) : '';
?>

<section class="bg-mrent-black pt-[20px] xl:pt-[40px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[15px] xl:gap-[30px] leading-[1.2]">
			<div class="flex flex-col-reverse xl:flex-row xl:items-start xl:justify-between gap-[10px] xl:gap-[40px]">
				<h1 class="text-mrent-white font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] xl:max-w-[1066px]">
					<?php the_title(); ?>
				</h1>
				<time
					datetime="<?php echo esc_attr(get_the_date('c')); ?>"
					class="text-mrent-white/50 font-[400] text-[clamp(14px,11.12px+0.78vw,30px)] whitespace-nowrap shrink-0">
					<?php echo esc_html($mrent_date); ?>
				</time>
			</div>

			<?php if ($mrent_lead !== '') : ?>
				<p class="text-mrent-white font-[400] text-[clamp(14px,12.54px+0.39vw,20px)]">
					<?php echo esc_html($mrent_lead); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>