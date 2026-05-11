<?php

/**
 * Страница услуги: /services/{slug}/
 *
 * Минимальный шаблон по макету (Figma 2083:1816 / 2535:5988):
 *   1. Hero: фоновая картинка с градиентом → #1E1D1F, заголовок, подзаголовок,
 *      жёлтая CTA-кнопка.
 *   2. Хлебные крошки (Figma 2142:1621): Главная > Услуги > {название}.
 *   3. WP-контент (опционально, если редактор заполнил `the_content`).
 *
 * Дополнительные ACF-блоки добавим итеративно по мере появления макета —
 * сейчас по дизайну виден только hero.
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

while (have_posts()) :
	the_post();
?>

	<main class="bg-mrent-black">
		<?php get_template_part('sections/services/single-hero'); ?>
		<?php get_template_part('sections/services/breadcrumbs'); ?>
		<?php get_template_part('sections/services/single-feature'); ?>
		<?php get_template_part('sections/services/single-packages'); ?>
		<?php get_template_part('sections/home/popular'); ?>
		<?php get_template_part('sections/services/single-benefits'); ?>
		<?php get_template_part('sections/services/single-booking'); ?>
		<?php get_template_part('sections/services/single-related'); ?>
		<?php get_template_part('sections/services/single-faq'); ?>
		<?php get_template_part('sections/common/contact'); ?>
		<?php get_template_part('sections/services/single-seo-text'); ?>

		<?php if (get_the_content()) : ?>
			<div class="px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px] pb-[60px] xl:pb-[100px]">
				<article class="prose prose-invert max-w-[1720px] mx-auto text-mrent-white text-[16px] xl:text-[18px] leading-[1.5]">
					<?php the_content(); ?>
				</article>
			</div>
		<?php endif; ?>
	</main>

<?php endwhile; ?>

<?php get_footer();
