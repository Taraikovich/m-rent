<?php
/**
 * Сетка карточек в архиве блога (Figma 2147:1870 / 2341:3145).
 *
 * Раскладки:
 *   • Desktop (xl+): статичная сетка 3 × N (Figma 2147:1870 — flex-wrap, 3 ряда
 *     по 4 карточки на демо-наборе из 12 постов). Swiper отключён.
 *   • Mobile  (<xl): Swiper c пагинацией полосками (Figma 2341:3266) — каждый
 *     слайд содержит до 4 карточек в одну колонку, под слайдером жёлтые
 *     prev/next и mrent-bullet-bar (как в sections/services/grid.php).
 *
 * Переходы между WP-страницами архива (`posts_per_page=12` бампится в
 * functions.php) — стандартная WP-пагинация под свайпером.
 *
 * NB: card.php тянет `global $post` и `get_field()`, поэтому для рендера по ID
 * мы поднимаем глобальный $post через setup_postdata().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! have_posts() ) : ?>
	<section class="bg-mrent-black pt-[40px] xl:pt-[80px]">
		<div class="px-[15px] xl:px-[100px]">
			<p class="max-w-[1720px] mx-auto text-mrent-white text-center text-[18px] py-[60px]">
				<?php esc_html_e( 'Записей не найдено.', 'm-rent' ); ?>
			</p>
		</div>
	</section>
<?php
	return;
endif;

global $wp_query;
$mrent_post_ids = wp_list_pluck( $wp_query->posts, 'ID' );

// Мобильный свайпер: страницы по 4 карточки.
$mrent_mobile_pages = array_chunk( $mrent_post_ids, 4 );

/**
 * Рендер одной карточки по ID. Поднимает global $post, чтобы card.php
 * (использующий get_the_*, the_post_thumbnail) работал из контекста.
 */
$mrent_render_card = function ( int $id ) : void {
	global $post;
	$post = get_post( $id );
	if ( ! $post ) {
		return;
	}
	setup_postdata( $post );
	get_template_part( 'sections/blog/card' );
};
?>

<section class="bg-mrent-black pt-[40px] xl:pt-[80px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

			<?php /* ── Desktop (xl+): сетка 3-колоночная, без свайпера. ── */ ?>
			<div class="hidden xl:grid xl:grid-cols-3 gap-[30px] items-stretch">
				<?php foreach ( $mrent_post_ids as $mrent_id ) : ?>
					<?php $mrent_render_card( (int) $mrent_id ); ?>
				<?php endforeach; ?>
			</div>

			<?php /* ── Mobile (<xl): Swiper, по 4 карточки в слайде. ── */ ?>
			<div class="xl:hidden flex flex-col gap-[30px]">
				<?php /* w-full + min-w-0: фиксируем ширину Swiper'а внутри
				         flex-col-родителя (см. sections/services/grid.php). */ ?>
				<div class="mrent-blog-swiper swiper w-full min-w-0 overflow-hidden" data-mrent-blog-swiper>
					<div class="swiper-wrapper">
						<?php foreach ( $mrent_mobile_pages as $mrent_page_ids ) : ?>
							<div class="swiper-slide h-auto">
								<div class="flex flex-col gap-[20px]">
									<?php foreach ( $mrent_page_ids as $mrent_id ) : ?>
										<?php $mrent_render_card( (int) $mrent_id ); ?>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php /* ── Навигация: жёлтые prev/next + полоски-пагинация
				          (Figma 2341:3266). ── */ ?>
				<div class="flex items-center justify-between gap-[10px]">
					<button
						type="button"
						class="mrent-blog-prev bg-mrent-yellow hover:bg-[#FFF831] rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black transition-colors"
						aria-label="<?php esc_attr_e( 'Предыдущие записи', 'm-rent' ); ?>"
					>
						<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
							<polyline points="8 1 1 8 8 15"/>
						</svg>
					</button>
					<div class="mrent-blog-pagination flex flex-wrap items-center justify-center gap-[10px]"></div>
					<button
						type="button"
						class="mrent-blog-next bg-mrent-yellow hover:bg-[#FFF831] rounded-[15px] size-[55px] shrink-0 flex items-center justify-center text-mrent-black transition-colors"
						aria-label="<?php esc_attr_e( 'Следующие записи', 'm-rent' ); ?>"
					>
						<svg viewBox="0 0 9 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[8px] h-[15px]" aria-hidden="true">
							<polyline points="1 1 8 8 1 15"/>
						</svg>
					</button>
				</div>
			</div>

			<?php wp_reset_postdata(); ?>

			<?php /* ── WP-пагинация: переходы между страницами архива
			           (когда постов больше, чем `posts_per_page=12`). ── */ ?>
			<?php
			$mrent_pagination = paginate_links( [
				'mid_size'  => 1,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'array',
			] );
			?>
			<?php if ( ! empty( $mrent_pagination ) ) : ?>
				<nav class="flex items-center justify-center gap-[10px] flex-wrap" aria-label="<?php esc_attr_e( 'Пагинация', 'm-rent' ); ?>">
					<?php foreach ( $mrent_pagination as $mrent_link ) :
						$mrent_is_current = strpos( $mrent_link, 'current' ) !== false;
						$mrent_skin = $mrent_is_current
							? 'bg-mrent-yellow text-mrent-black'
							: 'bg-[#252426] text-mrent-white hover:bg-[#2f2e30]';
					?>
						<span class="<?php echo esc_attr( $mrent_skin ); ?> rounded-[12px] min-w-[45px] h-[45px] xl:min-w-[55px] xl:h-[55px] inline-flex items-center justify-center px-[10px] text-[16px] xl:text-[18px] font-[500] transition-colors">
							<?php echo wp_kses( $mrent_link, [
								'a'    => [ 'href' => true, 'class' => true ],
								'span' => [ 'class' => true, 'aria-current' => true ],
							] ); ?>
						</span>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

		</div>
	</div>
</section>
