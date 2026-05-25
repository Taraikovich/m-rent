<?php

/**
 * Секция «Возможные форматы сертификатов» на странице подарочных сертификатов.
 *
 * Figma: 2218:3289.
 *
 * Сетка: 6 колонок на десктопе, по дизайну 7 карточек в три ряда (2/3/2).
 * Размер каждой карточки задаётся span-паттерном: [3,3, 2,2,2, 3,3].
 *
 * ACF (group_mrent_certificates):
 *   certificates_formats_title    (text)
 *   certificates_formats_items    (repeater: title, image, cta_text, cta_url)
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = get_field('certificates_formats_title');
$mrent_title = $mrent_title !== '' && $mrent_title !== null
	? $mrent_title
	: __('Возможные форматы сертификатов', 'm-rent');

$mrent_items = get_field('certificates_formats_items');
if (! is_array($mrent_items) || empty($mrent_items)) {
	$mrent_items = [
		['title' => __('Сертификат на фиксированную сумму', 'm-rent')],
		['title' => __('Сертификат на аренду на часы/сутки', 'm-rent')],
		['title' => __('Сертификат на фотосессию под ключ', 'm-rent')],
		['title' => __('Сертификат на конкретный автомобиль', 'm-rent')],
		['title' => __('Сертификат на weekend-тариф (2 суток)', 'm-rent')],
		['title' => __('Сертификат на аренду кабриолета', 'm-rent')],
		['title' => __('Сертификат на аренду с водителем', 'm-rent')],
	];
}

// Паттерн span по индексу карточки — задаёт раскладку 2/3/2 на десктопе.
$mrent_span_pattern = [3, 3, 2, 2, 2, 3, 3];
$mrent_span_classes = [
	2 => 'xl:col-span-2',
	3 => 'xl:col-span-3',
];

// На мобильном по дизайну видны первые 4 карточки, остальные раскрываются по «Показать все».
$mrent_mobile_visible = 4;
$mrent_total_items    = count($mrent_items);
$mrent_has_toggle     = $mrent_total_items > $mrent_mobile_visible;
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[40px] xl:pt-[100px] pb-[40px] xl:pb-[100px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

		<h2 class="font-display font-bold text-mrent-white text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2]">
			<?php echo esc_html($mrent_title); ?>
		</h2>

		<ul
			class="grid grid-cols-1 xl:grid-cols-6 gap-[15px] xl:gap-[20px] list-none p-0 m-0"
			data-formats-grid<?php echo $mrent_has_toggle ? ' data-collapsed="true"' : ''; ?>>
			<?php foreach (array_values($mrent_items) as $mrent_i => $mrent_item) :
				$mrent_card_title = isset($mrent_item['title']) ? (string) $mrent_item['title'] : '';
				$mrent_card_image = isset($mrent_item['image']) ? $mrent_item['image'] : null;
				$mrent_card_cta   = isset($mrent_item['cta_text']) && $mrent_item['cta_text'] !== ''
					? (string) $mrent_item['cta_text']
					: __('Заказать', 'm-rent');
				$mrent_card_url   = isset($mrent_item['cta_url']) && $mrent_item['cta_url'] !== ''
					? (string) $mrent_item['cta_url']
					: '#booking-form';

				$mrent_span = isset($mrent_span_pattern[$mrent_i]) ? $mrent_span_pattern[$mrent_i] : 2;
				$mrent_span_cls = $mrent_span_classes[$mrent_span];

				if (is_array($mrent_card_image) && ! empty($mrent_card_image['url'])) {
					$mrent_img_src = $mrent_card_image['url'];
					$mrent_img_alt = $mrent_card_image['alt'] !== '' ? $mrent_card_image['alt'] : $mrent_card_title;
				} else {
					$mrent_img_src = '';
					$mrent_img_alt = '';
				}
			?>
				<?php
				$mrent_hidden_mobile = $mrent_has_toggle && $mrent_i >= $mrent_mobile_visible;
				$mrent_li_cls = trim($mrent_span_cls . ' relative overflow-hidden rounded-[15px] border border-[#302F31] h-[280px] xl:h-[clamp(377px,-70px+34.9vw,600px)]' . ($mrent_hidden_mobile ? ' hidden xl:block' : ''));
				?>
				<li
					class="<?php echo esc_attr($mrent_li_cls); ?>"
					<?php if ($mrent_hidden_mobile) : ?>data-formats-extra<?php endif; ?>>
					<?php if ($mrent_img_src !== '') : ?>
						<img
							src="<?php echo esc_url($mrent_img_src); ?>"
							alt="<?php echo esc_attr($mrent_img_alt); ?>"
							class="absolute inset-0 w-full h-full object-cover"
							loading="lazy" decoding="async">
					<?php endif; ?>

					<div class="absolute inset-0 bg-gradient-to-b from-[rgba(30,29,31,0.9)] from-0% to-[rgba(30,29,31,0)] to-[57.7%]" aria-hidden="true"></div>

					<div class="relative h-full flex flex-col items-start gap-[20px] xl:gap-[30px] p-[20px] xl:p-[50px]">
						<?php if ($mrent_card_title !== '') : ?>
							<h3 class="font-display font-[600] text-mrent-white text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2] max-w-[420px]">
								<?php echo esc_html($mrent_card_title); ?>
							</h3>
						<?php endif; ?>

						<a
							href="<?php echo esc_url($mrent_card_url); ?>"
							class="font-display font-medium text-mrent-white text-[clamp(14px,13.03px+0.26vw,18px)] leading-none underline underline-offset-4 hover:text-mrent-yellow transition-colors">
							<?php echo esc_html($mrent_card_cta); ?>
						</a>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($mrent_has_toggle) : ?>
			<button
				type="button"
				class="xl:hidden self-center font-display font-medium text-mrent-yellow text-[14px] leading-none underline underline-offset-4 hover:opacity-80 transition-opacity"
				data-formats-toggle
				data-label-more="<?php echo esc_attr__('Показать все', 'm-rent'); ?>"
				data-label-less="<?php echo esc_attr__('Скрыть', 'm-rent'); ?>"
				aria-expanded="false">
				<?php esc_html_e('Показать все', 'm-rent'); ?>
			</button>
		<?php endif; ?>
	</div>
</section>

<?php if ($mrent_has_toggle) : ?>
	<script>
		(function() {
			var btn = document.currentScript.previousElementSibling.querySelector('[data-formats-toggle]');
			if (!btn) return;
			var grid = btn.parentElement.querySelector('[data-formats-grid]');
			btn.addEventListener('click', function() {
				var collapsed = grid.getAttribute('data-collapsed') === 'true';
				var next = !collapsed;
				grid.setAttribute('data-collapsed', next ? 'true' : 'false');
				btn.setAttribute('aria-expanded', next ? 'false' : 'true');
				btn.textContent = next ? btn.dataset.labelMore : btn.dataset.labelLess;
				grid.querySelectorAll('[data-formats-extra]').forEach(function(el) {
					el.classList.toggle('hidden', next);
				});
			});
		})();
	</script>
<?php endif; ?>