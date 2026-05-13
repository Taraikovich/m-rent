<?php
/**
 * Секция «Тарифы и пакеты» / «Дополнительные опции» на странице услуги
 * (Figma 2960:7082 — packages, 2120:1493 — options, 2535:6696 — mobile).
 *
 * Структура:
 *   • Mobile (<xl): сверху native <select> с двумя пунктами (тарифы / опции).
 *     Под ним — активная панель: H2 28px Bold, подзаголовок (только у packages),
 *     карточка-Swiper по 1 шт с пагинацией-полосками + жёлтые prev/next 55×55
 *     по краям контейнера.
 *   • Desktop (xl+): H2 74px Bold + пилюли 2×(300×65) в одну строку
 *     (justify-between). Под ними — flex-row карточек: 3 шт для пакетов
 *     (h-872), 3 шт для опций (h-447). Свайпер уничтожается.
 *
 * Переключение табов — JS в src/services.js (`initSinglePackagesTabs`).
 *
 * Источники: ACF group_mrent_service → service_packages_*.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_pkg_title    = (string) get_field( 'service_packages_title' );
$mrent_pkg_subtitle = (string) get_field( 'service_packages_subtitle' );
$mrent_pkg_items    = (array) get_field( 'service_packages_items' );
$mrent_pkg_options  = (array) get_field( 'service_packages_options' );

// Если ни одной карточки нет вовсе — секцию не выводим.
if ( empty( $mrent_pkg_items ) && empty( $mrent_pkg_options ) ) {
	return;
}

$mrent_pkg_tab1 = get_field( 'service_packages_tab1_label' ) ?: __( 'Тарифы и пакеты', 'm-rent' );
$mrent_pkg_tab2 = get_field( 'service_packages_tab2_label' ) ?: __( 'Дополнительные опции', 'm-rent' );

// Заголовок второй вкладки = её label (отдельного поля нет — Figma всегда
// показывает «Дополнительные опции» как H2).
$mrent_pkg_options_title = $mrent_pkg_tab2;

$mrent_pkg_note_label = get_field( 'service_packages_note_label' ) ?: __( 'Важно', 'm-rent' );
$mrent_pkg_note       = (string) get_field( 'service_packages_note' );

$mrent_pkg_has_items   = ! empty( $mrent_pkg_items );
$mrent_pkg_has_options = ! empty( $mrent_pkg_options );

// Tabs для итерации (только те, где есть контент). Первый — активный по умолчанию.
$mrent_pkg_tabs = [];
if ( $mrent_pkg_has_items ) {
	$mrent_pkg_tabs[] = [ 'id' => 'packages', 'label' => $mrent_pkg_tab1 ];
}
if ( $mrent_pkg_has_options ) {
	$mrent_pkg_tabs[] = [ 'id' => 'options', 'label' => $mrent_pkg_tab2 ];
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]" data-mrent-single-packages>
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px] text-mrent-white">

		<?php if ( count( $mrent_pkg_tabs ) > 1 ) : ?>
			<?php /* Mobile select (Figma 2535:6697): белая пилюля 55h c chevron справа.
			         На xl+ скрыт — там есть пилюли в шапке каждой панели. */ ?>
			<div class="relative xl:hidden">
				<select
					data-mrent-pkg-select
					class="appearance-none bg-mrent-white text-mrent-black w-full h-[55px] rounded-[15px] pl-[15px] pr-[40px] font-[500] text-[14px] cursor-pointer"
				>
					<?php foreach ( $mrent_pkg_tabs as $mrent_pkg_t ) : ?>
						<option value="<?php echo esc_attr( $mrent_pkg_t['id'] ); ?>"><?php echo esc_html( $mrent_pkg_t['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<svg class="absolute right-[15px] top-1/2 -translate-y-1/2 pointer-events-none w-[10px] h-[10px] text-mrent-black" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<polyline points="1 3 5 8 9 3"/>
				</svg>
			</div>
		<?php endif; ?>

		<?php /* ---------- Панель: ТАРИФЫ И ПАКЕТЫ ---------- */ ?>
		<?php if ( $mrent_pkg_has_items ) : ?>
			<div data-mrent-pkg-panel data-mrent-pkg-id="packages" class="flex flex-col gap-[30px] xl:gap-[60px]">

				<?php /* Шапка: H2 + (на xl) пилюли в строке. */ ?>
				<div class="flex flex-col gap-[20px] xl:gap-[30px]">
					<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[20px]">
						<?php if ( $mrent_pkg_title !== '' ) : ?>
							<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2]">
								<?php echo esc_html( $mrent_pkg_title ); ?>
							</h2>
						<?php endif; ?>

						<?php if ( count( $mrent_pkg_tabs ) > 1 ) : ?>
							<div class="hidden xl:flex gap-[20px] shrink-0">
								<button
									type="button"
									data-mrent-pkg-trigger
									data-mrent-pkg-id="packages"
									aria-current="true"
									class="bg-[#252426] text-mrent-white aria-[current=true]:bg-mrent-white aria-[current=true]:text-mrent-black flex items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] font-[500] text-[20px] whitespace-nowrap transition-colors cursor-pointer"
								>
									<?php echo esc_html( $mrent_pkg_tab1 ); ?>
								</button>
								<button
									type="button"
									data-mrent-pkg-trigger
									data-mrent-pkg-id="options"
									class="bg-[#252426] text-mrent-white aria-[current=true]:bg-mrent-white aria-[current=true]:text-mrent-black flex items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] font-[500] text-[20px] whitespace-nowrap transition-colors cursor-pointer"
								>
									<?php echo esc_html( $mrent_pkg_tab2 ); ?>
								</button>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $mrent_pkg_subtitle !== '' ) : ?>
						<p class="font-[400] text-[16px] xl:text-[30px] leading-[1.2] whitespace-pre-line">
							<?php echo esc_html( $mrent_pkg_subtitle ); ?>
						</p>
					<?php endif; ?>
				</div>

				<?php /* Mobile (<xl) — Swiper по 1 карточке.
				         Desktop (xl+) — swiper уничтожен; .swiper-wrapper остаётся
				         display:flex, на слайдах включаем `xl:!flex-1 xl:!w-auto`. */ ?>
				<div class="mrent-packages-swiper swiper w-full min-w-0 xl:!overflow-visible" data-mrent-packages-swiper>
					<div class="swiper-wrapper xl:gap-[20px] xl:items-stretch">
						<?php foreach ( $mrent_pkg_items as $mrent_pkg ) :
							$mrent_pkg_item_title    = (string) ( $mrent_pkg['title'] ?? '' );
							$mrent_pkg_item_subtitle = (string) ( $mrent_pkg['subtitle'] ?? '' );
							$mrent_pkg_item_specs    = (array) ( $mrent_pkg['specs'] ?? [] );
							$mrent_pkg_item_price    = mrent_convert_usd_in_text( (string) ( $mrent_pkg['price'] ?? '' ) );
							$mrent_pkg_item_unit     = (string) ( $mrent_pkg['price_unit'] ?? '' );
							$mrent_pkg_item_cta      = $mrent_pkg['cta_text'] ?? '';
							if ( ! $mrent_pkg_item_cta ) {
								$mrent_pkg_item_cta = __( 'Забронировать услугу', 'm-rent' );
							}
							$mrent_pkg_item_url = ! empty( $mrent_pkg['cta_url'] ) ? (string) $mrent_pkg['cta_url'] : '#booking';
						?>
							<div class="swiper-slide !h-auto xl:!flex-1 xl:!w-auto">
								<article class="bg-[#252426] border border-[#302F31] rounded-[15px] p-[20px] xl:p-[40px] flex flex-col gap-[30px] xl:gap-[40px] xl:min-w-0 xl:h-[872px] xl:justify-between">

									<div class="flex flex-col gap-[30px] xl:gap-[40px]">
										<div class="flex flex-col gap-[15px] xl:gap-[20px] xl:h-[160px] xl:justify-between">
											<div class="flex flex-col gap-[15px] xl:gap-[20px]">
												<?php if ( $mrent_pkg_item_title !== '' ) : ?>
													<h3 class="font-[600] text-[22px] xl:text-[35px] leading-[1.2]">
														<?php echo esc_html( $mrent_pkg_item_title ); ?>
													</h3>
												<?php endif; ?>
												<?php if ( $mrent_pkg_item_subtitle !== '' ) : ?>
													<p class="font-[400] text-[16px] xl:text-[24px] leading-[1.2]">
														<?php echo esc_html( $mrent_pkg_item_subtitle ); ?>
													</p>
												<?php endif; ?>
											</div>
											<hr class="border-0 border-t border-mrent-white/30 m-0 w-full" />
										</div>

										<?php if ( ! empty( $mrent_pkg_item_specs ) ) : ?>
											<ul class="flex flex-col gap-[10px] m-0 p-0 list-none">
												<?php foreach ( $mrent_pkg_item_specs as $mrent_spec ) :
													$mrent_spec_label = (string) ( $mrent_spec['label'] ?? '' );
													$mrent_spec_value = (string) ( $mrent_spec['value'] ?? '' );
													if ( $mrent_spec_label === '' && $mrent_spec_value === '' ) {
														continue;
													}
												?>
													<li class="text-[14px] xl:text-[22px] leading-[1.24]">
														<?php if ( $mrent_spec_label !== '' ) : ?>
															<span class="font-[700]"><?php echo esc_html( $mrent_spec_label ); ?></span>
														<?php endif; ?>
														<?php if ( $mrent_spec_value !== '' ) : ?>
															<span class="font-[400]"> <?php echo esc_html( $mrent_spec_value ); ?></span>
														<?php endif; ?>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</div>

									<div class="flex flex-col gap-[20px] xl:gap-[40px]">
										<hr class="border-0 border-t border-mrent-white/30 m-0" />
										<?php if ( $mrent_pkg_item_price !== '' || $mrent_pkg_item_unit !== '' ) : ?>
											<p class="leading-[1.2]">
												<?php if ( $mrent_pkg_item_price !== '' ) : ?>
													<span class="font-[800] text-[34px] xl:text-[50px]"><?php echo esc_html( $mrent_pkg_item_price ); ?></span>
												<?php endif; ?>
												<?php if ( $mrent_pkg_item_unit !== '' ) : ?>
													<span class="font-[400] text-[20px] xl:text-[30px]"><?php echo $mrent_pkg_item_price !== '' ? '/' : ''; ?><?php echo esc_html( $mrent_pkg_item_unit ); ?></span>
												<?php endif; ?>
											</p>
										<?php endif; ?>
										<a
											href="<?php echo esc_url( $mrent_pkg_item_url ); ?>"
											class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full px-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] whitespace-nowrap transition-colors"
										>
											<?php echo esc_html( $mrent_pkg_item_cta ); ?>
										</a>
									</div>
								</article>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php /* Mobile nav: prev | пагинация | next, тянется от края до края контейнера. */ ?>
				<div class="py-[20px] flex items-center justify-between gap-[15px] xl:hidden">
					<button
						type="button"
						class="mrent-packages-prev bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
						aria-label="<?php esc_attr_e( 'Предыдущий тариф', 'm-rent' ); ?>"
					>
						<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
							<polyline points="8 1 1 8 8 15"/>
						</svg>
					</button>
					<div class="mrent-packages-pagination flex flex-1 items-center justify-center gap-[10px]"></div>
					<button
						type="button"
						class="mrent-packages-next bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
						aria-label="<?php esc_attr_e( 'Следующий тариф', 'm-rent' ); ?>"
					>
						<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
							<polyline points="1 1 8 8 1 15"/>
						</svg>
					</button>
				</div>

				<?php if ( $mrent_pkg_note !== '' ) : ?>
					<p class="font-[400] text-[16px] xl:text-[30px] leading-[1.2]">
						<?php if ( $mrent_pkg_note_label !== '' ) : ?>
							<span class="font-[700] uppercase"><?php echo esc_html( $mrent_pkg_note_label ); ?>: </span>
						<?php endif; ?>
						<?php echo esc_html( $mrent_pkg_note ); ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php /* ---------- Панель: ДОПОЛНИТЕЛЬНЫЕ ОПЦИИ ---------- */ ?>
		<?php if ( $mrent_pkg_has_options ) : ?>
			<div
				data-mrent-pkg-panel
				data-mrent-pkg-id="options"
				class="flex flex-col gap-[30px] xl:gap-[60px]"
				<?php echo $mrent_pkg_has_items ? 'hidden' : ''; ?>
			>
				<?php /* Шапка опций — те же пилюли, но активна вторая. */ ?>
				<div class="flex flex-col gap-[20px] xl:gap-[30px]">
					<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[20px]">
						<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2]">
							<?php echo esc_html( $mrent_pkg_options_title ); ?>
						</h2>

						<?php if ( count( $mrent_pkg_tabs ) > 1 ) : ?>
							<div class="hidden xl:flex gap-[20px] shrink-0">
								<button
									type="button"
									data-mrent-pkg-trigger
									data-mrent-pkg-id="packages"
									class="bg-[#252426] text-mrent-white aria-[current=true]:bg-mrent-white aria-[current=true]:text-mrent-black flex items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] font-[500] text-[20px] whitespace-nowrap transition-colors cursor-pointer"
								>
									<?php echo esc_html( $mrent_pkg_tab1 ); ?>
								</button>
								<button
									type="button"
									data-mrent-pkg-trigger
									data-mrent-pkg-id="options"
									aria-current="true"
									class="bg-[#252426] text-mrent-white aria-[current=true]:bg-mrent-white aria-[current=true]:text-mrent-black flex items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] font-[500] text-[20px] whitespace-nowrap transition-colors cursor-pointer"
								>
									<?php echo esc_html( $mrent_pkg_tab2 ); ?>
								</button>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="mrent-options-swiper swiper w-full min-w-0 xl:!overflow-visible" data-mrent-options-swiper>
					<div class="swiper-wrapper xl:gap-[20px] xl:items-stretch">
						<?php foreach ( $mrent_pkg_options as $mrent_opt ) :
							$mrent_opt_icon  = is_array( $mrent_opt['icon'] ?? null ) ? $mrent_opt['icon'] : null;
							$mrent_opt_title = (string) ( $mrent_opt['title'] ?? '' );
							$mrent_opt_price = mrent_convert_usd_in_text( (string) ( $mrent_opt['price'] ?? '' ) );
							$mrent_opt_unit  = (string) ( $mrent_opt['price_unit'] ?? '' );
							$mrent_opt_cta   = $mrent_opt['cta_text'] ?? '';
							if ( ! $mrent_opt_cta ) {
								$mrent_opt_cta = __( 'Забронировать услугу', 'm-rent' );
							}
							$mrent_opt_url = ! empty( $mrent_opt['cta_url'] ) ? (string) $mrent_opt['cta_url'] : '#booking';
						?>
							<div class="swiper-slide !h-auto xl:!flex-1 xl:!w-auto">
								<article class="bg-[#252426] border border-[#302F31] rounded-[15px] p-[20px] xl:p-[40px] flex flex-col gap-[30px] xl:gap-[40px] xl:min-w-0 xl:h-[447px]">
									<?php if ( $mrent_opt_icon && ! empty( $mrent_opt_icon['url'] ) ) : ?>
										<img
											src="<?php echo esc_url( $mrent_opt_icon['url'] ); ?>"
											alt="<?php echo esc_attr( $mrent_opt_icon['alt'] ?? '' ); ?>"
											class="size-[50px] xl:size-[60px] object-contain shrink-0"
											loading="lazy"
											decoding="async"
										>
									<?php endif; ?>

									<div class="flex flex-col gap-[15px] xl:gap-[30px] flex-1">
										<?php if ( $mrent_opt_title !== '' ) : ?>
											<p class="font-[400] text-[16px] xl:text-[30px] leading-[1.2]">
												<?php echo esc_html( $mrent_opt_title ); ?>
											</p>
										<?php endif; ?>
										<?php if ( $mrent_opt_price !== '' || $mrent_opt_unit !== '' ) : ?>
											<p class="leading-[1.2]">
												<?php if ( $mrent_opt_price !== '' ) : ?>
													<span class="font-[800] text-[24px] xl:text-[40px]"><?php echo esc_html( $mrent_opt_price ); ?></span>
												<?php endif; ?>
												<?php if ( $mrent_opt_unit !== '' ) : ?>
													<span class="font-[400] text-[14px] xl:text-[24px]"><?php echo $mrent_opt_price !== '' ? '/' : ''; ?><?php echo esc_html( $mrent_opt_unit ); ?></span>
												<?php endif; ?>
											</p>
										<?php endif; ?>
									</div>

									<a
										href="<?php echo esc_url( $mrent_opt_url ); ?>"
										class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full px-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] whitespace-nowrap transition-colors"
									>
										<?php echo esc_html( $mrent_opt_cta ); ?>
									</a>
								</article>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php /* Mobile nav для опций. */ ?>
				<div class="py-[20px] flex items-center justify-between gap-[15px] xl:hidden">
					<button
						type="button"
						class="mrent-options-prev bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
						aria-label="<?php esc_attr_e( 'Предыдущая опция', 'm-rent' ); ?>"
					>
						<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
							<polyline points="8 1 1 8 8 15"/>
						</svg>
					</button>
					<div class="mrent-options-pagination flex flex-1 items-center justify-center gap-[10px]"></div>
					<button
						type="button"
						class="mrent-options-next bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
						aria-label="<?php esc_attr_e( 'Следующая опция', 'm-rent' ); ?>"
					>
						<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
							<polyline points="1 1 8 8 1 15"/>
						</svg>
					</button>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>
