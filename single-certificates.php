<?php

/**
 * Шаблон страницы "Подарочные сертификаты".
 *
 * Template name: Подарочные сертификаты
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
    <?php get_template_part('sections/certificates/hero'); ?>
    <?php get_template_part('sections/certificates/formats'); ?>
    <?php get_template_part('sections/certificates/audience'); ?>
    <?php get_template_part('sections/certificates/conditions'); ?>
    <?php get_template_part('sections/certificates/benefits'); ?>
    <?php get_template_part('sections/certificates/seo-text'); ?>
    <?php get_template_part('sections/common/contact'); ?>
</main>

<?php get_footer();
