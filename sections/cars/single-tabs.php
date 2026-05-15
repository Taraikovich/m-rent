<?php

/**
 * Cars: блок «Доп. инфо в табах» под `single-details`.
 *
 * 5 табов, контент полностью из ACF (group_mrent_car). Пилюля и панель таба
 * рендерятся только если соответствующее поле заполнено; если ни одного нет —
 * секция целиком ничего не выводит. Активный таб — первый непустой.
 *
 * ACF (group_mrent_car):
 *   • Tab «Доп. услуги»     ← car_services (repeater)
 *   • Tab «Характеристики»  ← car_specs_rows (repeater) + car_specs_ideal_* + car_specs_pros_*
 *   • Tab «Условия аренды»  ← car_terms_items (repeater) + car_terms_image (опц.)
 *   • Tab «Отзывы»          ← car_reviews (repeater)
 *   • Tab «Вопрос-ответ»    ← car_faq_items (repeater)
 *
 * Переключение табов и моб. спойлер «Подробнее» в характеристиках —
 * в `src/single-car-tabs.js`. FAQ-аккордеон — нативный `<details>` со стилями
 * `.mrent-faq-item` из `src/main.css` (CSS-only animation).
 */

if (! defined('ABSPATH')) {
	exit;
}

/* ── 1. Тянем ACF ─────────────────────────────────────────────────────── */

$mrent_services      = (array) get_field('car_services');
$mrent_specs_rows    = (array) get_field('car_specs_rows');
$mrent_specs_ideal   = (string) get_field('car_specs_ideal_items');
$mrent_specs_pros    = (string) get_field('car_specs_pros_items');
$mrent_terms_items   = (array) get_field('car_terms_items');
$mrent_reviews       = (array) get_field('car_reviews');
$mrent_faq_items     = (array) get_field('car_faq_items');

/* Картинки: «Характеристики» (полупрозрачный фон) и «Условия аренды»
   (рядом с текстом) — отдельные ACF-поля. Если поле пустое — каскад:
   первое фото галереи → featured. */
$mrent_specs_image_field = get_field('car_specs_image');
$mrent_terms_image_field = get_field('car_terms_image');

$mrent_gallery     = (array) get_field('car_gallery');
$mrent_image_fallback = '';
if (! empty($mrent_gallery[0]['url'])) {
	$mrent_image_fallback = (string) $mrent_gallery[0]['url'];
} else {
	$mrent_image_fallback = (string) get_the_post_thumbnail_url(get_the_ID(), 'large');
}

$mrent_specs_bg = is_array($mrent_specs_image_field) && ! empty($mrent_specs_image_field['url'])
	? (string) $mrent_specs_image_field['url']
	: $mrent_image_fallback;

$mrent_terms_image = is_array($mrent_terms_image_field) && ! empty($mrent_terms_image_field['url'])
	? (string) $mrent_terms_image_field['url']
	: $mrent_image_fallback;

/* ── 2. Какие табы вообще показывать ─────────────────────────────────── */

$mrent_specs_filled = ! empty($mrent_specs_rows) || $mrent_specs_ideal !== '' || $mrent_specs_pros !== '';

$mrent_tabs = [];
if (! empty($mrent_services)) {
	$mrent_tabs['services'] = __('Дополнительные услуги', 'm-rent');
}
if ($mrent_specs_filled) {
	$mrent_tabs['specifications'] = __('Характеристики', 'm-rent');
}
if (! empty($mrent_terms_items)) {
	$mrent_tabs['terms'] = __('Условия аренды', 'm-rent');
}
if (! empty($mrent_reviews)) {
	$mrent_tabs['reviews'] = __('Отзывы', 'm-rent');
}
if (! empty($mrent_faq_items)) {
	$mrent_tabs['faq'] = __('Вопрос-ответ', 'm-rent');
}

if (empty($mrent_tabs)) {
	return;
}

$mrent_active = array_key_first($mrent_tabs);

/* Заголовки опц. колонок «Характеристик» с дефолтами. */
$mrent_specs_ideal_title = (string) (get_field('car_specs_ideal_title') ?: __('Идеально для:', 'm-rent'));
$mrent_specs_pros_title  = (string) (get_field('car_specs_pros_title')  ?: __('Преимущества:', 'm-rent'));

$mrent_specs_ideal_lines = $mrent_specs_ideal !== ''
	? array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $mrent_specs_ideal))))
	: [];
$mrent_specs_pros_lines  = $mrent_specs_pros !== ''
	? array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $mrent_specs_pros))))
	: [];
?>

<section class="px-[15px] xl:px-[100px] mt-[40px] xl:mt-[100px]" data-car-tabs>
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

		<?php /* ── Mobile: dropdown с активным табом ── */ ?>
		<details class="xl:hidden relative" data-car-tabs-mobile>
			<summary class="list-none bg-mrent-white flex items-center justify-between h-[55px] rounded-[15px] px-[15px] cursor-pointer [&::-webkit-details-marker]:hidden">
				<span class="font-[500] text-[14px] text-mrent-black leading-none" data-mobile-active-label>
					<?php echo esc_html($mrent_tabs[$mrent_active]); ?>
				</span>
				<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M1 3L5 7L9 3" stroke="#1E1D1F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</summary>
			<ul class="absolute left-0 right-0 mt-[8px] z-10 bg-mrent-white rounded-[15px] overflow-hidden shadow-lg">
				<?php foreach ($mrent_tabs as $key => $label) : ?>
					<li>
						<button type="button" class="w-full text-left px-[15px] py-[12px] font-[500] text-[14px] text-mrent-black hover:bg-black/5" data-tab="<?php echo esc_attr($key); ?>">
							<?php echo esc_html($label); ?>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
		</details>

		<?php /* ── Desktop: пилюли, активный белый ── */ ?>
		<div class="hidden xl:flex items-center gap-[20px] w-full">
			<?php foreach ($mrent_tabs as $key => $label) :
				$is_active = ($key === $mrent_active);
			?>
				<button type="button"
					class="<?php echo $is_active ? 'bg-mrent-white text-mrent-black' : 'bg-[#252426] text-mrent-white'; ?> flex-1 h-[65px] flex items-center justify-center rounded-[15px] font-[500] text-[20px] leading-none whitespace-nowrap px-[15px] transition-colors"
					data-tab="<?php echo esc_attr($key); ?>"
					aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
					<?php echo esc_html($label); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php /* ─────────────── Tab: Доп. услуги ─────────────── */ ?>
		<?php if (isset($mrent_tabs['services'])) : ?>
			<div<?php echo $mrent_active === 'services' ? '' : ' hidden'; ?> class="flex flex-col gap-[15px] xl:gap-[60px]" data-tab-panel="services">
				<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2] text-mrent-white">
					<?php esc_html_e('Дополнительные услуги', 'm-rent'); ?>
				</h2>
				<div class="flex flex-col xl:flex-row xl:items-stretch gap-[10px] xl:gap-[30px] w-full">
					<?php foreach ($mrent_services as $svc) :
						$svc_title        = (string) ($svc['title'] ?? '');
						$svc_title_accent = (string) ($svc['title_accent'] ?? '');
						$svc_body         = (string) ($svc['body'] ?? '');
						$svc_price_usd    = (float) ($svc['price_usd'] ?? 0);
						$svc_price_main   = $svc_price_usd > 0
							? '+ ' . mrent_byn_price($svc_price_usd)
							: (string) ($svc['price_main'] ?? '');
						$svc_price_suffix = (string) ($svc['price_suffix'] ?? '');
						$svc_btn_text     = (string) ($svc['button_text'] ?? '');
						$svc_btn_url      = (string) ($svc['button_url'] ?? '#booking');
					?>
						<div class="bg-[#252426] border border-[#302f31] rounded-[15px] p-[25px] xl:p-[40px] flex flex-col gap-[25px] xl:gap-[40px] flex-1 xl:min-h-[406px]">
							<div class="flex flex-col gap-[15px] xl:gap-[30px] text-mrent-white flex-1">
								<div class="flex flex-col gap-[15px] xl:gap-[20px]">
									<h3 class="font-[800] text-[20px] xl:text-[35px] leading-[1.2]">
										<?php echo esc_html($svc_title); ?>
										<?php if ($svc_title_accent !== '') : ?>
											<span class="text-mrent-yellow"><?php echo esc_html($svc_title_accent); ?></span>
										<?php endif; ?>
									</h3>
									<?php if ($svc_body !== '') : ?>
										<div class="mrent-article text-[14px] xl:text-[24px] leading-[1.2]">
											<?php echo wp_kses_post(mrent_convert_usd_in_text($svc_body)); ?>
										</div>
									<?php endif; ?>
								</div>
								<?php if ($svc_price_main !== '' || $svc_price_suffix !== '') : ?>
									<p class="leading-[1.2]">
										<?php if ($svc_price_main !== '') : ?>
											<span class="font-[800] text-[20px] xl:text-[35px]"><?php echo esc_html($svc_price_main); ?></span>
										<?php endif; ?>
										<?php if ($svc_price_suffix !== '') : ?>
											<span class="text-[14px] xl:text-[24px]"> <?php echo esc_html($svc_price_suffix); ?></span>
										<?php endif; ?>
									</p>
								<?php endif; ?>
							</div>
							<?php if ($svc_btn_text !== '') : ?>
								<a href="<?php echo esc_url($svc_btn_url ?: '#booking'); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center h-[50px] xl:h-[65px] rounded-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] transition-colors">
									<?php echo esc_html($svc_btn_text); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
	</div>
<?php endif; ?>

<?php /* ─────────────── Tab: Характеристики ─────────────── */ ?>
<?php if (isset($mrent_tabs['specifications'])) : ?>
	<div<?php echo $mrent_active === 'specifications' ? '' : ' hidden'; ?> class="flex flex-col gap-[20px] xl:gap-[60px]" data-tab-panel="specifications">
		<div class="relative">
			<?php if ($mrent_specs_bg) : ?>
				<img src="<?php echo esc_url($mrent_specs_bg); ?>" alt="" aria-hidden="true"
					class="hidden xl:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[707px] h-[319px] object-cover object-bottom pointer-events-none">
			<?php endif; ?>

			<?php /* 3 колонки на десктопе; mobile: только specs, остальное прячется `max-xl:hidden`,
					         JS снимает класс по «Подробнее». */ ?>
			<div class="relative flex flex-col xl:flex-row xl:items-start xl:justify-between gap-[20px] xl:gap-[40px] text-mrent-white">

				<?php if (! empty($mrent_specs_rows)) : ?>
					<div class="xl:flex-1 xl:basis-0 xl:min-w-0 flex flex-col gap-[10px] text-[16px] xl:text-[30px] leading-[1.24]">
						<?php foreach ($mrent_specs_rows as $row) :
							$row_label  = (string) ($row['label'] ?? '');
							$row_value  = (string) ($row['value'] ?? '');
							$mobile_only = ! empty($row['mobile_only']);
							$row_class  = $mobile_only ? 'xl:hidden' : '';
						?>
							<p class="<?php echo esc_attr($row_class); ?>">
								<?php if ($row_label !== '') : ?>
									<span class="font-[700]"><?php echo esc_html($row_label); ?>:</span>
								<?php endif; ?>
								<span class="font-[400]"><?php echo $row_label !== '' ? ' ' : ''; ?><?php echo esc_html($row_value); ?></span>
							</p>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if (! empty($mrent_specs_ideal_lines)) : ?>
					<div data-spec-extra class="max-xl:hidden xl:flex-1 xl:basis-0 xl:min-w-0 flex flex-col gap-[10px] text-[16px] xl:text-[30px] leading-[1.24]">
						<p class="font-[700]"><?php echo esc_html($mrent_specs_ideal_title); ?></p>
						<ul class="list-disc pl-[20px] xl:pl-[45px] flex flex-col gap-[2px] xl:gap-0 font-[400]">
							<?php foreach ($mrent_specs_ideal_lines as $line) : ?>
								<li><?php echo esc_html($line); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if (! empty($mrent_specs_pros_lines)) : ?>
					<div data-spec-extra class="max-xl:hidden xl:flex-1 xl:basis-0 xl:min-w-0 flex flex-col gap-[10px] text-[16px] xl:text-[30px] leading-[1.24]">
						<p class="font-[700]"><?php echo esc_html($mrent_specs_pros_title); ?></p>
						<ul class="list-disc pl-[20px] xl:pl-[45px] flex flex-col gap-[2px] xl:gap-0 font-[400]">
							<?php foreach ($mrent_specs_pros_lines as $line) : ?>
								<li><?php echo esc_html($line); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php if (! empty($mrent_specs_ideal_lines) || ! empty($mrent_specs_pros_lines)) : ?>
			<button type="button" class="xl:hidden text-mrent-yellow underline font-[500] text-[18px] self-start"
				data-spec-toggle
				data-label-more="<?php esc_attr_e('Подробнее', 'm-rent'); ?>"
				data-label-less="<?php esc_attr_e('Свернуть', 'm-rent'); ?>">
				<?php esc_html_e('Подробнее', 'm-rent'); ?>
			</button>
		<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php /* ─────────────── Tab: Условия аренды ─────────────── */ ?>
	<?php if (isset($mrent_tabs['terms'])) : ?>
		<div<?php echo $mrent_active === 'terms' ? '' : ' hidden'; ?> class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] xl:gap-[40px]" data-tab-panel="terms">
			<div class="flex flex-col gap-[15px] xl:gap-[30px] text-mrent-white xl:w-[756px] xl:shrink-0">
				<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2]">
					<?php esc_html_e('Условия аренды', 'm-rent'); ?>
				</h2>
				<ul class="list-disc pl-[20px] xl:pl-[30px] flex flex-col gap-[4px] xl:gap-[6px] text-[16px] xl:text-[30px] leading-[1.24]">
					<?php foreach ($mrent_terms_items as $item) :
						$item_label = (string) ($item['label'] ?? '');
						$item_value = (string) ($item['value'] ?? '');
					?>
						<li>
							<?php if ($item_label !== '') : ?>
								<span class="font-[700]"><?php echo esc_html($item_label); ?></span>
							<?php endif; ?>
							<span class="font-[400]"><?php echo esc_html($item_value); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<?php if ($mrent_terms_image) : ?>
				<div class="rounded-[15px] overflow-hidden w-full xl:w-auto xl:flex-1 xl:min-w-0 h-[267px] xl:h-[480px]">
					<img src="<?php echo esc_url($mrent_terms_image); ?>" alt="" class="w-full h-full object-cover">
				</div>
			<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php /* ─────────────── Tab: Отзывы ─────────────── */ ?>
		<?php if (isset($mrent_tabs['reviews'])) : ?>
			<div<?php echo $mrent_active === 'reviews' ? '' : ' hidden'; ?> class="flex flex-col xl:flex-row gap-[10px] xl:gap-[29px] xl:items-stretch" data-tab-panel="reviews">
				<?php foreach ($mrent_reviews as $r) :
					$rev_author = (string) ($r['author'] ?? '');
					$rev_car    = (string) ($r['car'] ?? '');
					$rev_text   = (string) ($r['text'] ?? '');
					$rev_rating = (int) ($r['rating'] ?? 5);
					$rev_rating = max(0, min(5, $rev_rating));
				?>
					<div class="bg-[#252426] border border-[#302f31] rounded-[15px] p-[25px] xl:p-[35px] flex flex-col gap-[20px] flex-1 xl:min-h-[419px] text-mrent-white">
						<div class="flex flex-col gap-[15px]">
							<div class="flex flex-col gap-[15px] xl:gap-[20px]">
								<div class="flex items-center gap-[6px]" aria-label="<?php echo esc_attr(sprintf(__('Рейтинг %d из 5', 'm-rent'), $rev_rating)); ?>">
									<?php for ($i = 0; $i < 5; $i++) :
										$on = $i < $rev_rating;
									?>
										<svg width="20" height="19" viewBox="0 0 20 19" fill="<?php echo $on ? '#FFD700' : '#3a393b'; ?>" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="xl:w-[28px] xl:h-[27px]">
											<path d="M10 0l2.939 6.038 6.661.967-4.82 4.69 1.138 6.626L10 15.2l-5.918 3.121 1.138-6.626L.4 7.005l6.661-.967L10 0z" />
										</svg>
									<?php endfor; ?>
								</div>
								<p class="font-[800] xl:font-[600] text-[20px] xl:text-[30px] leading-[1.2]">
									<?php echo esc_html($rev_author); ?>
								</p>
							</div>
							<?php if ($rev_car !== '') : ?>
								<p class="font-[400] text-[14px] xl:text-[18px] leading-[1.2] xl:leading-[1.3]">
									<?php echo esc_html($rev_car); ?>
								</p>
							<?php endif; ?>
						</div>
						<p class="font-[400] text-[14px] xl:text-[24px] leading-[1.2]">
							<?php echo esc_html($rev_text); ?>
						</p>
					</div>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php /* ─────────────── Tab: Вопрос-ответ ─────────────── */ ?>
			<?php if (isset($mrent_tabs['faq'])) : ?>
				<div<?php echo $mrent_active === 'faq' ? '' : ' hidden'; ?> class="flex flex-col gap-[30px] xl:gap-[60px]" data-tab-panel="faq">
					<h2 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2] text-mrent-white">
						<?php esc_html_e('Часто задаваемые вопросы', 'm-rent'); ?>
					</h2>

					<div class="flex flex-col gap-[10px] xl:grid xl:grid-cols-2 xl:items-start xl:gap-[30px] w-full">
						<?php foreach ($mrent_faq_items as $faq) :
							$faq_q = (string) ($faq['question'] ?? '');
							$faq_a = (string) ($faq['answer'] ?? '');
							if ($faq_q === '') {
								continue;
							}
						?>
							<details class="mrent-faq-item group bg-[#252426] border border-[#302F31] rounded-[15px] p-[20px] xl:p-[40px]">
								<summary class="flex items-center justify-between gap-[20px] cursor-pointer">
									<span class="font-[600] text-[18px] xl:text-[30px] leading-[1.2] text-mrent-white">
										<?php echo esc_html($faq_q); ?>
									</span>
									<span class="mrent-faq-icon shrink-0 flex items-center justify-center bg-mrent-yellow rounded-[8px] xl:rounded-[12px] size-[25px] xl:size-[36px] text-mrent-black" aria-hidden="true">
										<svg viewBox="0 0 16 9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[10px] h-[6px] xl:w-[14px] xl:h-[8px]">
											<polyline points="1 1 8 8 15 1" />
										</svg>
									</span>
								</summary>

								<?php if ($faq_a !== '') : ?>
									<div class="mt-[10px] xl:mt-[20px] font-[400] text-[14px] xl:text-[24px] leading-[1.2] text-mrent-white whitespace-pre-line">
										<?php echo wp_kses_post(wpautop($faq_a)); ?>
									</div>
								<?php endif; ?>
							</details>
						<?php endforeach; ?>
					</div>
					</div>
				<?php endif; ?>

				</div>
</section>