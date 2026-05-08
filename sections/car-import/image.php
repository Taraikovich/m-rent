<?php
/**
 * «Авто под заказ»: фото-карусель Swiper.
 *
 * Figma:
 *   • desktop 2983:7088 — 1720×800, p-50 rounded-15, стрелки 65×65 без фона.
 *   • mobile  3004:9332 — 330×280, p-20/25 rounded-15, стрелки 50×50.
 *
 * Источник слайдов: ACF gallery `car_import_gallery` (если создана).
 * Фолбэк — одна картинка `assets/images/car-import.jpg`.
 *
 * Init: src/cars.js → initCarImportGallery() ловит `[data-mrent-car-import-gallery]`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_gallery = function_exists( 'get_field' ) ? (array) get_field( 'car_import_gallery' ) : [];
$mrent_slides  = [];

foreach ( $mrent_gallery as $img ) {
	if ( ! empty( $img['url'] ) ) {
		$mrent_slides[] = [ 'url' => $img['url'], 'alt' => $img['alt'] ?? '' ];
	}
}

if ( empty( $mrent_slides ) ) {
	$mrent_slides[] = [
		'url' => get_template_directory_uri() . '/assets/images/car-import.jpg',
		'alt' => '',
	];
}
?>

<section class="bg-mrent-black px-3.75 xl:px-25 pt-5 xl:pt-10">
	<div class="max-w-430 mx-auto">
		<div class="mrent-car-import-gallery swiper relative rounded-[15px] overflow-hidden h-70 xl:h-200 bg-[#252426]" data-mrent-car-import-gallery>
			<div class="swiper-wrapper">
				<?php foreach ( $mrent_slides as $slide ) : ?>
					<div class="swiper-slide">
						<img src="<?php echo esc_url( $slide['url'] ); ?>" alt="<?php echo esc_attr( $slide['alt'] ); ?>" class="size-full object-cover" loading="lazy" decoding="async">
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $mrent_slides ) > 1 ) : ?>
				<button
					type="button"
					class="mrent-car-import-prev absolute left-3.75 xl:left-12.5 top-1/2 -translate-y-1/2 size-12.5 xl:size-16.25 flex items-center justify-center rounded-lg bg-mrent-white/20 hover:bg-mrent-white/30 backdrop-blur-sm text-mrent-white transition-colors z-10"
					aria-label="<?php esc_attr_e( 'Предыдущее фото', 'm-rent' ); ?>"
				>
					<svg class="w-[18px] h-[18px] xl:w-6 xl:h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M15.5 4.5 8 12l7.5 7.5 1.5-1.5L11 12l6-6z"/>
					</svg>
				</button>
				<button
					type="button"
					class="mrent-car-import-next absolute right-3.75 xl:right-12.5 top-1/2 -translate-y-1/2 size-12.5 xl:size-16.25 flex items-center justify-center rounded-lg bg-mrent-white/20 hover:bg-mrent-white/30 backdrop-blur-sm text-mrent-white transition-colors z-10"
					aria-label="<?php esc_attr_e( 'Следующее фото', 'm-rent' ); ?>"
				>
					<svg class="w-[18px] h-[18px] xl:w-6 xl:h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M8.5 4.5 16 12l-7.5 7.5L7 18l6-6-6-6z"/>
					</svg>
				</button>
			<?php endif; ?>
		</div>
	</div>
</section>
