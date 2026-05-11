<?php

/**
 * Каталог автопарка: /cars/
 *
 * Тонкий шаблон — заголовок секции, фильтры и сетка карточек выносятся в
 * sections/cars/*. Активная категория («Все автомобили») определяется внутри
 * filters.php по queried object.
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px]">
	<?php get_template_part('sections/cars/breadcrumbs'); ?>
	<?php get_template_part('sections/cars/filters'); ?>
	<?php get_template_part('sections/cars/grid'); ?>
	<?php get_template_part('sections/cars/services-tabbed'); ?>
	<?php get_template_part('sections/cars/archive-seo-text'); ?>
	<?php get_template_part('sections/common/contact'); ?>
</main>

<?php get_footer();
