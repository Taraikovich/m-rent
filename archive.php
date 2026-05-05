<?php
/**
 * Архив записей блога: категории, метки, авторы, даты, поиск.
 *
 * Тонкий шаблон — заголовок, хлебные крошки и сетка выносятся в sections/blog/*.
 * Для главной страницы записей (когда выбрана `page_for_posts`) используется
 * `home.php`, который дублирует эту же раскладку.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[60px] xl:pb-[100px]">
	<?php get_template_part( 'sections/blog/breadcrumbs' ); ?>
	<?php get_template_part( 'sections/blog/header' ); ?>
	<?php get_template_part( 'sections/blog/grid' ); ?>
</main>

<?php get_footer();
