<?php

/**
 * Карусель фото на странице модели (single-car.php).
 *
 * Самодостаточная секция: берёт текущий пост из глобального цикла, собирает
 * слайды из featured image + ACF-галереи (`car_gallery`) и рендерит Swiper
 * с двумя стрелками-навигацией по краям.
 *
 * Если фото нет вообще — секция ничего не выводит.
 *
 * Figma: 399:695 (desktop) / 2326:3900 (mobile).
 */

if (! defined('ABSPATH')) {
	exit;
}

// Источник слайдов: ACF-галерея, если в ней что-то есть; иначе — featured image
// (обложка) как единственный фолбэк. Логика: featured используется в каталоге
// на карточке, а на single-странице приоритет у галереи; если редактор её
// заполнил — обложку в свайпер уже не подмешиваем, чтобы не дублировать.
$mrent_gallery = (array) get_field('car_gallery');
$mrent_slides  = [];

if (! empty($mrent_gallery)) {
	foreach ($mrent_gallery as $img) {
		if (! empty($img['url'])) {
			$mrent_slides[] = ['url' => $img['url'], 'alt' => $img['alt'] ?: get_the_title()];
		}
	}
} else {
	$mrent_featured_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
	if ($mrent_featured_url) {
		$mrent_slides[] = ['url' => $mrent_featured_url, 'alt' => get_the_title()];
	}
}

if (empty($mrent_slides)) {
	return;
}
?>

<?php /* Полная ширина контейнера (до 1720px). Высота 280 на мобайле, 600 на xl+.
         Стрелки 50/65px фростед-белые, прижаты к краям с отступом 20/50px.
         Absolute-позиционирование — Swiper-wrapper занимает весь контейнер. */ ?>
<div class="px-[15px] xl:px-[100px] my-[30px] xl:my-[70px]">
	<div class="mrent-car-gallery swiper relative max-w-[1720px] mx-auto rounded-[15px] overflow-hidden h-[280px] xl:h-[calc(100vh-100px)] bg-[#252426]" data-mrent-car-gallery>
		<div class="swiper-wrapper">
			<?php foreach ($mrent_slides as $slide) : ?>
				<div class="swiper-slide">
					<img src="<?php echo esc_url($slide['url']); ?>" alt="<?php echo esc_attr($slide['alt']); ?>" class="size-full object-cover" loading="eager" decoding="async">
				</div>
			<?php endforeach; ?>
		</div>
		<?php if (count($mrent_slides) > 1) : ?>
			<button type="button" aria-label="<?php esc_attr_e('Предыдущее фото', 'm-rent'); ?>" class="mrent-car-gallery-prev absolute left-[20px] xl:left-[50px] top-1/2 -translate-y-1/2 z-10 size-[50px] xl:size-[65px] flex items-center justify-center rounded-lg bg-mrent-white/20 hover:bg-mrent-white/30 backdrop-blur-sm text-mrent-white transition-colors">
				<svg class="w-[18px] h-[18px] xl:w-[24px] xl:h-[24px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path d="M15.5 4.5 8 12l7.5 7.5 1.5-1.5L11 12l6-6z" />
				</svg>
			</button>
			<button type="button" aria-label="<?php esc_attr_e('Следующее фото', 'm-rent'); ?>" class="mrent-car-gallery-next absolute right-[20px] xl:right-[50px] top-1/2 -translate-y-1/2 z-10 size-[50px] xl:size-[65px] flex items-center justify-center rounded-lg bg-mrent-white/20 hover:bg-mrent-white/30 backdrop-blur-sm text-mrent-white transition-colors">
				<svg class="w-[18px] h-[18px] xl:w-[24px] xl:h-[24px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path d="M8.5 4.5 16 12l-7.5 7.5L7 18l6-6-6-6z" />
				</svg>
			</button>
		<?php endif; ?>
	</div>
</div>