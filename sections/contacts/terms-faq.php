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
	'<svg viewBox="0 0 70 70" fill="currentColor" class="size-full" aria-hidden="true">'
		. '<path d="M55.7566 6C57.6295 6 59.4263 6.74515 60.7504 8.07328C62.0744 9.40148 62.8181 11.204 62.8181 13.0828V40.4787C62.8181 40.9041 62.649 41.3117 62.349 41.6127C62.049 41.9136 61.6426 42.0833 61.2185 42.0833C60.7946 42.0833 60.3881 41.9136 60.0881 41.6127C59.7882 41.3118 59.6199 40.904 59.6199 40.4787V13.0828C59.6199 12.0548 59.2116 11.0696 58.4875 10.3432C57.7635 9.61695 56.7813 9.20825 55.7566 9.20825H12.0606C11.0358 9.2083 10.0537 9.61688 9.32961 10.3432C8.60553 11.0696 8.19824 12.0548 8.19819 13.0828V56.9162C8.19819 57.9442 8.60561 58.9294 9.32961 59.6558C10.0537 60.3821 11.0358 60.7917 12.0606 60.7917H35.001C35.4249 60.7917 35.8314 60.9606 36.1314 61.2614C36.4314 61.5623 36.6005 61.9701 36.6006 62.3954C36.6006 62.8208 36.4314 63.2285 36.1314 63.5294C35.8314 63.8303 35.425 64 35.001 64H12.0606C10.1877 64 8.39082 63.2539 7.06678 61.9257C5.74282 60.5975 5 58.795 5 56.9162V13.0828C5.00005 11.204 5.74274 9.40148 7.06678 8.07328C8.39082 6.74508 10.1877 6.00005 12.0606 6H55.7566ZM63.574 49.8442C63.9328 49.8827 64.2698 50.0435 64.5289 50.3011L64.644 50.4302C64.8728 50.7145 65 51.0691 65 51.437C65 51.8029 64.8741 52.1555 64.6479 52.4389L64.525 52.5769L53.6088 63.5274C53.3089 63.8278 52.9029 63.9971 52.4794 63.9971C52.1125 63.997 51.7591 63.8695 51.4757 63.6399L51.3343 63.5127L46.954 59.1177L46.9432 59.1079L46.8242 58.9856C46.7197 58.8655 46.633 58.7295 46.5677 58.5825C46.5019 58.4342 46.4589 58.2778 46.44 58.1187L46.4292 57.9582C46.4255 57.743 46.4643 57.5291 46.5443 57.3301C46.6245 57.131 46.745 56.9487 46.8974 56.7959C47.0497 56.643 47.2314 56.5221 47.4299 56.4417C47.6284 56.3614 47.8416 56.3225 48.0561 56.3262C48.2694 56.33 48.4813 56.3771 48.6784 56.4652C48.8701 56.5508 49.0429 56.6739 49.1866 56.8252L49.1983 56.837L49.2119 56.8526L52.4794 60.1303L62.2485 50.3295L62.3938 50.1964C62.6763 49.9656 63.0299 49.838 63.3965 49.8363L63.574 49.8442ZM35.001 47.6417C35.4249 47.6417 35.8314 47.8106 36.1314 48.1114C36.4314 48.4123 36.6005 48.82 36.6006 49.2454C36.6006 49.6708 36.4314 50.0784 36.1314 50.3794C35.8314 50.6803 35.425 50.85 35.001 50.85H19.7074C19.2834 50.8499 18.8769 50.6802 18.577 50.3794C18.2771 50.0784 18.1088 49.6707 18.1088 49.2454C18.1088 48.82 18.277 48.4123 18.577 48.1114C18.8769 47.8105 19.2834 47.6418 19.7074 47.6417H35.001ZM48.0971 33.3959L48.2707 33.4037C48.6354 33.4406 48.9782 33.6028 49.2402 33.8655C49.5401 34.1664 49.7093 34.5742 49.7093 34.9995C49.7093 35.4249 49.5402 35.8326 49.2402 36.1335C48.9402 36.4344 48.5338 36.6041 48.1098 36.6041H19.7074C19.2834 36.6041 18.8769 36.4344 18.577 36.1335C18.2771 35.8326 18.1088 35.4248 18.1088 34.9995C18.1088 34.5742 18.277 34.1664 18.577 33.8655C18.8769 33.5646 19.2834 33.3959 19.7074 33.3959H48.0971ZM48.0971 19.15L48.2707 19.1578C48.6354 19.1948 48.9782 19.3569 49.2402 19.6197C49.5401 19.9205 49.7093 20.3283 49.7093 20.7537C49.7093 21.1791 49.5402 21.5867 49.2402 21.8877C48.9805 22.1481 48.6415 22.3096 48.2804 22.3485L48.0766 22.3583H19.7074C19.2834 22.3582 18.8769 22.1885 18.577 21.8877C18.2771 21.5867 18.1088 21.179 18.1088 20.7537C18.1088 20.3283 18.277 19.9205 18.577 19.6197C18.8769 19.3188 19.2834 19.1501 19.7074 19.15H48.0971Z"/>'
		. '</svg>',
	// hugeicons:passport — удостоверение
	'<svg viewBox="0 0 70 70" fill="none" stroke="currentColor" stroke-width="3" class="size-full" aria-hidden="true">'
		. '<path d="M6 35C6 21.3281 6 14.4938 10.2456 10.2469C14.497 6 21.3294 6 35 6C48.6706 6 55.5059 6 59.7515 10.2469C63.9971 14.4938 64 21.3313 64 35C64 48.6687 64 55.5062 59.7515 59.7531C55.5088 64 48.6706 64 35 64C21.3294 64 14.4941 64 10.2456 59.7531C5.9971 55.5062 6 48.6687 6 35Z" stroke-linecap="round" stroke-linejoin="round"/>'
		. '<path d="M15 47C18.1063 40.1173 29.688 39.6667 33 47M29.1429 28.3333C29.1429 29.7478 28.601 31.1044 27.6365 32.1046C26.6721 33.1048 25.364 33.6667 24 33.6667C22.636 33.6667 21.3279 33.1048 20.3635 32.1046C19.399 31.1044 18.8571 29.7478 18.8571 28.3333C18.8571 26.9188 19.399 25.5623 20.3635 24.5621C21.3279 23.5619 22.636 23 24 23C25.364 23 26.6721 23.5619 27.6365 24.5621C28.601 25.5623 29.1429 26.9188 29.1429 28.3333Z" stroke-linecap="round"/>'
		. '<path d="M42 29H53M42 41H53" stroke-linecap="round" stroke-linejoin="round"/>'
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