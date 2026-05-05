<?php
/**
 * Секция «Полезные статьи» (Figma 355:677 / 2324:2806).
 *
 * Выводит N последних записей блога:
 *   • Десктоп (xl+) — статичная сетка из N карточек в ряд.
 *   • Мобайл (<xl)  — Swiper, по 1 карточке в видимой области, с пагинацией
 *     полосками (mrent-bullet-bar). Без prev/next кнопок (по дизайну).
 *
 * Использует общий swiper-инициализатор `[data-mrent-blog-swiper]` из
 * src/blog.js: тот же код, без navigation, потому что в DOM нет
 * `.mrent-blog-prev/next`.
 *
 * Параметры (через $args в get_template_part):
 *   • count       (int)    — сколько записей (по умолчанию 3, ограничено 1..6)
 *   • title       (string) — заголовок секции
 *   • subtitle    (string) — подзаголовок
 *   • button_text (string) — текст CTA-кнопки
 *   • button_url  (string) — URL CTA (по умолчанию — страница блога)
 *
 * Любой параметр можно опустить — будет дефолт по дизайну. Если записей нет,
 * секция не рендерится.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_args = is_array( $args ?? null ) ? $args : [];

$mrent_count = max( 1, min( 6, (int) ( $mrent_args['count'] ?? 3 ) ) );

$mrent_title       = $mrent_args['title']       ?? __( 'Полезные статьи об аренде авто', 'm-rent' );
$mrent_subtitle    = $mrent_args['subtitle']    ?? __( 'Cоветы по выбору авто, идеи для мероприятий, особенности проката и ответы на популярные вопросы', 'm-rent' );
$mrent_button_text = $mrent_args['button_text'] ?? __( 'Все статьи', 'm-rent' );

$mrent_button_url = $mrent_args['button_url'] ?? '';
if ( $mrent_button_url === '' ) {
	$mrent_posts_page_id = (int) get_option( 'page_for_posts' );
	if ( $mrent_posts_page_id > 0 ) {
		$mrent_button_url = get_permalink( $mrent_posts_page_id );
	}
	if ( ! $mrent_button_url ) {
		$mrent_button_url = home_url( '/blog/' );
	}
}

$mrent_query = new WP_Query( [
	'post_type'      => 'post',
	'posts_per_page' => $mrent_count,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
	'ignore_sticky_posts' => true,
] );

if ( ! $mrent_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

// Соответствует tailwind-набору grid-cols-{N} — нужно литералом для сканера.
$mrent_cols_class = match ( $mrent_count ) {
	1, 2 => 'xl:grid-cols-2',
	3    => 'xl:grid-cols-3',
	4    => 'xl:grid-cols-4',
	5, 6 => 'xl:grid-cols-3', // 5 и 6 — тоже 3 в ряд (2 ряда)
	default => 'xl:grid-cols-3',
};
?>

<section class="bg-mrent-black py-[60px] xl:py-[100px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[50px] items-center">

			<?php /* ── Заголовок + подзаголовок (центрированный) ── */ ?>
			<div class="flex flex-col gap-[15px] xl:gap-[30px] items-center text-center text-mrent-white">
				<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2] xl:whitespace-nowrap">
					<?php echo esc_html( $mrent_title ); ?>
				</h2>
				<?php if ( $mrent_subtitle !== '' ) : ?>
					<p class="font-[400] text-[16px] xl:text-[30px] leading-[1.24] max-w-[306px] xl:max-w-[675px]">
						<?php echo esc_html( $mrent_subtitle ); ?>
					</p>
				<?php endif; ?>
			</div>

			<?php /* ── Desktop (xl+): статичная сетка карточек. ── */ ?>
			<div class="hidden xl:grid <?php echo esc_attr( $mrent_cols_class ); ?> gap-[30px] items-stretch w-full">
				<?php while ( $mrent_query->have_posts() ) : $mrent_query->the_post(); ?>
					<?php get_template_part( 'sections/blog/card' ); ?>
				<?php endwhile; ?>
			</div>

			<?php /* ── Mobile (<xl): Swiper по 1 карточке в слайде + bullet-bars.
			           prev/next отсутствуют — JS-инициализатор это поддерживает
			           (см. src/blog.js: `prev && next ? {…} : false`). ── */ ?>
			<div class="xl:hidden w-full flex flex-col gap-[30px]">
				<div class="mrent-blog-swiper swiper w-full min-w-0 overflow-hidden" data-mrent-blog-swiper>
					<div class="swiper-wrapper">
						<?php
						$mrent_query->rewind_posts();
						while ( $mrent_query->have_posts() ) : $mrent_query->the_post(); ?>
							<div class="swiper-slide h-auto">
								<?php get_template_part( 'sections/blog/card' ); ?>
							</div>
						<?php endwhile; ?>
					</div>
				</div>
				<div class="mrent-blog-pagination flex flex-wrap items-center justify-center gap-[10px]"></div>
			</div>

			<?php wp_reset_postdata(); ?>

			<?php /* ── CTA «Все статьи». Мобайл: full-width × 55, текст 14.
			           Десктоп: w-300 × 65, текст 20. ── */ ?>
			<a
				href="<?php echo esc_url( $mrent_button_url ); ?>"
				class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full xl:w-[300px] px-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] whitespace-nowrap transition-colors"
			>
				<?php echo esc_html( $mrent_button_text ); ?>
			</a>

		</div>
	</div>
</section>
