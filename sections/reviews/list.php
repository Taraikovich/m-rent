<?php

/**
 * Страница «Отзывы»: секция со свайпером отзывов.
 *
 * Контент: ACF-группа «Отзывы — страница» (group_mrent_reviews), привязана
 * к шаблону `reviews-page.php`.
 *   • reviews_title      — заголовок секции (default «Отзывы наших клиентов»).
 *   • reviews_cta_label  — текст CTA-кнопки (default «Оставить отзыв»).
 *   • reviews_cta_url    — ссылка CTA (default «#reviews-form»).
 *   • reviews_items      — repeater (поля: name, car_label, text, rating 1..5).
 *
 * Если repeater пуст, секция полностью скрывается.
 *
 * Layout (Figma 2218:3282 desktop / 2483:4159 mobile nav):
 *   • Desktop (xl+): «заголовок 74px Bold ← → жёлтая кнопка “Оставить отзыв”
 *     300×65 справа», ниже Swiper — 4 колонки × 3 ряда (Grid-модуль),
 *     gap 29px. Под свайпером — навигация: prev (55×55) ↔ полоски-пагинация ↔
 *     next (55×55).
 *   • Mobile (<xl): заголовок 28px центрирован, под ним кнопка во всю ширину,
 *     Swiper — 1 карточка × 4 ряда, под ним та же навигация.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();

$mrent_title = (string) get_field('reviews_title', $mrent_page_id);
if ($mrent_title === '') {
	$mrent_title = __('Отзывы наших клиентов', 'm-rent');
}

$mrent_cta_label = (string) get_field('reviews_cta_label', $mrent_page_id);
if ($mrent_cta_label === '') {
	$mrent_cta_label = __('Оставить отзыв', 'm-rent');
}
$mrent_cta_url = (string) get_field('reviews_cta_url', $mrent_page_id);
if ($mrent_cta_url === '') {
	$mrent_cta_url = '#reviews-form';
}

$mrent_items_raw = get_field('reviews_items', $mrent_page_id);
$mrent_items     = [];
if (is_array($mrent_items_raw)) {
	foreach ($mrent_items_raw as $mrent_row) {
		$mrent_items[] = [
			'name'      => (string) ($mrent_row['name'] ?? ''),
			'car_label' => (string) ($mrent_row['car_label'] ?? ''),
			'text'      => (string) ($mrent_row['text'] ?? ''),
			'rating'    => max(0, min(5, (int) ($mrent_row['rating'] ?? 5))),
		];
	}
}

if (empty($mrent_items)) {
	return;
}
?>

<section class="bg-mrent-black pt-[40px] xl:pt-[60px] pb-[60px] xl:pb-[100px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

			<?php /* ── Заголовок + CTA (на xl+; на мобайле CTA уезжает вниз) ── */ ?>
			<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[20px] xl:gap-[30px]">
				<h1 class="font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] text-mrent-white text-center xl:text-left">
					<?php echo esc_html($mrent_title); ?>
				</h1>
				<a href="<?php echo esc_url($mrent_cta_url); ?>" class="hidden xl:flex bg-mrent-yellow hover:bg-[#FFF831] transition-colors items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] leading-none shrink-0">
					<?php echo esc_html($mrent_cta_label); ?>
				</a>
			</div>

			<?php /* ── Swiper-карточек отзывов ──
			         min-w-0 — Swiper как flex-item внутри flex-col родителя,
			         иначе подстраивается под intrinsic ширину и расползается. */ ?>
			<div class="mrent-reviews swiper w-full min-w-0" data-mrent-reviews>
				<div class="swiper-wrapper">
					<?php foreach ($mrent_items as $mrent_item) : ?>
						<div class="swiper-slide flex">
							<article class="bg-[#252426] border border-[#302f31] rounded-[15px] p-[25px] xl:p-[35px] flex flex-col gap-[20px] w-full h-full">
								<div class="flex flex-col gap-[15px]">
									<div class="flex flex-col gap-[15px] xl:gap-[20px]">
										<?php /* Рейтинг 5 звёзд (Figma 161×25). */ ?>
										<div class="flex items-center gap-[5px] text-mrent-yellow" aria-label="<?php echo esc_attr(sprintf(__('Оценка %d из 5', 'm-rent'), $mrent_item['rating'])); ?>">
											<?php for ($mrent_s = 1; $mrent_s <= 5; $mrent_s++) :
												$mrent_filled = $mrent_s <= $mrent_item['rating'];
											?>
												<svg class="w-[27px] h-[25px] <?php echo $mrent_filled ? '' : 'opacity-25'; ?>" viewBox="0 0 27 25" fill="currentColor" aria-hidden="true">
													<path d="M13.5 0l3.708 8.292L26 9.55l-6.5 6.342L21.034 25 13.5 20.792 5.966 25l1.534-9.108L1 9.55l8.792-1.258L13.5 0z" />
												</svg>
											<?php endfor; ?>
										</div>
										<p class="font-[600] text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2] text-mrent-white">
											<?php echo esc_html($mrent_item['name']); ?>
										</p>
									</div>
									<?php if ($mrent_item['car_label'] !== '') : ?>
										<p class="font-[400] text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.3] text-mrent-white">
											<?php echo esc_html($mrent_item['car_label']); ?>
										</p>
									<?php endif; ?>
								</div>
								<p class="font-[400] text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.2] text-mrent-white">
									<?php echo esc_html($mrent_item['text']); ?>
								</p>
							</article>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* ── Навигация под свайпером (Figma 2483:4159) ── */ ?>
			<div class="flex items-center justify-between gap-[15px]">
				<button type="button" class="mrent-reviews-prev bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] size-[55px] xl:size-[65px] shrink-0 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" aria-label="<?php esc_attr_e('Предыдущий', 'm-rent'); ?>">
					<svg width="9" height="16" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M8 1L1 8L8 15" stroke="#1E1D1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
				<div class="mrent-reviews-pagination flex flex-wrap gap-[10px] justify-center"></div>
				<button type="button" class="mrent-reviews-next bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] size-[55px] xl:size-[65px] shrink-0 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" aria-label="<?php esc_attr_e('Следующий', 'm-rent'); ?>">
					<svg width="9" height="16" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M1 1L8 8L1 15" stroke="#1E1D1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
			</div>

			<?php /* ── Mobile-only: CTA под навигацией ── */ ?>
			<a href="<?php echo esc_url($mrent_cta_url); ?>" class="xl:hidden bg-mrent-yellow hover:bg-[#FFF831] transition-colors flex items-center justify-center rounded-[15px] h-[55px] w-full px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] leading-none">
				<?php echo esc_html($mrent_cta_label); ?>
			</a>

		</div>
	</div>
</section>а