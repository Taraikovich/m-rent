<?php

/**
 * Главная: секция-виджет «Услуги».
 *
 * Тянет услуги (CPT `service`) с включённым ACF-флагом `service_show_on_home`.
 * Если ни одной нет — секция не выводится. Заголовок и кнопка настраиваются
 * в админке шаблона «Главная» (поля `home_services_*`).
 *
 * Layout (Figma 199:109 desktop / 2322:3733 mobile):
 *   • Desktop: «заголовок 74px Bold слева ↔ жёлтая кнопка 300×65 справа»;
 *     ниже Swiper, по 2 карточки в видимой области, gap-20, h-600.
 *     Карточка 850×600, бордер #302F31, rounded-15, p-50.
 *     Поверх фото — корнерный градиент (top-left dark → bottom-right transparent).
 *     Заголовок 35px Heavy сверху-слева + ссылка «Подробнее» под ним.
 *   • Mobile: заголовок 28px Bold по центру; ниже Swiper по 1 карточке h-233;
 *     карточка 330×233, бордер, p-25; заголовок 20px, ссылка 18px;
 *     ниже полоски-пагинация (40×4) + жёлтая кнопка-CTA на всю ширину (h-55).
 *
 * Источник фото для карточки — service_hero_image → service_card_image →
 * featured image (тот же каскад, что в hero на single-странице).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title       = (string) get_field('home_services_title');
$mrent_button_text = get_field('home_services_button_text') ?: __('Все услуги', 'm-rent');
$mrent_button_url  = get_field('home_services_button_url');
if (! $mrent_button_url) {
	$mrent_button_url = get_post_type_archive_link('service') ?: home_url('/services/');
}

if ($mrent_title === '') {
	return;
}

$mrent_query = new WP_Query([
	'post_type'      => 'service',
	'posts_per_page' => -1,
	'meta_query'     => [
		[
			'key'   => 'service_show_on_home',
			'value' => '1',
		],
	],
	'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
	'no_found_rows'  => true,
]);

if (! $mrent_query->have_posts()) {
	wp_reset_postdata();
	return;
}
?>

<section class="bg-mrent-black py-[60px] xl:py-[clamp(40px,6vh,100px)] xl:h-[calc(100vh-100px)]">
	<div class="px-[15px] xl:px-[100px] xl:h-full">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[clamp(20px,4vh,60px)] items-center xl:h-full">

			<?php /* ── Заголовок (+ кнопка справа на десктопе) ── */ ?>
			<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[15px] w-full -mb-[15px] xl:mb-0">
				<h2 class="font-[700] text-[clamp(24px,calc(16.83px+2.98vw),50px)] leading-[1.2] text-mrent-white text-left">
					<?php echo esc_html($mrent_title); ?>
				</h2>
				<a
					href="<?php echo esc_url($mrent_button_url); ?>"
					class="hidden xl:flex bg-mrent-yellow hover:bg-[#FFF831] items-center justify-center rounded-[15px] h-[65px] w-[300px] px-[15px] text-mrent-black font-[500] text-[18px] whitespace-nowrap transition-colors">
					<?php echo esc_html($mrent_button_text); ?>
				</a>
			</div>

			<?php /* ── Swiper с карточками ── */ ?>
			<div class="mrent-home-services swiper w-full min-w-0 xl:flex-1 xl:min-h-0 xl:h-full" data-mrent-home-services>
				<div class="swiper-wrapper">
					<?php while ($mrent_query->have_posts()) :
						$mrent_query->the_post();

						// Каскад: hero → card → featured (как в single-hero.php).
						$mrent_image_url = '';
						foreach (['service_hero_image', 'service_card_image'] as $mrent_field) {
							$mrent_value = get_field($mrent_field);
							if (is_array($mrent_value) && ! empty($mrent_value['url'])) {
								$mrent_image_url = $mrent_value['url'];
								break;
							}
						}
						if ($mrent_image_url === '') {
							$mrent_image_url = get_the_post_thumbnail_url(get_post(), 'large') ?: '';
						}
					?>
						<div class="swiper-slide">
							<a
								href="<?php the_permalink(); ?>"
								class="group relative flex flex-col items-start aspect-[850/600] xl:aspect-auto xl:h-full w-full p-[25px] xl:p-[50px] rounded-[15px] border border-[#302f31] overflow-hidden bg-[#252426]">
								<?php if ($mrent_image_url) : ?>
									<img
										src="<?php echo esc_url($mrent_image_url); ?>"
										alt="<?php echo esc_attr(get_the_title()); ?>"
										class="absolute inset-0 size-full object-cover"
										loading="lazy"
										decoding="async">
								<?php endif; ?>

								<?php /* Корнерный градиент: тёмный в top-left, прозрачный в bottom-right.
								         Это даёт читаемый текст в углу с заголовком, не «глуша» всю картинку. */ ?>
								<span
									class="absolute inset-0 pointer-events-none rounded-[15px]"
									style="background: linear-gradient(135deg, rgba(30,29,31,0.85) 0%, rgba(30,29,31,0.6) 25%, rgba(30,29,31,0) 60%);"
									aria-hidden="true"></span>

								<div class="relative flex flex-col gap-[15px] xl:gap-[30px] items-start max-w-[55%]">
									<h3 class="text-mrent-white font-[800] text-[clamp(16px,calc(16.36px+0.97vw),24px)] leading-[1.2]">
										<?php the_title(); ?>
									</h3>
									<span class="text-mrent-white font-[500] text-[clamp(14px,calc(15.09px+0.78vw),18px)] leading-[1.2] underline whitespace-nowrap group-hover:text-mrent-yellow transition-colors">
										<?php esc_html_e('Подробнее', 'm-rent'); ?>
									</span>
								</div>
							</a>
						</div>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>
			</div>

			<?php /* ── Пагинация полосками. Видна на всех брейкпоинтах под свайпером. ── */ ?>
			<div class="mrent-home-services-pagination flex flex-wrap gap-[10px] xl:gap-[19px] justify-center"></div>

			<?php /* ── Mobile-only: жёлтая кнопка-CTA на всю ширину ── */ ?>
			<a
				href="<?php echo esc_url($mrent_button_url); ?>"
				class="xl:hidden bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] w-full px-[15px] text-mrent-black font-[500] text-[14px] whitespace-nowrap transition-colors">
				<?php echo esc_html($mrent_button_text); ?>
			</a>

		</div>
	</div>
</section>