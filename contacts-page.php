<?php

/**
 * Шаблон страницы контактов.
 *
 * Template name: Контакты
 *
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px]">
    <?php get_template_part('sections/common/breadcrumbs'); ?>
    <?php get_template_part('sections/contacts/contact-form'); ?>
    <?php get_template_part('sections/contacts/map'); ?>
</main>

<?php get_footer();
