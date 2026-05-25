<?php

/**
 * Общая секция «Услуги по категориям с табами».
 *
 * Используется на странице «О нас» и на других, где нужен виджет услуг,
 * сгруппированных по категориям. Тонкие обёртки в `sections/{page}/services-tabbed.php`
 * читают ACF/таксономию и передают сюда уже сформированные данные:
 *
 *     get_template_part( 'sections/common/services-tabbed', null, [
 *         'title'       => 'Аренда авто под ваш запрос',
 *         'button_text' => 'Все услуги',
 *         'button_url'  => '/services/',
 *         'tabs'        => [
 *             [
 *                 'id'       => 'events',                 // уникальный slug, для data-attr
 *                 'name'     => 'Для мероприятий',
 *                 'services' => [                          // до 4 элементов
 *                     [ 'url'=>..., 'title'=>..., 'image_url'=>... ],
 *                     ...
 *                 ],
 *             ],
 *             ...
 *         ],
 *     ] );
 *
 * Если `tabs` пустой или нет ни одной услуги — секция не выводится.
 *
 * Layout (Figma 2169:2145 desktop / 2345:3873 mobile):
 *   • Desktop (≥xl): «заголовок 74px Bold ↔ ряд пилюль 300×65 ↔ жёлтый CTA 300×65».
 *     Ниже — Swiper по 2 карточки 850×600 на видимую область, gap-20, h-600.
 *     Карточка с фото-фоном, корнерный/нижний градиент, текст p-50 в нижнем углу.
 *   • Mobile  (<xl): заголовок 28px по центру; ниже — нативный <select> 55h
 *     (выбор активного таба); ниже — Swiper по 1 карточке 330×233; пагинация
 *     полосками; внизу жёлтый CTA на всю ширину h-55.
 *
 * Свитч табов и инициализация Swiper'ов — в `src/services.js`
 * (`[data-mrent-services-tabbed]`).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title       = isset($args['title']) ? (string) $args['title'] : '';
$mrent_button_text = isset($args['button_text']) ? (string) $args['button_text'] : '';
$mrent_button_url  = isset($args['button_url']) ? (string) $args['button_url'] : '';
$mrent_tabs_raw    = isset($args['tabs']) && is_array($args['tabs']) ? $args['tabs'] : [];

// Отсеиваем табы без услуг — пустой таб в DOM не нужен.
$mrent_tabs = [];
foreach ($mrent_tabs_raw as $mrent_tab) {
	if (empty($mrent_tab['services']) || ! is_array($mrent_tab['services'])) {
		continue;
	}
	$mrent_tabs[] = $mrent_tab;
}

if (! $mrent_tabs) {
	return;
}
?>

<section class="bg-mrent-black py-[60px] xl:py-[100px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]" data-mrent-services-tabbed>

			<?php if ($mrent_title !== '') : ?>
				<h2 class="font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] text-mrent-white text-center xl:text-left">
					<?php echo esc_html($mrent_title); ?>
				</h2>
			<?php endif; ?>

			<?php /* Ряд: пилюли (desktop) + select (mobile) + жёлтый CTA (desktop). */ ?>
			<div class="flex flex-col gap-[20px] xl:flex-row xl:items-center xl:justify-between xl:gap-[20px]">

				<?php /* Desktop pills */ ?>
				<div class="hidden xl:flex gap-[20px]">
					<?php foreach ($mrent_tabs as $mrent_tab) : ?>
						<button
							type="button"
							data-mrent-tab-trigger
							data-mrent-tab-id="<?php echo esc_attr($mrent_tab['id']); ?>"
							class="mrent-tab-pill bg-[#252426] text-mrent-white aria-[current=true]:bg-mrent-white aria-[current=true]:text-mrent-black flex items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] whitespace-nowrap transition-colors cursor-pointer">
							<?php echo esc_html($mrent_tab['name']); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<?php /* Mobile select */ ?>
				<div class="relative xl:hidden">
					<select
						data-mrent-tab-select
						class="appearance-none bg-white text-mrent-black w-full h-[55px] rounded-[15px] pl-[15px] pr-[40px] font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] cursor-pointer">
						<?php foreach ($mrent_tabs as $mrent_tab) : ?>
							<option value="<?php echo esc_attr($mrent_tab['id']); ?>"><?php echo esc_html($mrent_tab['name']); ?></option>
						<?php endforeach; ?>
					</select>
					<svg class="absolute right-[15px] top-1/2 -translate-y-1/2 pointer-events-none w-[10px] h-[10px] text-mrent-black" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<polyline points="1 3 5 8 9 3" />
					</svg>
				</div>

				<?php /* Desktop yellow CTA */ ?>
				<?php if ($mrent_button_text !== '') : ?>
					<a
						href="<?php echo esc_url($mrent_button_url ?: '#'); ?>"
						class="hidden xl:flex bg-mrent-yellow hover:bg-[#FFF831] items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] whitespace-nowrap transition-colors">
						<?php echo esc_html($mrent_button_text); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php /* Tab panels: каждый — свой Swiper с пагинацией. Активный без `hidden`. */ ?>
			<?php foreach ($mrent_tabs as $mrent_index => $mrent_tab) : ?>
				<div
					data-mrent-tab-panel
					data-mrent-tab-id="<?php echo esc_attr($mrent_tab['id']); ?>"
					class="flex flex-col gap-[30px] xl:gap-[60px] items-center"
					<?php if ($mrent_index > 0) : ?>hidden<?php endif; ?>>
					<div class="mrent-services-tabbed-swiper swiper w-full min-w-0">
						<div class="swiper-wrapper">
							<?php foreach ($mrent_tab['services'] as $mrent_service) :
								$mrent_service_url   = (string) ($mrent_service['url'] ?? '#');
								$mrent_service_title = (string) ($mrent_service['title'] ?? '');
								$mrent_service_image = (string) ($mrent_service['image_url'] ?? '');
							?>
								<div class="swiper-slide">
									<a
										href="<?php echo esc_url($mrent_service_url); ?>"
										class="group relative flex flex-col items-start h-[233px] xl:h-auto xl:aspect-850/600 w-full p-[25px] xl:p-[50px] rounded-[15px] border border-[#302f31] overflow-hidden bg-[#252426]">
										<?php if ($mrent_service_image !== '') : ?>
											<img
												src="<?php echo esc_url($mrent_service_image); ?>"
												alt="<?php echo esc_attr($mrent_service_title); ?>"
												class="absolute inset-0 size-full object-cover"
												loading="lazy"
												decoding="async">
										<?php endif; ?>

										<?php /* Корнерный градиент: тёмный в top-left → прозрачный к bottom-right.
										         Текст в верхнем-левом углу читаем, остальная картинка не «глушится». */ ?>
										<span
											class="absolute inset-0 pointer-events-none rounded-[15px]"
											style="background: linear-gradient(135deg, rgba(30,29,31,0.85) 0%, rgba(30,29,31,0.6) 25%, rgba(30,29,31,0) 60%);"
											aria-hidden="true"></span>

										<div class="relative flex flex-col gap-[15px] xl:gap-[30px] items-start max-w-[60%]">
											<h3 class="text-mrent-white font-[800] text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2]">
												<?php echo esc_html($mrent_service_title); ?>
											</h3>
											<span class="text-mrent-white font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.2] underline whitespace-nowrap group-hover:text-mrent-yellow transition-colors">
												<?php esc_html_e('Подробнее', 'm-rent'); ?>
											</span>
										</div>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div data-mrent-tab-pagination class="flex flex-wrap gap-[10px] xl:gap-[19px] justify-center"></div>
				</div>
			<?php endforeach; ?>

			<?php /* Mobile-only: жёлтый CTA на всю ширину под пагинацией. */ ?>
			<?php if ($mrent_button_text !== '') : ?>
				<a
					href="<?php echo esc_url($mrent_button_url ?: '#'); ?>"
					class="xl:hidden bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] w-full px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] whitespace-nowrap transition-colors">
					<?php echo esc_html($mrent_button_text); ?>
				</a>
			<?php endif; ?>

		</div>
	</div>
</section>