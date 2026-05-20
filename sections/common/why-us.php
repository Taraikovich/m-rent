<?php

/**
 * Общая секция «Почему выбирают нас».
 *
 * Используется на главной, в клубе, на карточке авто и т.д. Тонкие обёртки в
 * `sections/{page}/why-us.php` читают ACF и передают сюда уже готовый массив
 * карточек:
 *
 *     get_template_part( 'sections/common/why-us', null, [
 *         'title' => 'Почему выбирают нас',          // опционально
 *         'items' => [
 *             [
 *                 'why_us_icon'        => [ 'url' => ..., 'alt' => ... ],
 *                 'why_us_title'       => 'Полная конфиденциальность.',
 *                 'why_us_description' => 'Текст…',
 *             ],
 *             ...
 *         ],
 *     ] );
 *
 * Если `items` пустой — секция не выводится.
 *
 * Layout (Figma 230:445 desktop / 2317:3541 mobile):
 *   • Mobile: заголовок 28px → Swiper из N слайдов по 3 карточки стопкой
 *     (gap 10), под ним полоски-пагинации.
 *   • Desktop: заголовок 74px → статичная сетка 3 кол (gap 30, карточки w-553).
 *     Swiper не инициализируется.
 *
 * Один DOM на оба брейкпоинта: на xl+ обёртки `.swiper-slide` и внутренний
 * `flex flex-col` сворачиваются через `xl:!contents`, и карточки становятся
 * прямыми детьми `.swiper-wrapper`, у которой на xl+ включается
 * `xl:!grid xl:!grid-cols-3 xl:!gap-[30px]`.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_items = (isset($args['items']) && is_array($args['items'])) ? $args['items'] : [];
$mrent_title = isset($args['title']) && $args['title'] !== ''
	? (string) $args['title']
	: __('Почему выбирают нас', 'm-rent');

if (! $mrent_items) {
	return;
}

// По 3 карточки на slide — каждая группа = один swiper-slide на мобайле.
$mrent_chunks = array_chunk($mrent_items, 3);
?>

<section class="bg-mrent-black py-[60px] xl:py-[100px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col items-center gap-[30px] xl:gap-[60px]">

			<h2 class="font-[700] text-[clamp(24px,16.84px_+_2.98vw,50px)] leading-[1.2] text-mrent-white text-center">
				<?php echo esc_html($mrent_title); ?>
			</h2>

			<?php /* Mobile = Swiper, Desktop = 3-кол grid через схлопывание промежуточных
			         обёрток в `display: contents`. `min-w-0` обязателен — без него
			         flex-item с intrinsic-content может бесконечно разрастаться
			         (та же проблема, что и в popular). */ ?>
			<div class="mrent-why-us swiper w-full min-w-0 xl:!overflow-visible" data-mrent-why-us>
				<div class="swiper-wrapper xl:!grid xl:!grid-cols-3 xl:!gap-[30px]">
					<?php foreach ($mrent_chunks as $chunk) : ?>
						<div class="swiper-slide xl:!contents">
							<div class="flex flex-col gap-[10px] xl:contents">
								<?php foreach ($chunk as $item) : ?>
									<div class="bg-[#252426] border border-[#302f31] flex flex-col items-start gap-[30px] xl:gap-[70px] p-[25px] xl:p-[40px] rounded-[15px] w-full">
										<?php if (! empty($item['why_us_icon']['url'])) : ?>
											<img
												src="<?php echo esc_url($item['why_us_icon']['url']); ?>"
												alt="<?php echo esc_attr($item['why_us_icon']['alt'] ?? ''); ?>"
												class="shrink-0 size-[30px] xl:size-[60px]"
												loading="lazy"
												decoding="async"
												aria-hidden="true">
										<?php endif; ?>
										<p class="leading-[1.2] text-mrent-white text-[clamp(14px,11.57px_+_0.65vw,18px)]">
											<span class="font-[800]"><?php echo esc_html($item['why_us_title'] ?? ''); ?></span>
											<?php echo esc_html($item['why_us_description'] ?? ''); ?>
										</p>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* Mobile-only: пагинация-полоски (по одной на swiper-slide). */ ?>
			<?php if (count($mrent_chunks) > 1) : ?>
				<div class="xl:hidden mrent-why-us-pagination flex flex-wrap gap-[10px] justify-center"></div>
			<?php endif; ?>

		</div>
	</div>
</section>