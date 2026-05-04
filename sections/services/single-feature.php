<?php
/**
 * Вторая секция страницы услуги: заголовок + длинное описание + карусель фото.
 * Figma desktop 2142:1629, mobile 2535:6027.
 *
 * Раскладка:
 *   Десктоп — два столбца в одну строку: H1 (74px Bold, w-548) слева,
 *     описание (30px Book, w-1015) справа. gap между ними auto (justify-between).
 *     Под ними карусель h-550, full width, rounded-15, со стрелками внутри.
 *   Мобайл  — H1 (28px Bold) + описание (16px Book) в столбце с gap-15.
 *     Ниже карусель h-330. Стрелки size-50 (vs 65 на десктопе).
 *
 * Если пусто `service_feature_title` — секция не выводится. Карусель показываем
 * только при наличии хотя бы одного изображения, но заголовок+описание могут
 * жить и без галереи.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_title       = (string) get_field( 'service_feature_title' );
$mrent_description = (string) get_field( 'service_feature_description' );
$mrent_gallery     = (array) get_field( 'service_feature_gallery' );

if ( $mrent_title === '' && $mrent_description === '' && empty( $mrent_gallery ) ) {
	return;
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

		<?php if ( $mrent_title !== '' || $mrent_description !== '' ) : ?>
			<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[15px] xl:gap-[20px] text-mrent-white w-full">
				<?php if ( $mrent_title !== '' ) : ?>
					<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2] xl:w-[548px] shrink-0">
						<?php echo esc_html( $mrent_title ); ?>
					</h2>
				<?php endif; ?>
				<?php if ( $mrent_description !== '' ) : ?>
					<p class="font-[400] text-[16px] xl:text-[30px] leading-[1.2] xl:w-[1015px]">
						<?php echo esc_html( $mrent_description ); ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $mrent_gallery ) ) : ?>
			<?php /* Высоту 330/550 ставим на сам .swiper, а не на родителя+inset-0.
			         Из swiper/css на .swiper приходит position:relative (та же
			         специфичность что и Tailwind .absolute, но swiper/css загружается
			         после, поэтому побеждает) → inset-0 переставал работать и слайды
			         получали 0 высоты. На прямой высоте всё стабильно. */ ?>
			<div class="relative w-full rounded-[15px] overflow-hidden">
				<div class="mrent-feature-gallery swiper w-full h-[330px] xl:h-[550px]" data-mrent-feature-gallery>
					<div class="swiper-wrapper">
						<?php foreach ( $mrent_gallery as $mrent_item ) :
							if ( empty( $mrent_item['url'] ) ) {
								continue;
							}
						?>
							<div class="swiper-slide">
								<img
									src="<?php echo esc_url( $mrent_item['url'] ); ?>"
									alt="<?php echo esc_attr( $mrent_item['alt'] ?? '' ); ?>"
									class="absolute inset-0 w-full h-full object-cover object-center"
									loading="lazy"
									decoding="async"
								>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( count( $mrent_gallery ) > 1 ) : ?>
					<?php /* Дизайн кнопок (Figma 2135:1411): полупрозрачный белый
					         фон + backdrop-blur, filled-chevron, как в cars/single-gallery.php. */ ?>
					<button
						type="button"
						class="mrent-feature-gallery-prev absolute left-[15px] xl:left-[50px] top-1/2 -translate-y-1/2 size-[50px] xl:size-[65px] flex items-center justify-center rounded-lg bg-mrent-white/20 hover:bg-mrent-white/30 backdrop-blur-sm text-mrent-white transition-colors z-10"
						aria-label="<?php esc_attr_e( 'Предыдущее фото', 'm-rent' ); ?>"
					>
						<svg class="w-[18px] h-[18px] xl:w-[24px] xl:h-[24px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
							<path d="M15.5 4.5 8 12l7.5 7.5 1.5-1.5L11 12l6-6z"/>
						</svg>
					</button>
					<button
						type="button"
						class="mrent-feature-gallery-next absolute right-[15px] xl:right-[50px] top-1/2 -translate-y-1/2 size-[50px] xl:size-[65px] flex items-center justify-center rounded-lg bg-mrent-white/20 hover:bg-mrent-white/30 backdrop-blur-sm text-mrent-white transition-colors z-10"
						aria-label="<?php esc_attr_e( 'Следующее фото', 'm-rent' ); ?>"
					>
						<svg class="w-[18px] h-[18px] xl:w-[24px] xl:h-[24px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
							<path d="M8.5 4.5 16 12l-7.5 7.5L7 18l6-6-6-6z"/>
						</svg>
					</button>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
