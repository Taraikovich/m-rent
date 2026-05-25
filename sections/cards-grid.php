<?php

/**
 * Универсальная секция «сетка карточек "иконка + заголовок"».
 *
 * Используется для секций certificates/audience и certificates/benefits —
 * визуально одинаковая раскладка, отличаются только данные и набор иконок.
 *
 * Раскладка:
 *   Desktop (xl+): Swiper уничтожается, контейнер падает в CSS-grid.
 *   Mobile: Swiper c grid-модулем (rows=4) — по 4 карточки на слайд.
 *
 * Ожидаемые $args:
 *   title          (string)  — заголовок H2.
 *   items          (array)   — список карточек: [['icon' => array, 'title' => string], ...].
 *                              icon — ACF image-массив (return_format «array») с ключами url/alt.
 *   grid_cols      (int)     — кол-во колонок desktop-сетки (1..4), по умолчанию 4.
 *   tall_cards     (bool)    — true → добавить `xl:min-h-[278px]` к карточкам (нужно audience).
 *   pagination_after (int)   — порог количества карточек, после которого показывать буллеты на мобайле.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title       = isset($args['title']) ? (string) $args['title'] : '';
$mrent_items       = isset($args['items']) && is_array($args['items']) ? array_values($args['items']) : [];
$mrent_grid_cols   = isset($args['grid_cols']) ? max(1, min(4, (int) $args['grid_cols'])) : 4;
$mrent_tall_cards  = ! empty($args['tall_cards']);
$mrent_pag_after   = isset($args['pagination_after']) ? (int) $args['pagination_after'] : 4;

if (! $mrent_items) {
	return;
}

$mrent_cols_class = [
	1 => 'xl:grid-cols-1',
	2 => 'xl:grid-cols-2',
	3 => 'xl:grid-cols-3',
	4 => 'xl:grid-cols-4',
][$mrent_grid_cols];

$mrent_total = count($mrent_items);
$mrent_show_pagination = $mrent_total > $mrent_pag_after;
// Литеральный класс (xl:min-h-[278px]) — иначе Tailwind v4 не увидит его при сканировании исходников.
$mrent_min_h_class = $mrent_tall_cards ? 'xl:min-h-[278px]' : '';
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[40px] xl:pt-[100px] pb-[40px] xl:pb-[100px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

		<?php if ($mrent_title !== '') : ?>
			<h2 class="font-display font-bold text-mrent-white text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] text-center xl:text-left">
				<?php echo esc_html($mrent_title); ?>
			</h2>
		<?php endif; ?>

		<?php /* Slide-wrapper c `!h-auto` обязателен: Swiper Grid иначе уносит высоту слайда в бесконечность. */ ?>
		<div class="mrent-cards-grid-swiper swiper w-full min-w-0 xl:!overflow-visible" data-mrent-cards-grid-swiper>
			<ul class="swiper-wrapper xl:!grid <?php echo esc_attr($mrent_cols_class); ?> xl:gap-[29px] list-none p-0 m-0">
				<?php foreach ($mrent_items as $mrent_item) :
					$mrent_icon       = (isset($mrent_item['icon']) && is_array($mrent_item['icon'])) ? $mrent_item['icon'] : [];
					$mrent_icon_url   = (string) ($mrent_icon['url'] ?? '');
					$mrent_icon_alt   = (string) ($mrent_icon['alt'] ?? '');
					$mrent_card_title = isset($mrent_item['title']) ? (string) $mrent_item['title'] : '';
				?>
					<li class="swiper-slide !h-auto xl:!w-auto">
						<article class="bg-[#252426] border border-[#302F31] rounded-[15px] p-[25px] xl:p-[40px] flex flex-col gap-[40px] xl:gap-[70px] <?php echo esc_attr($mrent_min_h_class); ?> h-full">
							<?php if ($mrent_icon_url !== '') : ?>
								<img
									src="<?php echo esc_url($mrent_icon_url); ?>"
									alt="<?php echo esc_attr($mrent_icon_alt); ?>"
									class="block w-[30px] h-[30px] xl:w-[60px] xl:h-[60px] object-contain shrink-0"
									loading="lazy"
									decoding="async"
								/>
							<?php endif; ?>
							<h3 class="font-display font-[700] xl:font-[600] text-mrent-white text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2] mt-auto">
								<?php echo esc_html($mrent_card_title); ?>
							</h3>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php if ($mrent_show_pagination) : ?>
			<div class="mrent-cards-grid-pagination xl:hidden flex gap-[10px] items-center justify-center" aria-hidden="true"></div>
		<?php endif; ?>
	</div>
</section>