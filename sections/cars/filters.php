<?php

/**
 * Фильтры по категориям автопарка.
 *
 * Десктоп: ровный ряд карточек, активная — белая (388:365), остальные — серые
 * на тёмном фоне с обводкой (388:368). Картинка категории — силуэт авто.
 *
 * Мобильный: тот же ряд, но прокручивается Swiper'ом со «свободным» прокручиванием
 * и визуальным заездом за правый край (правый паддинг сокращается, slidesOffset).
 *
 * Активная категория определяется по queried object: если открыт taxonomy-archive
 * `car_category` — активным становится этот терм, иначе — «Все автомобили» (ссылка
 * на /cars/).
 *
 * Figma: 388:361.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_terms = mrent_get_car_categories();

$mrent_active_slug = '';
if (is_tax('car_category')) {
	$mrent_active_slug = (string) get_queried_object()->slug;
}

$mrent_archive_url = get_post_type_archive_link('car') ?: home_url('/cars/');

// Силуэт для виртуальной пилюли «Все автомобили» — это не терм, поэтому
// картинка лежит в assets/images/ как тема-ассет, а не в медиатеке.
$mrent_all_image = [
	'url' => get_stylesheet_directory_uri() . '/assets/images/cars-category-all.png',
	'alt' => __('Все автомобили', 'm-rent'),
];

/**
 * Один пункт фильтра. Возвращает HTML-разметку.
 * Стили карточки переключаются по $is_active. Иконка — необязательная.
 */
$mrent_render_pill = function (string $url, string $label, ?array $image, bool $is_active): string {
	$base = 'group flex flex-col items-center justify-between gap-[20px] rounded-[15px] p-[20px] w-[160px] shrink-0 xl:w-full xl:max-w-[197px] xl:mx-auto transition-colors';
	$skin = $is_active
		? 'bg-mrent-white text-mrent-black'
		: 'bg-[#252426] border border-[#302f31] text-mrent-white hover:bg-[#2f2e30]';
	$weight = $is_active ? 'font-[600]' : 'font-[400]'; // Demi vs Book
	ob_start();
?>
	<a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($base . ' ' . $skin); ?>">
		<span class="text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.2] text-center whitespace-nowrap <?php echo esc_attr($weight); ?>">
			<?php echo esc_html($label); ?>
		</span>
		<?php if (! empty($image['url'])) : ?>
			<span class="block h-[55px] xl:h-[65px] w-full">
				<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: $label); ?>" class="size-full object-contain" loading="lazy" decoding="async">
			</span>
		<?php endif; ?>
	</a>
<?php
	return (string) ob_get_clean();
};
?>

<section class="bg-mrent-black pt-[30px] xl:pt-[50px] xl:!px-[100px] ">
	<?php /* Wrapper Swiper'а. До xl (1280px) — горизонтальная карусель
	         (свободный скролл, edge-bleed справа). На xl+ — статичный flex-ряд
	         без JS: пилюли flex-1 равной ширины масштабируются под контейнер
	         (max-w 197px по дизайну), поэтому не переполняют его. Класс
	         .mrent-cars-filter — точка инициализации в src/cars.js. */ ?>
	<div
		class="mrent-cars-filter swiper xl:!overflow-visible max-w-[1720px] xl:mx-auto"
		data-mrent-cars-filter>
		<div class="swiper-wrapper xl:!flex xl:!gap-[20px] xl:!transform-none xl:!w-auto xl:items-stretch">
			<?php /* «Все автомобили» — всегда первый, ведёт на /cars/ */ ?>
			<div class="swiper-slide !w-auto xl:!flex-1 xl:!min-w-0">
				<?php
				echo $mrent_render_pill(
					$mrent_archive_url,
					__('Все автомобили', 'm-rent'),
					$mrent_all_image,
					$mrent_active_slug === ''
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — функция уже экранирует
				?>
			</div>

			<?php foreach ($mrent_terms as $term) :
				$image_field = get_field('category_image', 'car_category_' . $term->term_id);
				$image       = is_array($image_field) ? $image_field : null;
			?>
				<div class="swiper-slide !w-auto xl:!flex-1 xl:!min-w-0">
					<?php
					echo $mrent_render_pill(
						get_term_link($term),
						$term->name,
						$image,
						$mrent_active_slug === $term->slug
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>