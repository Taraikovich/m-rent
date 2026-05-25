<?php

/**
 * Заголовок архива блога (Figma 2147:1867 / 2341:3142).
 *
 *   Десктоп: title 74 (Bold), subtitle 30 (Book), gap-30, выравнивание по левому краю.
 *   Мобайл:  title 28, subtitle 16, gap-30.
 *
 * Источник текстов:
 *   • Заголовок таксономии/архива → стандартные WP-функции (`single_cat_title`,
 *     `single_tag_title`, и т.д.). На главном архиве блога — настраиваемые
 *     дефолты из дизайна.
 *   • Подзаголовок — описание термина, либо дефолт из дизайна (только на
 *     главном архиве блога / home).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_default_title    = __('Полезные статьи об аренде BMW и MINI', 'm-rent');
$mrent_default_subtitle = __('Cоветы по выбору авто, идеи для мероприятий, особенности проката и ответы на популярные вопросы', 'm-rent');

$mrent_title    = $mrent_default_title;
$mrent_subtitle = $mrent_default_subtitle;

if (is_category() || is_tag() || is_tax()) {
	$mrent_title       = single_term_title('', false);
	$mrent_term_desc   = term_description();
	$mrent_subtitle    = $mrent_term_desc !== '' ? wp_strip_all_tags($mrent_term_desc) : $mrent_default_subtitle;
} elseif (is_author()) {
	$mrent_title    = get_the_author();
	$mrent_subtitle = $mrent_default_subtitle;
} elseif (is_year() || is_month() || is_day()) {
	$mrent_title    = get_the_archive_title();
	$mrent_subtitle = $mrent_default_subtitle;
} elseif (is_search()) {
	/* translators: %s — поисковый запрос */
	$mrent_title    = sprintf(__('Результаты поиска: %s', 'm-rent'), get_search_query());
	$mrent_subtitle = '';
}
?>

<section class="bg-mrent-black pt-[20px] xl:pt-[40px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[15px] xl:gap-[30px] text-mrent-white">
			<h1 class="font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2]">
				<?php echo esc_html($mrent_title); ?>
			</h1>
			<?php if ($mrent_subtitle !== '') : ?>
				<p class="font-[400] text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.24]">
					<?php echo esc_html($mrent_subtitle); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>