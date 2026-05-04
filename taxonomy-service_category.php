<?php
/**
 * Архив одной категории услуг: /services/category/{slug}/
 *
 * Та же раскладка, что и /services/, но виден только один раздел (через
 * GET-параметр `cat` — синхронизируем со slug текущего терма). Это даёт
 * единый шаблон сетки с архивом без дублирования логики.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Имитируем GET-параметр cat для grid.php — он рендерит одну секцию по slug.
if ( ! isset( $_GET['cat'] ) ) {
	$mrent_term = get_queried_object();
	if ( $mrent_term instanceof WP_Term ) {
		$_GET['cat'] = $mrent_term->slug;
	}
}

get_header();
?>

<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[60px] xl:pb-[100px]">
	<?php get_template_part( 'sections/services/breadcrumbs' ); ?>
	<?php get_template_part( 'sections/services/filters' ); ?>
	<?php get_template_part( 'sections/services/grid' ); ?>
</main>

<?php get_footer();
