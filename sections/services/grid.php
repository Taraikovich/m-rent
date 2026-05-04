<?php
/**
 * Сетка услуг по секциям-категориям.
 *
 * Десктоп (xl+):
 *   • count ∈ [2..4] → один ряд из N карточек, ширина равная (flex-1).
 *   • count ≥ 5      → чередующиеся ряды по 3 и 4 карточки (Figma 5095:7367):
 *                      первый ряд — 3 карточки (больше по ширине),
 *                      второй ряд — 4 карточки, и т.д.
 *   • count == 1 и фильтр не активен → секция скрывается (но пилюля категории
 *                      в фильтре остаётся, чтобы при клике увидеть услугу одну).
 *
 * Мобайл (<xl):
 *   • count ≤ 7  → обычный stack (одна колонка).
 *   • count > 7  → Swiper с пагинацией: 4 карточки в слайде (один под другим),
 *                  кнопки prev/next + полоски-табы (Figma 2535:5717).
 *
 * NB: card.php читает `global $post` и `get_field()`, поэтому для рендера по ID
 * мы вручную поднимаем глобальный $post через setup_postdata().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_terms       = mrent_get_service_categories();
$mrent_active_slug = isset( $_GET['cat'] ) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : '';
if ( $mrent_active_slug === 'all' ) {
	$mrent_active_slug = '';
}

if ( $mrent_active_slug !== '' ) {
	$mrent_terms = array_values( array_filter( $mrent_terms, fn( $t ) => $t->slug === $mrent_active_slug ) );
}

if ( empty( $mrent_terms ) ) : ?>
	<section class="bg-mrent-black pt-[40px] xl:pt-[80px]">
		<div class="px-[15px] xl:px-[100px]">
			<p class="max-w-[1720px] mx-auto text-mrent-white text-center text-[18px] py-[60px]">
				<?php esc_html_e( 'Категории не найдены.', 'm-rent' ); ?>
			</p>
		</div>
	</section>
<?php
	return;
endif;

/**
 * Разбивает массив постов на чередующиеся чанки 3-4-3-4-... (для desktop ≥5 карточек).
 * Хвост — что осталось (может быть меньше «положенного» размера).
 *
 * @param int[] $ids
 * @return int[][]
 */
$mrent_chunks_alternating = function ( array $ids ) : array {
	$sizes  = [ 3, 4 ];
	$rows   = [];
	$offset = 0;
	$i      = 0;
	$count  = count( $ids );
	while ( $offset < $count ) {
		$size   = $sizes[ $i % 2 ];
		$rows[] = array_slice( $ids, $offset, $size );
		$offset += $size;
		$i++;
	}
	return $rows;
};

/**
 * Рендер одной карточки по ID. Поднимает global $post, чтобы card.php
 * (использующий get_field/the_title) работал из контекста.
 */
$mrent_render_card = function ( int $id ) : void {
	global $post;
	$post = get_post( $id );
	if ( ! $post ) {
		return;
	}
	setup_postdata( $post );
	get_template_part( 'sections/services/card' );
};
?>

<section class="bg-mrent-black pt-[40px] xl:pt-[80px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[40px] xl:gap-[80px]">

			<?php foreach ( $mrent_terms as $mrent_term ) :
				$mrent_post_ids = get_posts( [
					'post_type'      => 'service',
					'posts_per_page' => -1,
					'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
					'tax_query'      => [
						[
							'taxonomy' => 'service_category',
							'field'    => 'term_id',
							'terms'    => $mrent_term->term_id,
						],
					],
					'fields'         => 'ids',
					'no_found_rows'  => true,
				] );

				$mrent_count = count( $mrent_post_ids );
				if ( $mrent_count === 0 ) {
					continue;
				}

				// Скрываем категории с одной карточкой на «Все услуги», но при
				// явном фильтре по этому slug всё равно показываем.
				if ( $mrent_count === 1 && $mrent_active_slug === '' ) {
					continue;
				}

				$mrent_heading = (string) get_field( 'service_category_heading', 'term_' . $mrent_term->term_id );
				if ( $mrent_heading === '' ) {
					$mrent_heading = $mrent_term->name;
				}

				$mrent_rows          = ( $mrent_count <= 4 ) ? [ $mrent_post_ids ] : $mrent_chunks_alternating( $mrent_post_ids );
				$mrent_mobile_swiper = $mrent_count > 7;
				$mrent_pages         = $mrent_mobile_swiper ? array_chunk( $mrent_post_ids, 4 ) : [];
			?>

				<div class="flex flex-col gap-[30px] xl:gap-[60px]" id="cat-<?php echo esc_attr( $mrent_term->slug ); ?>">
					<h2 class="text-mrent-white font-[700] text-[28px] xl:text-[74px] leading-[1.2] w-full">
						<?php echo esc_html( $mrent_heading ); ?>
					</h2>

					<?php /* ── Desktop: чередующиеся ряды через flex-1 (ширина карточки
					           зависит от количества в ряду — 3 или 4 — автоматически). */ ?>
					<div class="hidden xl:flex flex-col gap-[20px]">
						<?php foreach ( $mrent_rows as $mrent_row ) : ?>
							<div class="flex gap-[20px] items-stretch">
								<?php foreach ( $mrent_row as $mrent_id ) : ?>
									<div class="flex-1 min-w-0">
										<?php $mrent_render_card( (int) $mrent_id ); ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>

					<?php /* ── Mobile: либо обычный стек, либо swiper по 4 карточки. */ ?>
					<?php if ( $mrent_mobile_swiper ) : ?>
						<div class="xl:hidden flex flex-col gap-[30px]">
							<?php /* w-full + min-w-0: фиксируем ширину Swiper'а внутри
							         flex-col-родителя. Без этого .swiper auto-size берёт
							         ширину от .swiper-wrapper, а тот от слайдов — а слайды
							         от .swiper. Получается циркулярный layout, и контейнер
							         бесконечно расширяется. */ ?>
							<div class="mrent-services-swiper swiper w-full min-w-0 overflow-hidden" data-mrent-services-swiper>
								<div class="swiper-wrapper">
									<?php foreach ( $mrent_pages as $mrent_page_ids ) : ?>
										<div class="swiper-slide">
											<div class="flex flex-col gap-[10px]">
												<?php foreach ( $mrent_page_ids as $mrent_id ) : ?>
													<?php $mrent_render_card( (int) $mrent_id ); ?>
												<?php endforeach; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>

							<?php /* Навигация: жёлтые кнопки prev/next + полоски-пагинация
							         (Figma 2535:5717). chevron 8.25×15. */ ?>
							<div class="flex items-center justify-between gap-[10px]">
								<button
									type="button"
									class="mrent-services-prev bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
									aria-label="<?php esc_attr_e( 'Предыдущие услуги', 'm-rent' ); ?>"
								>
									<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
										<polyline points="8 1 1 8 8 15"/>
									</svg>
								</button>
								<div class="mrent-services-pagination flex items-center gap-[10px]"></div>
								<button
									type="button"
									class="mrent-services-next bg-mrent-yellow rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black"
									aria-label="<?php esc_attr_e( 'Следующие услуги', 'm-rent' ); ?>"
								>
									<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
										<polyline points="1 1 8 8 1 15"/>
									</svg>
								</button>
							</div>
						</div>
					<?php else : ?>
						<div class="xl:hidden flex flex-col gap-[10px]">
							<?php foreach ( $mrent_post_ids as $mrent_id ) : ?>
								<?php $mrent_render_card( (int) $mrent_id ); ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php wp_reset_postdata(); ?>

			<?php endforeach; ?>

		</div>
	</div>
</section>
