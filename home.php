<?php
/**
 * Главная страница блога — лента всех записей.
 *
 * Используется WordPress'ом, когда в Settings → Reading выбрана
 * «Страница записей». Дублирует раскладку archive.php.
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
