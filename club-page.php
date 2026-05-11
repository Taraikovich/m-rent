<?php

/**
 * Шаблон страницы о клубных картах.
 *
 * Template name: Клубные карты
 *
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main>
    <?php get_template_part('sections/club/hero'); ?>
    <?php get_template_part('sections/club/breadcrumbs'); ?>
    <?php get_template_part('sections/club/cards'); ?>
    <?php get_template_part('sections/home/popular'); ?>
    <?php get_template_part('sections/club/why-us'); ?>
    <?php get_template_part('sections/club/services-tabbed'); ?>

    <?php get_template_part('sections/club/seo-text'); ?>
    <?php get_template_part('sections/common/contact'); ?>
</main>

<?php get_footer();
