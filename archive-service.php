<?php
/**
 * Архив услуг: /services/
 *
 * По дизайну (Figma 5095:7367 / 5095:7370) — одностраничный каталог: пилюли
 * категорий сверху + секции по каждой категории. Активная категория задаётся
 * через GET-параметр `cat` (slug терма) или равна `all`/пусто (показать всё).
 *
 * Внутри секций сетки рендерятся через цикл `WP_Query` по `service_category`,
 * не через main query. Главный цикл здесь не используем — он нужен только для
 * роутинга и заголовка/SEO.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[60px] xl:pb-[100px]">
	<?php get_template_part( 'sections/services/breadcrumbs' ); ?>
	<?php get_template_part( 'sections/services/filters' ); ?>
	<?php get_template_part( 'sections/services/grid' ); ?>
</main>

<?php get_footer();
