<?php

/**
 * Шаблон страницы условий аренды.
 *
 * Template name: Условия аренды
 *
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[80px]">
    <?php get_template_part('sections/common/breadcrumbs'); ?>
    <?php get_template_part('sections/contacts/instructions'); ?>
    <?php get_template_part('sections/contacts/terms-faq'); ?>
</main>

<?php get_footer();
