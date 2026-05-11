<?php
/**
 * Cars: секция «Услуги по категориям» под сеткой архива — тонкая обёртка над
 * `sections/common/services-tabbed-block`.
 *
 * Читает ACF из Options-подстраницы `mrent-cars-archive`:
 *   • cars_archive_services_tabs_title       — заголовок
 *   • cars_archive_services_tabs_button_text — лейбл CTA
 *   • cars_archive_services_tabs_button_url  — ссылка CTA
 *   • cars_archive_services_tabs_terms       — выбранные термы `service_category`
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_field' ) ) {
	return;
}

?>
<div class="mt-[40px] xl:mt-[100px]">
<?php
get_template_part(
	'sections/common/services-tabbed-block',
	null,
	[
		'title'       => (string) get_field( 'cars_archive_services_tabs_title', 'option' ),
		'button_text' => (string) get_field( 'cars_archive_services_tabs_button_text', 'option' ),
		'button_url'  => (string) get_field( 'cars_archive_services_tabs_button_url', 'option' ),
		'term_ids'    => get_field( 'cars_archive_services_tabs_terms', 'option' ),
	]
);
?>
</div>
<?php
