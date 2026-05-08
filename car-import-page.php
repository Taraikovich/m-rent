<?php

/**
 * Шаблон страницы импорта bmw.
 *
 * Template name: Авто под заказ
 * Template Post Type: service
 *
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="bg-mrent-black pt-15 xl:pt-23.25">
    <?php get_template_part('sections/services/breadcrumbs'); ?>
    <?php get_template_part('sections/car-import/image'); ?>
    <?php get_template_part('sections/car-import/intro'); ?>
    <?php get_template_part('sections/car-import/tabs'); ?>
    <?php get_template_part('sections/car-import/why-benefits'); ?>
    <?php get_template_part('sections/car-import/benefits'); ?>
    <?php get_template_part('sections/car-import/consultation'); ?>
    <?php get_template_part('sections/car-import/seo-text'); ?>
</main>

<?php get_footer();
