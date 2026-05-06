<?php

/**
 * Клубные карты: секция со списком карт (Silver / Gold).
 *
 * Контент: ACF-группа «Клубные карты», таб «Карты».
 * Если поле «Заголовок» (club_cards_title) не заполнено — секция не выводится.
 *
 * Один DOM, адаптив через `2xl:`-варианты (Figma 2597:6159 desktop / 2353:5663 mobile):
 *
 *   < 2xl  — flex-col gap-30: section description (заголовок 28px Bold + лид 16px,
 *            gap-15 внутри) → список карт flex-col gap-10. Каждая карта:
 *            bg #252426, border #302F31, rounded-15, p-25, gap-20 внутри
 *            (image → название/описание → заголовок списка/буллеты).
 *
 *   ≥ 2xl  — flex-row gap-30 items-stretch: слева текст-колонка 554px
 *            (заголовок 74px max-w-373 + лид 30px max-w-433, gap-30 внутри),
 *            справа карты flex-1 в одну строку. Каждая карта p-40, gap-40
 *            внутри, border-radius 15. items-stretch выравнивает высоты
 *            обеих карт по самой высокой (вместо фиксированной 1112px из Figma).
 *
 * Изображение карты — `aspect-[481/288]` (≈ 16:9.6) с object-cover. Fallback
 * на assets/images/club-card-{silver,gold}.png по индексу строки.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field('club_cards_title', $mrent_page_id);

if ($mrent_title === '') {
	return;
}

$mrent_lead    = (string) get_field('club_cards_lead', $mrent_page_id);
$mrent_items   = get_field('club_cards_items', $mrent_page_id);
$mrent_dir     = get_template_directory_uri() . '/assets/images';
$mrent_fallbacks = [$mrent_dir . '/club-card-silver.png', $mrent_dir . '/club-card-gold.png'];
?>

<section class="bg-mrent-black px-[15px] 2xl:px-[100px] py-[60px] 2xl:py-[100px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] 2xl:flex-row 2xl:items-stretch 2xl:gap-[30px]">

		<?php /* Левая текст-колонка. Mobile — full-width; 2xl — фикс 554px. */ ?>
		<div class="flex flex-col gap-[15px] 2xl:gap-[30px] text-mrent-white leading-[1.2] 2xl:w-[554px] 2xl:shrink-0">
			<h2 class="font-display font-[700] text-[28px] 2xl:text-[74px] 2xl:max-w-[373px]">
				<?php echo esc_html($mrent_title); ?>
			</h2>
			<?php if ($mrent_lead !== '') : ?>
				<p class="text-[16px] 2xl:text-[30px] 2xl:max-w-[433px] whitespace-pre-line">
					<?php echo esc_html($mrent_lead); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if (is_array($mrent_items) && ! empty($mrent_items)) : ?>
			<div class="flex flex-col gap-[10px] 2xl:flex-row 2xl:flex-1 2xl:items-stretch 2xl:gap-[30px]">
				<?php foreach ($mrent_items as $mrent_index => $mrent_card) :
					$mrent_card_title       = isset($mrent_card['card_title']) ? (string) $mrent_card['card_title'] : '';
					$mrent_card_description = isset($mrent_card['card_description']) ? (string) $mrent_card['card_description'] : '';
					$mrent_bullets_title    = isset($mrent_card['card_bullets_title']) ? (string) $mrent_card['card_bullets_title'] : '';
					$mrent_bullets_raw      = isset($mrent_card['card_bullets']) && is_array($mrent_card['card_bullets']) ? $mrent_card['card_bullets'] : [];

					$mrent_image     = $mrent_card['card_image'] ?? null;
					$mrent_image_url = is_array($mrent_image) && ! empty($mrent_image['url'])
						? $mrent_image['url']
						: ($mrent_fallbacks[$mrent_index] ?? '');
					$mrent_image_alt = is_array($mrent_image) ? ($mrent_image['alt'] ?? '') : '';

					$mrent_bullets = [];
					foreach ($mrent_bullets_raw as $mrent_b) {
						$mrent_text = isset($mrent_b['bullet_text']) ? (string) $mrent_b['bullet_text'] : '';
						if ($mrent_text !== '') {
							$mrent_bullets[] = $mrent_text;
						}
					}
				?>
					<article class="bg-[#252426] border border-[#302F31] rounded-[15px] p-[25px] 2xl:p-[40px] flex flex-col gap-[20px] 2xl:gap-[40px] 2xl:flex-1">

						<?php if ($mrent_image_url) : ?>
							<div class="w-full aspect-[481/288] overflow-hidden rounded-[10px]">
								<img
									src="<?php echo esc_url($mrent_image_url); ?>"
									alt="<?php echo esc_attr($mrent_image_alt); ?>"
									class="block w-full h-full object-cover"
									loading="lazy"
									decoding="async"
								>
							</div>
						<?php endif; ?>

						<?php if ($mrent_card_title !== '' || $mrent_card_description !== '') : ?>
							<div class="flex flex-col gap-[10px] 2xl:gap-[20px] text-mrent-white leading-[1.2]">
								<?php if ($mrent_card_title !== '') : ?>
									<h3 class="font-display font-[600] text-[20px] 2xl:text-[35px]">
										<?php echo esc_html($mrent_card_title); ?>
									</h3>
								<?php endif; ?>
								<?php if ($mrent_card_description !== '') : ?>
									<p class="text-[16px] 2xl:text-[30px]">
										<?php echo esc_html($mrent_card_description); ?>
									</p>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if (! empty($mrent_bullets)) : ?>
							<div class="flex flex-col gap-[15px] 2xl:gap-[20px] text-mrent-white leading-[1.2]">
								<?php if ($mrent_bullets_title !== '') : ?>
									<p class="font-display font-[600] text-[16px] 2xl:text-[30px]">
										<?php echo esc_html($mrent_bullets_title); ?>
									</p>
								<?php endif; ?>
								<ul class="flex flex-col gap-[10px]">
									<?php foreach ($mrent_bullets as $mrent_bullet) : ?>
										<li class="flex gap-[10px] items-center">
											<span class="size-[5px] 2xl:size-[7px] rounded-full bg-mrent-white shrink-0" aria-hidden="true"></span>
											<span class="text-[14px] 2xl:text-[24px]"><?php echo esc_html($mrent_bullet); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
