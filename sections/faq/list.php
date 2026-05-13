<?php

/**
 * Секция «Часто задаваемые вопросы» — FAQ-аккордеон страницы FAQ.
 * Figma desktop 2187:3309 / mobile 2345:4729.
 *
 * ACF (group_mrent_faq):
 *   • faq_title — Text; default «Часто задаваемые вопросы».
 *   • faq_items — Repeater: question (Text) + answer (Textarea).
 *
 * Если вопросов нет — секция скрыта.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_faq_title = (string) get_field('faq_title');
if ($mrent_faq_title === '') {
	$mrent_faq_title = __('Часто задаваемые вопросы', 'm-rent');
}

$mrent_faq_items = (array) get_field('faq_items');
if (! $mrent_faq_items) {
	return;
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[52px] text-mrent-white">

		<h1 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2] w-full">
			<?php echo esc_html($mrent_faq_title); ?>
		</h1>

		<div class="flex flex-col gap-[10px] xl:gap-[20px] w-full">
			<?php foreach ($mrent_faq_items as $mrent_faq_item) :
				$mrent_q = (string) ($mrent_faq_item['question'] ?? '');
				$mrent_a = (string) ($mrent_faq_item['answer'] ?? '');
				if ($mrent_q === '') {
					continue;
				}
			?>
				<details class="mrent-faq-item group bg-[#252426] border border-[#302F31] rounded-[15px] p-[20px] xl:p-[40px]">
					<summary class="flex items-center justify-between gap-[20px] cursor-pointer list-none [&::-webkit-details-marker]:hidden">
						<span class="font-[600] text-[18px] xl:text-[35px] leading-[1.2] text-mrent-white">
							<?php echo esc_html($mrent_q); ?>
						</span>
						<span class="shrink-0 flex items-center justify-center bg-mrent-yellow rounded-[6px] xl:rounded-[8px] size-[25px] xl:size-[36px] text-mrent-black transition-transform group-open:rotate-180" aria-hidden="true">
							<svg viewBox="0 0 16 9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[10px] h-[6px] xl:w-[14px] xl:h-[8px]">
								<polyline points="1 1 8 8 15 1" />
							</svg>
						</span>
					</summary>

					<?php if ($mrent_a !== '') : ?>
						<div class="mt-[15px] xl:mt-[25px] font-[400] text-[16px] xl:text-[24px] leading-[1.5] text-mrent-white/90 whitespace-pre-line">
							<?php echo wp_kses_post(wpautop($mrent_a)); ?>
						</div>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
