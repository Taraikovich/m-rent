<?php

/**
 * Секция «Условия аренды» — FAQ-аккордеон + 2 info-карточки.
 * (Figma desktop 2061:1300; mobile 2324:3286.)
 *
 * Раскладка:
 *   Десктоп (1920px):
 *     • Заголовок 74px.
 *     • Через 52px — сетка карточек (2 шт. в одном ряду 850×244 + потом ряды вопросов на всю ширину).
 *     • Info-карточки: фон #252426, p-40, rounded-15, иконка в правом верхнем углу 70×70.
 *     • Вопросы: тот же фон + border #302F31, p-40, заголовок 35px, жёлтый chevron 36×36.
 *
 *   Мобайл (360px):
 *     • Заголовок 28px.
 *     • Через 30px — список в одну колонку, gap 10. Info-карточки сверху, потом вопросы.
 *
 * Аккордеон собран на нативном <details>/<summary> (как в sections/services/single-faq.php).
 *
 * ACF (group_mrent_terms):
 *   • terms_faq_title  — Text; default «Условия аренды».
 *   • terms_info_cards — Repeater (max 2):
 *       title   (Text, optional)
 *       content (Wysiwyg, basic toolbar — позволяет <ul>/<strong>)
 *     Иконки в правом верхнем углу прибиты к индексу (0 — требования, 1 — паспорт).
 *   • terms_faq_items  — Repeater: question (Text) + answer (Textarea).
 *     Если пусто — секция всё равно показывает info-карточки; если и они пусты — секция скрыта.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_terms_title = (string) get_field('terms_faq_title');
if ($mrent_terms_title === '') {
	$mrent_terms_title = __('Условия аренды', 'm-rent');
}

$mrent_info_cards = (array) get_field('terms_info_cards');
$mrent_faq_items  = (array) get_field('terms_faq_items');

if (! $mrent_info_cards && ! $mrent_faq_items) {
	return;
}

// Иконки-«украшения» в правом верхнем углу info-карточек.
// Inline SVG: Figma-ассеты живут 7 дней, поэтому не подходят как зависимость.
$mrent_info_icons = [
	// pajamas:requirements — лист с галочкой
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-full" aria-hidden="true">'
		. '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>'
		. '<path d="M14 3v6h6"/>'
		. '<path d="M8.5 14.5l2 2 4-4"/>'
		. '</svg>',
	// hugeicons:passport — удостоверение
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-full" aria-hidden="true">'
		. '<rect x="4" y="3" width="16" height="18" rx="2"/>'
		. '<circle cx="12" cy="10" r="2.5"/>'
		. '<path d="M8.5 16h7"/>'
		. '<path d="M9.5 18.5h5"/>'
		. '</svg>',
];
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[52px] text-mrent-white">

		<h2 class="font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] w-full">
			<?php echo esc_html($mrent_terms_title); ?>
		</h2>

		<div class="flex flex-col gap-[10px] xl:gap-[20px] w-full">

			<?php if ($mrent_info_cards) : ?>
				<div class="flex flex-col gap-[10px] xl:flex-row xl:gap-[20px]">
					<?php foreach ($mrent_info_cards as $mrent_idx => $mrent_card) :
						$mrent_card_title   = (string) ($mrent_card['title'] ?? '');
						$mrent_card_content = (string) ($mrent_card['content'] ?? '');
						if ($mrent_card_title === '' && trim(wp_strip_all_tags($mrent_card_content)) === '') {
							continue;
						}
						$mrent_icon_svg = $mrent_info_icons[$mrent_idx] ?? '';
					?>
						<div class="relative bg-[#252426] rounded-[15px] p-[20px] xl:p-[40px] flex-1 flex flex-col gap-[15px] xl:gap-[30px] xl:min-h-[244px] xl:justify-center">
							<?php if ($mrent_icon_svg !== '') : ?>
								<span class="absolute top-[20px] right-[20px] xl:top-[40px] xl:right-[40px] size-[30px] xl:size-[70px] text-mrent-white">
									<?php echo $mrent_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG 
									?>
								</span>
							<?php endif; ?>

							<?php if ($mrent_card_title !== '') : ?>
								<p class="font-[600] text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2] pr-[40px] xl:pr-[90px]">
									<?php echo esc_html($mrent_card_title); ?>
								</p>
							<?php endif; ?>

							<?php if ($mrent_card_content !== '') : ?>
								<div class="mrent-terms-info text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.2] font-[400] pr-[40px] xl:pr-[90px]">
									<?php echo wp_kses_post($mrent_card_content); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php foreach ($mrent_faq_items as $mrent_faq_item) :
				$mrent_q = (string) ($mrent_faq_item['question'] ?? '');
				$mrent_a = (string) ($mrent_faq_item['answer'] ?? '');
				if ($mrent_q === '') {
					continue;
				}
			?>
				<details class="mrent-faq-item group bg-[#252426] border border-[#302F31] rounded-[15px] p-[20px] xl:p-[40px]">
					<summary class="flex items-center justify-between gap-[20px] cursor-pointer list-none [&::-webkit-details-marker]:hidden">
						<span class="font-[600] text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2] text-mrent-white">
							<?php echo esc_html($mrent_q); ?>
						</span>
						<span class="shrink-0 flex items-center justify-center bg-mrent-yellow rounded-[6px] xl:rounded-[8px] size-[25px] xl:size-[36px] text-mrent-black transition-transform group-open:rotate-180" aria-hidden="true">
							<svg viewBox="0 0 16 9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[10px] h-[6px] xl:w-[14px] xl:h-[8px]">
								<polyline points="1 1 8 8 15 1" />
							</svg>
						</span>
					</summary>

					<?php if ($mrent_a !== '') : ?>
						<div class="mt-[15px] xl:mt-[25px] font-[400] text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.5] text-mrent-white/90 whitespace-pre-line">
							<?php echo wp_kses_post(wpautop($mrent_a)); ?>
						</div>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>

		</div>
	</div>
</section>