<?php

/**
 * Секция «Условия для аренды» на странице подарочных сертификатов.
 *
 * Figma: 2345:4504 (mobile).
 *
 * Раскладка:
 *   Mobile: одна колонка — заголовок → две группы списков → ссылка «Подробнее» → фото.
 *   Desktop (xl+, 1720): 2-кол row, текст слева (max-w-[717px]), фото справа (854×658).
 *
 * ACF (group_mrent_certificates):
 *   certificates_conditions_title        (text)
 *   certificates_conditions_groups       (repeater: title + items[text])
 *   certificates_conditions_link_text    (text)
 *   certificates_conditions_link_url     (text)
 *   certificates_conditions_image        (image)
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = (string) get_field('certificates_conditions_title');
if ($mrent_title === '') {
	$mrent_title = __('Условия для аренды', 'm-rent');
}

$mrent_groups_raw = (array) get_field('certificates_conditions_groups');
$mrent_groups     = [];
foreach ($mrent_groups_raw as $mrent_group) {
	$mrent_group_title = (string) ($mrent_group['title'] ?? '');
	$mrent_group_items = (array)  ($mrent_group['items'] ?? []);
	$mrent_items_norm  = [];
	foreach ($mrent_group_items as $mrent_row) {
		$mrent_text = (string) ($mrent_row['text'] ?? '');
		if ($mrent_text !== '') {
			$mrent_items_norm[] = $mrent_text;
		}
	}
	if ($mrent_group_title !== '' || $mrent_items_norm) {
		$mrent_groups[] = [
			'title' => $mrent_group_title,
			'items' => $mrent_items_norm,
		];
	}
}
if (! $mrent_groups) {
	$mrent_groups = [
		[
			'title' => __('Требования к арендатору', 'm-rent'),
			'items' => [
				__('Возраст: от 20 лет', 'm-rent'),
				__('Стаж вождения: от 2 лет', 'm-rent'),
			],
		],
		[
			'title' => __('Для аренды потребуется', 'm-rent'),
			'items' => [
				__('Паспорт', 'm-rent'),
				__('Водительское удостоверение', 'm-rent'),
			],
		],
	];
}

$mrent_link_text = (string) get_field('certificates_conditions_link_text');
if ($mrent_link_text === '') {
	$mrent_link_text = __('Подробнее', 'm-rent');
}

$mrent_link_url = (string) get_field('certificates_conditions_link_url');
if ($mrent_link_url === '') {
	$mrent_link_url = '#';
}

$mrent_image_acf = get_field('certificates_conditions_image');
$mrent_image_url = '';
$mrent_image_alt = '';
if (is_array($mrent_image_acf)) {
	if (! empty($mrent_image_acf['url'])) {
		$mrent_image_url = (string) $mrent_image_acf['url'];
	}
	if (! empty($mrent_image_acf['alt'])) {
		$mrent_image_alt = (string) $mrent_image_acf['alt'];
	}
}
if ($mrent_image_url === '') {
	$mrent_image_url = get_template_directory_uri() . '/assets/images/business-conditions-bmw.png';
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[40px] xl:pt-[100px] pb-[40px] xl:pb-[100px]">
	<div class="max-w-[1720px] mx-auto flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] xl:gap-[100px]">

		<div class="flex flex-col gap-[30px] xl:gap-[60px] xl:w-[717px] xl:shrink-0">
			<div class="flex flex-col gap-[15px] xl:gap-[40px]">
				<h2 class="font-display font-[700] text-mrent-white text-[28px] xl:text-[74px] leading-[1.2]">
					<?php echo esc_html($mrent_title); ?>
				</h2>

				<div class="flex flex-col gap-[15px] xl:gap-[40px]">
					<?php foreach ($mrent_groups as $mrent_group) : ?>
						<div class="flex flex-col gap-[20px]">
							<?php if ($mrent_group['title'] !== '') : ?>
								<h3 class="font-display font-[600] text-mrent-white text-[20px] xl:text-[35px] leading-[1.2]">
									<?php echo esc_html($mrent_group['title']); ?>
								</h3>
							<?php endif; ?>
							<?php if ($mrent_group['items']) : ?>
								<ul class="flex flex-col gap-[10px] xl:gap-[15px] font-[400] text-mrent-white text-[14px] xl:text-[24px] leading-[1.2]">
									<?php foreach ($mrent_group['items'] as $mrent_item) : ?>
										<li class="list-disc ms-[21px] xl:ms-[30px]">
											<?php echo esc_html($mrent_item); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<a href="<?php echo esc_url($mrent_link_url); ?>"
				class="self-start font-display font-[450] text-[18px] xl:text-[24px] leading-[1] text-mrent-yellow underline decoration-solid">
				<?php echo esc_html($mrent_link_text); ?>
			</a>
		</div>

		<div class="relative w-full h-[172px] xl:w-[854px] xl:h-[658px] xl:shrink-0 overflow-hidden">
			<img
				src="<?php echo esc_url($mrent_image_url); ?>"
				alt="<?php echo esc_attr($mrent_image_alt); ?>"
				class="absolute inset-0 size-full object-contain object-center"
				loading="lazy"
				decoding="async">
		</div>

	</div>
</section>
