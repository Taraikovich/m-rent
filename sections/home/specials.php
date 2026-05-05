<?php
/**
 * Главная: секция «Спецпредложения» — простой свайпер баннеров.
 *
 * Контент: ACF-группа «Главная», таб «Спецпредложения».
 *   • home_specials_title    — заголовок (пусто → «Спецпредложения»).
 *   • home_specials_images   — gallery с баннерами; пусто → дефолтные
 *     assets/images/special-offer-{1..4}.png.
 *
 * Layout (Figma 171:389 desktop / 2323:3796 mobile):
 *   • Mobile (<xl): заголовок 28px Bold по центру → Swiper по 2 карточки
 *     в видимой области (gap 10px, пропорции 408×613) → пагинация-полоски
 *     (40×4, активная белая, неактивные #302F31).
 *   • Desktop (xl+): «заголовок 74px Bold слева ↔ две жёлтые кнопки 65×65
 *     prev/next справа», ниже Swiper по 4 карточки в ряд (gap 30px).
 *     Если в галерее ровно 4 баннера — Swiper всё равно жив, но navigation
 *     остаётся неактивным (Swiper сам делает кнопки disabled).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field( 'home_specials_title', $mrent_page_id );
if ( $mrent_title === '' ) {
	$mrent_title = __( 'Спецпредложения', 'm-rent' );
}

$mrent_gallery = get_field( 'home_specials_images', $mrent_page_id );
$mrent_images  = [];
if ( is_array( $mrent_gallery ) ) {
	foreach ( $mrent_gallery as $mrent_item ) {
		if ( ! empty( $mrent_item['url'] ) ) {
			$mrent_images[] = [
				'url' => $mrent_item['url'],
				'alt' => $mrent_item['alt'] ?? '',
			];
		}
	}
}
if ( empty( $mrent_images ) ) {
	$mrent_dir = get_template_directory_uri() . '/assets/images';
	for ( $i = 1; $i <= 4; $i++ ) {
		$mrent_images[] = [
			'url' => $mrent_dir . '/special-offer-' . $i . '.png',
			'alt' => '',
		];
	}
}
?>

<section class="bg-mrent-black py-[60px] xl:py-[100px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

			<?php /* ── Заголовок (+ кнопки prev/next справа на десктопе) ── */ ?>
			<div class="flex items-center justify-between gap-[15px]">
				<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2] text-mrent-white text-center xl:text-left w-full xl:w-auto">
					<?php echo esc_html( $mrent_title ); ?>
				</h2>

				<div class="hidden xl:flex gap-[10px] shrink-0">
					<button type="button" class="mrent-specials-prev bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] size-[65px] transition-colors disabled:opacity-50 disabled:cursor-not-allowed" aria-label="<?php esc_attr_e( 'Предыдущий', 'm-rent' ); ?>">
						<svg width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M10 1L1 10L10 19" stroke="#1E1D1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
					<button type="button" class="mrent-specials-next bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] size-[65px] transition-colors disabled:opacity-50 disabled:cursor-not-allowed" aria-label="<?php esc_attr_e( 'Следующий', 'm-rent' ); ?>">
						<svg width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M1 1L10 10L1 19" stroke="#1E1D1F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
				</div>
			</div>

			<?php /* ── Swiper-картинок ──
			         min-w-0 — Swiper как flex-item внутри `flex flex-col` родителя
			         иначе подстраивается под intrinsic ширину контента и расползается. */ ?>
			<div class="mrent-specials swiper w-full min-w-0" data-mrent-specials>
				<div class="swiper-wrapper">
					<?php foreach ( $mrent_images as $mrent_image ) : ?>
						<div class="swiper-slide">
							<div class="aspect-[408/613] rounded-[15px] overflow-hidden bg-[#252426]">
								<img src="<?php echo esc_url( $mrent_image['url'] ); ?>" alt="<?php echo esc_attr( $mrent_image['alt'] ); ?>" class="block size-full object-cover" loading="lazy" decoding="async">
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* ── Mobile-only: пагинация-полоски ── */ ?>
			<div class="xl:hidden mrent-specials-pagination flex flex-wrap gap-[10px] justify-center"></div>

		</div>
	</div>
</section>
