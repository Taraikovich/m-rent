<?php

/**
 * Шаблон страницы FAQ.
 *
 * Template name: FAQ
 *
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[80px]">
    <?php get_template_part('sections/common/breadcrumbs'); ?>
    <?php get_template_part('sections/faq/list'); ?>
    <?php get_template_part('sections/common/contact'); ?>
</main>

<?php get_footer();
