<?php

/**
 * Template name: Аренда для юр. лиц
 * Template Post Type: service
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
?>

    <main class="bg-mrent-black">
        <?php get_template_part('sections/services/business-hero'); ?>
        <?php get_template_part('sections/services/breadcrumbs'); ?>
        <?php get_template_part('sections/services/business-included'); ?>
        <?php get_template_part('sections/services/business-benefits'); ?>
        <?php get_template_part('sections/services/business-conditions'); ?>
        <?php get_template_part('sections/services/business-faq'); ?>
        <?php get_template_part('sections/common/contact'); ?>
    </main>

<?php endwhile; ?>

<?php get_footer();
