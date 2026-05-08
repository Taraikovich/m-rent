<?php
/**
 * «Авто под заказ»: секция «Наши преимущества» (6 карточек).
 *
 * Figma: desktop 2989:7617 / mobile 3020:7716.
 * Desktop (xl+): статичная 3-кол × 2-строч сетка через xl:!contents.
 * Mobile: Swiper из 2 слайдов по 3 карточки + полосочная пагинация.
 *
 * Init: `[data-mrent-car-import-benefits]` в src/cars.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_theme_uri = get_template_directory_uri();

// ACF-репитер `car_import_benefits`. Если пустой — дефолт из дизайна.
$mrent_acf_raw = (array) get_field( 'car_import_benefits' );
$mrent_benefits = [];

foreach ( $mrent_acf_raw as $mrent_row ) {
	$mrent_icon_url = '';
	if ( is_array( $mrent_row['icon'] ?? null ) && ! empty( $mrent_row['icon']['url'] ) ) {
		$mrent_icon_url = (string) $mrent_row['icon']['url'];
	}
	$mrent_title = (string) ( $mrent_row['title'] ?? '' );
	if ( $mrent_title === '' ) {
		continue;
	}
	$mrent_benefits[] = [ 'icon_url' => $mrent_icon_url, 'title' => $mrent_title ];
}

// Фолбэк: 6 карточек из дизайна с иконками из темы.
if ( ! $mrent_benefits ) {
	$mrent_defaults = [
		[ 'icon' => 'icon-car.svg',       'title' => 'Подбор авто под ваш запрос' ],
		[ 'icon' => 'icon-check.svg',     'title' => 'Проверка перед покупкой' ],
		[ 'icon' => 'icon-security.svg',  'title' => 'Надежность и проверенные поставщики' ],
		[ 'icon' => 'icon-people.svg',    'title' => 'Одна команда от запроса до поставки' ],
		[ 'icon' => 'icon-cash.svg',      'title' => 'Фиксированная стоимость — 300 USD' ],
		[ 'icon' => 'icon-handshake.svg', 'title' => 'Работа с Беларусью и Россией' ],
	];
	foreach ( $mrent_defaults as $mrent_d ) {
		$mrent_benefits[] = [
			'icon_url' => $mrent_theme_uri . '/assets/images/benefits/' . $mrent_d['icon'],
			'title'    => $mrent_d['title'],
		];
	}
}

$mrent_chunks = array_chunk( $mrent_benefits, 3 );
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[30px] xl:pt-[60px]">
	<div class="max-w-[1720px] mx-auto flex flex-col items-center xl:items-start gap-[30px] xl:gap-[60px]">

		<h2 class="font-display font-[700] text-[28px] xl:text-[74px] leading-[1.2] text-mrent-white text-center xl:text-left">
			<?php esc_html_e( 'Наши преимущества', 'm-rent' ); ?>
		</h2>

		<div class="mrent-car-import-benefits swiper w-full min-w-0 xl:!overflow-visible" data-mrent-car-import-benefits>
			<div class="swiper-wrapper xl:!grid xl:!grid-cols-3 xl:!gap-[30px]">
				<?php foreach ( $mrent_chunks as $mrent_chunk ) : ?>
					<div class="swiper-slide xl:!contents">
						<div class="flex flex-col gap-[10px] xl:contents">
							<?php foreach ( $mrent_chunk as $mrent_item ) : ?>
								<div class="bg-[#252426] border border-[#302f31] flex flex-col items-start gap-[30px] xl:gap-[70px] p-[25px] xl:p-[40px] rounded-[15px] w-full">
									<?php if ( $mrent_item['icon_url'] !== '' ) : ?>
										<img
											src="<?php echo esc_url( $mrent_item['icon_url'] ); ?>"
											alt=""
											class="shrink-0 size-7.5 xl:size-15"
											loading="lazy"
											decoding="async"
											aria-hidden="true"
										>
									<?php endif; ?>
									<p class="font-[600] leading-[1.2] text-mrent-white text-[14px] xl:text-[24px]">
										<?php echo esc_html( $mrent_item['title'] ); ?>
									</p>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php /* Mobile-only: полосочная пагинация (по одной на swiper-slide). */ ?>
		<div class="xl:hidden mrent-car-import-benefits-pagination flex gap-[10px] justify-center"></div>

	</div>
</section>
