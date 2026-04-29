<?php
/**
 * Каталог по категории: /cars/category/{slug}/
 *
 * Та же раскладка, что и /cars/, но заголовок — название текущего терма,
 * а активная пилюля в фильтрах — он же.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px]">
	<?php get_template_part( 'sections/cars/breadcrumbs' ); ?>
	<?php get_template_part( 'sections/cars/filters' ); ?>
	<?php get_template_part( 'sections/cars/grid' ); ?>
</main>

<?php get_footer();
