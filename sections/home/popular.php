<?php

/**
 * Главная: секция «Популярные модели».
 *
 * Тянет машины с включённым ACF-флагом `car_is_popular` (true/false).
 * Если ни одна машина не помечена — секция не выводится.
 *
 * Layout (Figma 2994:8824 desktop / 2994:8989 mobile):
 *   • Mobile: заголовок 28px по центру → одна карточка в Swiper'е → 4 полоски
 *     пагинации (40×4, активная белая, #302f31 неактивные) → ссылка
 *     «Смотреть каталог» 18px жёлтая подчёркнутая.
 *   • Desktop: ряд «заголовок 74px Bold слева ↔ Смотреть каталог 30px справа»,
 *     ниже Swiper на 4 карточки в ряд (gap 29).
 *
 * Swiper-инициализация — в src/cars.js, через [data-mrent-popular].
 * Карточки внутри секции инлайновые: их разметка отличается от каталога —
 * на мобайле здесь видно название, кнопки в ряд, фото 450px высотой.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_popular = new WP_Query([
	'post_type'      => 'car',
	'posts_per_page' => -1,
	'meta_query'     => [
		[
			'key'   => 'car_is_popular',
			'value' => '1',
		],
	],
	'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
	'no_found_rows'  => true,
]);

if (! $mrent_popular->have_posts()) {
	wp_reset_postdata();
	return;
}

$mrent_catalog_url = get_post_type_archive_link('car') ?: home_url('/cars/');
?>

<section class="bg-mrent-black py-[60px] xl:py-[60px] xl:h-[calc(100vh-100px)] xl:flex xl:flex-col">
	<div class="px-[15px] xl:px-[100px] xl:flex-1 xl:min-h-0 xl:w-full xl:flex xl:flex-col">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px] xl:flex-1 xl:min-h-0 xl:w-full">

			<?php /* ── Заголовок (+ ссылка на каталог справа на десктопе) ── */ ?>
			<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[15px]">
				<h2 class="font-[700] text-[clamp(24px,16.83px+2.98vw,50px)] leading-[1.2] text-mrent-white text-center xl:text-left">
					<?php esc_html_e('Популярные модели', 'm-rent'); ?>
				</h2>
				<a href="<?php echo esc_url($mrent_catalog_url); ?>" class="hidden xl:inline-flex text-mrent-yellow underline font-[500] text-[18px] hover:text-[#FFF831] transition-colors">
					<?php esc_html_e('Смотреть каталог', 'm-rent'); ?>
				</a>
			</div>

			<?php /* ── Swiper с карточками ──
			         `w-full min-w-0` критично: .swiper — flex-item внутри родителя
			         `flex flex-col`, у flex-items по умолчанию `min-width: auto`,
			         поэтому Swiper может бесконечно расширяться, подстраиваясь под
			         intrinsic width контента. min-w-0 фиксирует это. */ ?>
			<div class="mrent-popular swiper w-full min-w-0 xl:flex-1 xl:min-h-0" data-mrent-popular>
				<div class="swiper-wrapper">
					<?php while ($mrent_popular->have_posts()) :
						$mrent_popular->the_post();
						$mrent_price       = (float) get_field('car_price_per_day');
						$mrent_booking_url = get_field('car_booking_url') ?: '#booking';
						$mrent_thumb_url   = get_the_post_thumbnail_url(get_post(), 'large');
						$mrent_hover_image = get_field('car_hover_image');
						$mrent_hover_url   = is_array($mrent_hover_image) && ! empty($mrent_hover_image['url']) ? $mrent_hover_image['url'] : '';
					?>
						<div class="swiper-slide">
							<article class="flex flex-col gap-[20px] xl:gap-[30px] w-full h-full">
								<a href="<?php the_permalink(); ?>" class="group block relative rounded-[15px] overflow-hidden h-[225px] xl:h-auto xl:flex-1 xl:min-h-0 bg-[#252426]">
									<?php if ($mrent_thumb_url) : ?>
										<img src="<?php echo esc_url($mrent_thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="absolute inset-0 size-full object-cover transition-opacity duration-300<?php echo $mrent_hover_url ? ' group-hover:opacity-0' : ''; ?>" loading="lazy" decoding="async">
									<?php endif; ?>
									<?php if ($mrent_hover_url) : ?>
										<img src="<?php echo esc_url($mrent_hover_url); ?>" alt="" aria-hidden="true" class="absolute inset-0 size-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-300" loading="lazy" decoding="async">
									<?php endif; ?>
								</a>

								<div class="flex flex-col gap-[15px] xl:gap-[30px] items-start w-full flex-1">
									<h3 class="w-full font-[700] text-[clamp(12px,10.54px+0.39vw,18px)] leading-[1.2] text-mrent-white">
										<a href="<?php the_permalink(); ?>" class="hover:text-mrent-yellow transition-colors">
											<?php the_title(); ?>
										</a>
									</h3>

									<div class="w-full mt-auto">
										<p class="font-[400] text-[clamp(14px,12.6px+0.91vw,20px)] leading-[1.2] text-mrent-white">
											<?php echo esc_html(mrent_byn_price($mrent_price, '/' . __('сутки', 'm-rent'))); ?>
										</p>
										<p class="mt-[4px] font-[400] text-[clamp(11px,9.95px+0.68vw,15px)] leading-[1.2] text-mrent-white/60">
											<?php echo esc_html(mrent_usd_price($mrent_price, '/' . __('сутки', 'm-rent'))); ?>
											<span class="px-[4px]">·</span>
											<?php echo esc_html(mrent_rub_price($mrent_price, '/' . __('сутки', 'm-rent'))); ?>
										</p>
									</div>

									<?php /* В отличие от каталога мобильные кнопки здесь — в ряд (Figma 2994:8984). */ ?>
									<div class="flex gap-[10px] items-stretch w-full">
										<a href="<?php echo esc_url($mrent_booking_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] flex-1 h-[55px] xl:h-[60px] flex items-center justify-center rounded-[15px] text-mrent-black font-[500] text-[clamp(14px,12.54px+0.39vw,18px)] whitespace-nowrap transition-colors">
											<?php esc_html_e('Забронировать', 'm-rent'); ?>
										</a>
										<a href="<?php the_permalink(); ?>" class="border border-mrent-white hover:bg-mrent-white/10 flex-1 h-[55px] xl:h-[60px] flex items-center justify-center rounded-[15px] text-mrent-white font-[500] text-[clamp(14px,12.54px+0.39vw,18px)] whitespace-nowrap transition-colors">
											<?php esc_html_e('Подробнее', 'm-rent'); ?>
										</a>
									</div>
								</div>
							</article>
						</div>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>
			</div>

			<?php /* ── Mobile-only: пагинация-полоски + ссылка «Смотреть каталог» ── */ ?>
			<div class="xl:hidden flex flex-col items-center gap-[30px]">
				<div class="mrent-popular-pagination flex flex-wrap gap-[10px] justify-center"></div>
				<a href="<?php echo esc_url($mrent_catalog_url); ?>" class="text-mrent-yellow underline font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] hover:text-[#FFF831] transition-colors">
					<?php esc_html_e('Смотреть каталог', 'm-rent'); ?>
				</a>
			</div>

		</div>
	</div>
</section>