<?php

/**
 * Шаблон главной страницы темы m-rent.
 *
 * Template name: Home Page
 *
 * Все секции вынесены в sections/home/*.php и подключаются через get_template_part.
 * Сюда добавляем только новые секции в нужном порядке.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main>
	<?php get_template_part( 'sections/home/hero' ); ?>
</main>

<?php get_footer();
