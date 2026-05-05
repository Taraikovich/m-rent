<?php

/**
 * Общий SEO-текстовый блок (Footer text block, Figma 2760:7230 / 2324:2851).
 *
 * Используется на нескольких страницах — лежит в `sections/common/`. Сейчас
 * подключён только на главной (home-page.php), но шаблон не привязан к ней:
 * берёт значения через `get_queried_object_id()` + ACF, поэтому может работать
 * на любой странице, где определены поля `home_seo_*` (или их аналоги после
 * расширения ACF-группы / location'а).
 *
 * Контент: ACF-группа «Главная», таб «SEO-текст». Если заголовок пустой — секция
 * не выводится.
 *
 * Один DOM, адаптив через `2xl:`-варианты:
 *   • < 2xl  — flex-col gap-40 (gap-15 внутри текст-блока):
 *              заголовок 28px Bold (white) + лид 16px Book → кнопка 18px Medium yellow.
 *   • ≥ 2xl  — flex-col gap-40 (gap-30 внутри текст-блока):
 *              заголовок 74px, лид 30px → кнопка 30px.
 *
 * Кнопка «Подробнее» — НЕ ссылка, а toggle: плавно разворачивает полный текст.
 *   • Свёрнуто: контейнер `[data-seo-content]` ограничен `max-h-[150px]/[260px]`,
 *     overflow-hidden + поверх лежит градиентный оверлей (`from-transparent → to-mrent-black`),
 *     создаёт эффект «text fades to bottom» как в Figma (`from-[82.339%]`).
 *   • Раскрыто: JS выставляет `max-height = scrollHeight` (плавная transition),
 *     оверлей через `group-data-[expanded=true]:opacity-0` уезжает в 0.
 *   • Логика — `src/seo-text.js`. Если текст помещается без обрезки —
 *     кнопка и оверлей скрываются автоматически.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field('home_seo_title', $mrent_page_id);

if ($mrent_title === '') {
	return;
}

$mrent_lead        = (string) get_field('home_seo_lead', $mrent_page_id);
$mrent_more_label  = get_field('home_seo_more_label', $mrent_page_id) ?: __('Подробнее', 'm-rent');
$mrent_less_label  = get_field('home_seo_less_label', $mrent_page_id) ?: __('Свернуть', 'm-rent');
?>

<section class="bg-mrent-black px-3.75 py-15 2xl:px-25 2xl:py-25">
	<div class="group max-w-430 mx-auto flex flex-col gap-10" data-seo-block data-expanded="false">

		<div class="flex flex-col gap-3.75 2xl:gap-7.5 leading-[1.2]">
			<h2 class="font-display font-bold text-mrent-white text-[28px] 2xl:text-[74px]">
				<?php echo esc_html($mrent_title); ?>
			</h2>

			<?php if ($mrent_lead) : ?>
				<div class="relative">
					<div data-seo-content class="overflow-hidden max-h-[150px] 2xl:max-h-[260px] transition-[max-height] duration-500 ease-out">
						<div class="mrent-seo-prose font-display font-normal text-mrent-white text-base 2xl:text-3xl">
							<?php echo apply_filters('the_content', $mrent_lead); ?>
						</div>
					</div>
					<div
						data-seo-fade
						class="pointer-events-none absolute inset-x-0 bottom-0 h-15 2xl:h-25 bg-linear-to-b from-transparent to-mrent-black opacity-100 transition-opacity duration-300 group-data-[expanded=true]:opacity-0"
						aria-hidden="true"></div>
				</div>
			<?php endif; ?>
		</div>

		<?php if ($mrent_lead) : ?>
			<button
				type="button"
				data-seo-toggle
				data-label-more="<?php echo esc_attr($mrent_more_label); ?>"
				data-label-less="<?php echo esc_attr($mrent_less_label); ?>"
				aria-expanded="false"
				class="self-start font-display font-medium text-mrent-yellow text-lg 2xl:text-3xl underline underline-offset-2 hover:opacity-80 transition-opacity cursor-pointer">
				<?php echo esc_html($mrent_more_label); ?>
			</button>
		<?php endif; ?>

	</div>
</section>
