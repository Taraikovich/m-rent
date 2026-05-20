<?php

/**
 * Общий SEO-текстовый блок (Footer text block, Figma 2760:7230 / 2324:2851).
 *
 * Используется на нескольких страницах — поля у каждой свои. Тонкие обёртки
 * в `sections/{page}/seo-text.php` читают ACF и передают сюда через args:
 *
 *     get_template_part( 'sections/common/seo-text', null, [
 *         'title'      => 'Заголовок',
 *         'lead'       => '<p>HTML или plain-text</p>',  // прогоняется через the_content
 *         'more_label' => 'Подробнее',
 *         'less_label' => 'Свернуть',
 *     ] );
 *
 * Если `title` пустой — секция не выводится.
 *
 * Один DOM, адаптив через `xl:`-варианты:
 *   • < xl  — flex-col gap-40 (gap-15 внутри текст-блока):
 *              заголовок 28px Bold (white) + лид 16px Book → кнопка 18px Medium yellow.
 *   • ≥ xl  — flex-col gap-40 (gap-30 внутри текст-блока):
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

$mrent_title = isset($args['title']) ? (string) $args['title'] : '';

if ($mrent_title === '') {
	return;
}

$mrent_lead       = isset($args['lead']) ? (string) $args['lead'] : '';
$mrent_more_label = ! empty($args['more_label']) ? (string) $args['more_label'] : __('Подробнее', 'm-rent');
$mrent_less_label = ! empty($args['less_label']) ? (string) $args['less_label'] : __('Свернуть', 'm-rent');
?>

<section class="bg-mrent-black px-3.75 py-15 xl:px-25 xl:py-25">
	<div class="group max-w-430 mx-auto flex flex-col gap-10" data-seo-block data-expanded="false">

		<div class="flex flex-col gap-3.75 xl:gap-7.5 leading-[1.2]">
			<h2 class="font-display font-bold text-mrent-white text-[clamp(24px,calc(16.83px+2.98vw),50px)]">
				<?php echo esc_html($mrent_title); ?>
			</h2>

			<?php if ($mrent_lead) : ?>
				<div class="relative">
					<div data-seo-content class="overflow-hidden max-h-[150px] xl:max-h-[72px] transition-[max-height] duration-500 ease-out">
						<div class="mrent-seo-prose font-display font-normal text-mrent-white text-[clamp(14px,calc(12.6px+0.91vw),20px)]">
							<?php echo apply_filters('the_content', $mrent_lead); ?>
						</div>
					</div>
					<div
						data-seo-fade
						class="pointer-events-none absolute inset-x-0 bottom-0 h-15 xl:h-25 bg-linear-to-b from-transparent to-mrent-black opacity-100 transition-opacity duration-300 group-data-[expanded=true]:opacity-0"
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
				class="self-start font-display font-medium text-mrent-yellow text-[clamp(14px,calc(15.09px+0.78vw),18px)] underline underline-offset-2 hover:opacity-80 transition-opacity cursor-pointer">
				<?php echo esc_html($mrent_more_label); ?>
			</button>
		<?php endif; ?>

	</div>
</section>