<?php

/**
 * Секция «Похожие услуги» на странице услуги
 * (Figma desktop 2120:1425, mobile 2535:6239).
 *
 * Выводит услуги из тех же категорий `service_category`, что и текущая,
 * исключая саму страницу. Свайпер: 1 карточка на мобайле, 2 на десктопе,
 * полосочная пагинация (mrent-bullet-bar). Кнопок prev/next нет.
 *
 * Карточка отличается от card.php только высотой (600 vs 450 на десктопе) —
 * по дизайну в single-контексте она выше. Падинги/типографика — те же.
 *
 * Скрывается, если в той же категории нет других услуг.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_related_term_ids = wp_get_post_terms(get_the_ID(), 'service_category', ['fields' => 'ids']);
if (is_wp_error($mrent_related_term_ids) || empty($mrent_related_term_ids)) {
	return;
}

$mrent_related = new WP_Query([
	'post_type'      => 'service',
	'posts_per_page' => -1,
	'post__not_in'   => [get_the_ID()],
	'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
	'tax_query'      => [
		[
			'taxonomy' => 'service_category',
			'field'    => 'term_id',
			'terms'    => $mrent_related_term_ids,
		],
	],
	'no_found_rows'  => true,
]);

if (! $mrent_related->have_posts()) {
	wp_reset_postdata();
	return;
}

$mrent_services_url = get_post_type_archive_link('service') ?: home_url('/services/');
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px] items-center">

		<?php /* ── Заголовок (+ ссылка-кнопка справа на десктопе) ── */ ?>
		<div class="w-full flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[15px]">
			<h2 class="font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] text-mrent-white text-center xl:text-left">
				<?php esc_html_e('Похожие услуги', 'm-rent'); ?>
			</h2>
			<a
				href="<?php echo esc_url($mrent_services_url); ?>"
				class="hidden xl:flex bg-mrent-yellow hover:bg-[#FFF831] items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] whitespace-nowrap transition-colors">
				<?php esc_html_e('Выбрать авто', 'm-rent'); ?>
			</a>
		</div>

		<?php /* ── Swiper: 1 карточка <xl, 2 ≥xl, gap 10/20. w-full min-w-0 —
		         см. комментарий в sections/services/grid.php о flex-item min-width. */ ?>
		<div class="mrent-related-services swiper w-full min-w-0 overflow-hidden" data-mrent-related-services>
			<div class="swiper-wrapper">
				<?php while ($mrent_related->have_posts()) :
					$mrent_related->the_post();

					$mrent_related_image_field = get_field('service_card_image');
					$mrent_related_image_url   = '';
					$mrent_related_image_alt   = get_the_title();
					if (is_array($mrent_related_image_field) && ! empty($mrent_related_image_field['url'])) {
						$mrent_related_image_url = $mrent_related_image_field['url'];
						if (! empty($mrent_related_image_field['alt'])) {
							$mrent_related_image_alt = $mrent_related_image_field['alt'];
						}
					} else {
						$mrent_related_image_url = get_the_post_thumbnail_url(get_post(), 'large') ?: '';
					}
				?>
					<div class="swiper-slide">
						<a
							href="<?php the_permalink(); ?>"
							class="group relative flex flex-col items-start h-[233px] xl:h-auto xl:aspect-[17/12] w-full p-[25px] xl:p-[50px] rounded-[15px] border border-[#302f31] overflow-hidden bg-[#252426]">
							<?php if ($mrent_related_image_url) : ?>
								<img
									src="<?php echo esc_url($mrent_related_image_url); ?>"
									alt="<?php echo esc_attr($mrent_related_image_alt); ?>"
									class="absolute inset-0 size-full object-cover"
									loading="lazy"
									decoding="async">
							<?php endif; ?>
							<span class="absolute inset-0 bg-mrent-black/80 transition-colors group-hover:bg-mrent-black/70" aria-hidden="true"></span>

							<div class="relative flex flex-col gap-[15px] xl:gap-[30px] items-start max-w-[85%]">
								<h3 class="text-mrent-white font-[800] text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2]">
									<?php the_title(); ?>
								</h3>
								<span class="text-mrent-white font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.2] underline whitespace-nowrap group-hover:text-mrent-yellow transition-colors">
									<?php esc_html_e('Подробнее', 'm-rent'); ?>
								</span>
							</div>
						</a>
					</div>
				<?php endwhile;
				wp_reset_postdata(); ?>
			</div>
		</div>

		<?php /* ── Пагинация полосками (общая для mobile и desktop, как в макете). */ ?>
		<div class="mrent-related-services-pagination flex flex-wrap items-center justify-center gap-[10px] xl:gap-[19px]"></div>

		<?php /* ── Mobile-only: жёлтая CTA «Все услуги» на всю ширину. */ ?>
		<a
			href="<?php echo esc_url($mrent_services_url); ?>"
			class="xl:hidden bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] w-full px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] whitespace-nowrap transition-colors">
			<?php esc_html_e('Все услуги', 'm-rent'); ?>
		</a>

	</div>
</section>