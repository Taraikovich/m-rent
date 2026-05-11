<?php

/**
 * Секция «Почему стоит выбрать …» на странице услуги
 * (Figma desktop 2135:1436, mobile 2535:6110).
 *
 * Раскладка:
 *   Desktop (xl+, max-w-1720): шапка — H1 73px Bold (w-887) слева,
 *     подзаголовок 30px Book (w-724) справа в одну строку
 *     (justify-between, items-start). Сетка карточек — 3 колонки,
 *     gap-30. На xl+ Swiper уничтожается; раскладку даёт `xl:grid` на
 *     `.swiper-wrapper` (см. services.js → initBenefitsSwiper).
 *   Mobile: шапка стопкой (gap-15) — заголовок 28px → подзаголовок 16px.
 *     Карточки в Swiper'е: 1 колонка × 3 ряда на видимую страницу
 *     (Grid module, fill:row, swipe горизонтальный). Под слайдером —
 *     prev | пагинация-полосками | next.
 *
 * Карточка (Figma 2124:1827):
 *   bg #252426, border #302F31, rounded-15, p-30/40, gap-30/70 между иконкой
 *   и блоком «заголовок + описание». Иконка 40/60. Заголовок 18/30 Demi,
 *   описание 14/24 Book.
 *
 * ACF (group_mrent_service):
 *   • service_benefits_title    — Text;     если пусто — секция не выводится
 *   • service_benefits_subtitle — Textarea
 *   • service_benefits_items    — Repeater (icon image + title + text);
 *                                 если пусто — секция не выводится
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title    = (string) get_field('service_benefits_title');
$mrent_subtitle = (string) get_field('service_benefits_subtitle');
$mrent_items    = (array) get_field('service_benefits_items');

if ($mrent_title === '' || empty($mrent_items)) {
	return;
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px] text-mrent-white">

		<div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-[15px] xl:gap-[20px]">
			<h2 class="font-[700] text-[28px] xl:text-[73px] leading-[1.2] xl:w-[887px] shrink-0">
				<?php echo esc_html($mrent_title); ?>
			</h2>
			<?php if ($mrent_subtitle !== '') : ?>
				<p class="font-[400] text-[16px] xl:text-[30px] leading-[1.2] xl:w-[724px] whitespace-pre-line">
					<?php echo esc_html($mrent_subtitle); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php /* На xl+ Swiper уничтожается, .swiper-wrapper становится 3-кол grid'ом
		         через `xl:!grid` (важно `!`, чтобы перебить inline display:flex от Swiper'а
		         даже если он успел его выставить до destroy). На мобайле — Grid module
		         со slidesPerView:1, rows:3, fill:row → 3 карточки на страницу. */ ?>
		<div class="mrent-benefits-swiper swiper w-full min-w-0 xl:!overflow-visible" data-mrent-benefits-swiper>
			<div class="swiper-wrapper xl:!grid xl:grid-cols-3 xl:gap-[30px]">
				<?php foreach ($mrent_items as $mrent_item) :
					$mrent_item_icon  = is_array($mrent_item['icon'] ?? null) ? $mrent_item['icon'] : null;
					$mrent_item_title = (string) ($mrent_item['title'] ?? '');
					$mrent_item_text  = (string) ($mrent_item['text'] ?? '');
					if ($mrent_item_title === '' && $mrent_item_text === '') {
						continue;
					}
				?>
					<div class="swiper-slide !h-auto xl:!w-auto">
						<article class="bg-[#252426] border border-[#302F31] rounded-[15px] p-[30px] xl:p-[40px] flex flex-col gap-[30px] xl:gap-[70px] h-full">
							<?php if ($mrent_item_icon && ! empty($mrent_item_icon['url'])) : ?>
								<img
									src="<?php echo esc_url($mrent_item_icon['url']); ?>"
									alt="<?php echo esc_attr($mrent_item_icon['alt'] ?? ''); ?>"
									class="size-[40px] xl:size-[60px] object-contain shrink-0"
									loading="lazy"
									decoding="async">
							<?php endif; ?>
							<div class="flex flex-col gap-[10px] xl:gap-[15px]">
								<?php if ($mrent_item_title !== '') : ?>
									<h3 class="font-[600] text-[18px] xl:text-[30px] leading-[1.2]">
										<?php echo esc_html($mrent_item_title); ?>
									</h3>
								<?php endif; ?>
								<?php if ($mrent_item_text !== '') : ?>
									<p class="font-[400] text-[14px] xl:text-[24px] leading-[1.2]">
										<?php echo esc_html($mrent_item_text); ?>
									</p>
								<?php endif; ?>
							</div>
						</article>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php /* Mobile nav: prev | пагинация-полосками | next, full width. */ ?>
		<?php if (count($mrent_items) > 3) : ?>
			<div class="flex items-center justify-between gap-[15px] xl:hidden">
				<button
					type="button"
					class="mrent-benefits-prev bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
					aria-label="<?php esc_attr_e('Предыдущие преимущества', 'm-rent'); ?>">
					<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
						<polyline points="8 1 1 8 8 15" />
					</svg>
				</button>
				<div class="mrent-benefits-pagination flex flex-1 items-center justify-center gap-[10px]"></div>
				<button
					type="button"
					class="mrent-benefits-next bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
					aria-label="<?php esc_attr_e('Следующие преимущества', 'm-rent'); ?>">
					<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
						<polyline points="1 1 8 8 1 15" />
					</svg>
				</button>
			</div>
		<?php endif; ?>
	</div>
</section>