<?php

/**
 * Страница модели автомобиля: /cars/{slug}/
 *
 * Тонкий шаблон: подключает секции из sections/cars/* в нужном порядке.
 *   1. Хлебные крошки
 *   2. Карусель фото (single-gallery.php) — full-width
 *   3. Заголовок + описание + прайс + депозит + CTA (single-details.php)
 *   4. Произвольный контент поста (если заполнен)
 *
 * Логика и ACF-поля живут внутри секций — здесь только композиция.
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

while (have_posts()) :
	the_post();
?>

	<main class="bg-mrent-black pt-[60px] xl:pt-[93px]">
		<?php get_template_part('sections/cars/breadcrumbs'); ?>
		<?php get_template_part('sections/cars/single-gallery'); ?>
		<?php get_template_part('sections/cars/single-details'); ?>
		<?php get_template_part('sections/cars/single-tabs'); ?>
		<?php get_template_part('sections/cars/single-booking'); ?>
		<?php get_template_part('sections/cars/services-tabbed'); ?>
		<?php get_template_part('sections/cars/why-us'); ?>
		<?php get_template_part('sections/home/popular'); ?>
		<?php get_template_part('sections/cars/single-seo-text'); ?>

		<?php if (get_the_content()) : ?>
			<div class="px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
				<article class="prose prose-invert max-w-[1720px] mx-auto text-mrent-white text-[16px] xl:text-[18px] leading-[1.5]">
					<?php the_content(); ?>
				</article>
			</div>
		<?php endif; ?>
	</main>

<?php endwhile; ?>

<?php get_footer();
