<?php

/**
 * Шаблон страницы отзывов.
 *
 * Template name: Отзывы
 *
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[80px]">
    <?php get_template_part('sections/reviews/breadcrumbs'); ?>
    <?php get_template_part('sections/reviews/list'); ?>
    <?php get_template_part('sections/common/contact'); ?>
    <?php get_template_part('sections/common/review-modal'); ?>
</main>

<?php get_footer();
