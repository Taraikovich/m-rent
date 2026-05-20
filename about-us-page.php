<?php

/**
 * Шаблон страницы о компании.
 *
 * Template name: О нас
 *
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main>
    <?php get_template_part('sections/about/hero'); ?>
    <?php get_template_part('sections/common/breadcrumbs'); ?>
    <?php get_template_part('sections/about/team'); ?>
    <?php get_template_part('sections/about/philosophy'); ?>
    <?php get_template_part('sections/about/benefits'); ?>
    <?php get_template_part('sections/about/services-tabbed'); ?>
    <?php get_template_part('sections/about/seo-text'); ?>
    <?php get_template_part('sections/common/contact'); ?>
</main>

<?php get_footer();
