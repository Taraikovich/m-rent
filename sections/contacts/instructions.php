<?php

/**
 * Секция «Как взять на прокат автомобиль?» (Figma 5379:7464 desktop / 2324:3243 mobile).
 *
 * Раскладка:
 *   Десктоп (1920px):
 *     • Заголовок 74px (слева) + жёлтая CTA 300×65 (справа), одна строка, h=89.
 *     • Под заголовком отступ 60px и фоновое изображение 1720×860, rounded-15.
 *     • Поверх изображения — градиент слева направо: rgba(30,29,31,0.5) 50% → transparent 62.85%.
 *     • Overlay слева — 4 step-карточки.
 *
 *   Мобайл (360px):
 *     • Заголовок 28px (без кнопки в шапке).
 *     • Через 30px — колонка из 4 «step» карточек (gap 10).
 *     • Снизу — CTA на всю ширину h-55, text 14px.
 *
 * Триггер модалки — href="#booking-form" (см. src/booking-modal.js).
 *
 * ACF (group_mrent_terms):
 *   • terms_title    — Text; default «Как взять на прокат автомобиль?»
 *   • terms_bg_image — Image; fallback /assets/images/instructions/bg-car.png
 *   • terms_steps    — Repeater (4 шт.): icon (Image), title (Text), text (Textarea)
 *                      Если репитер пуст — берутся 4 дефолтных шага и иконки из темы.
 *   • terms_cta_text — Text; default «Оставить заявку»
 *   • terms_cta_url  — Text; default «#booking-form»
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_uri = get_template_directory_uri();

$mrent_title = (string) get_field('terms_title');
if ($mrent_title === '') {
	$mrent_title = __('Как взять на прокат автомобиль?', 'm-rent');
}

$mrent_cta_text = (string) get_field('terms_cta_text');
if ($mrent_cta_text === '') {
	$mrent_cta_text = __('Оставить заявку', 'm-rent');
}

$mrent_cta_url = (string) get_field('terms_cta_url');
if ($mrent_cta_url === '') {
	$mrent_cta_url = '#booking-form';
}

$mrent_bg_url = '';
$mrent_bg_img = get_field('terms_bg_image');
if (is_array($mrent_bg_img) && ! empty($mrent_bg_img['url'])) {
	$mrent_bg_url = $mrent_bg_img['url'];
}
if ($mrent_bg_url === '') {
	$mrent_bg_url = $mrent_uri . '/assets/images/instructions/bg-car.png';
}

$mrent_steps     = [];
$mrent_steps_raw = get_field('terms_steps');
if (is_array($mrent_steps_raw) && $mrent_steps_raw) {
	foreach ($mrent_steps_raw as $idx => $row) {
		$icon_url = '';
		if (is_array($row['icon'] ?? null) && ! empty($row['icon']['url'])) {
			$icon_url = $row['icon']['url'];
		}
		if ($icon_url === '') {
			$icon_url = $mrent_uri . '/assets/images/instructions/step-' . ($idx + 1) . '.png';
		}
		$mrent_steps[] = [
			'icon'  => $icon_url,
			'title' => (string) ($row['title'] ?? ''),
			'text'  => (string) ($row['text'] ?? ''),
		];
	}
}
if (! $mrent_steps) {
	$mrent_steps = [
		[
			'icon'  => $mrent_uri . '/assets/images/instructions/step-1.png',
			'title' => __('Оставьте заявку', 'm-rent'),
			'text'  => __('Заполните форму на сайте или позвоните нам', 'm-rent'),
		],
		[
			'icon'  => $mrent_uri . '/assets/images/instructions/step-2.png',
			'title' => __('Выберите автомобиль', 'm-rent'),
			'text'  => __('Выберите подходящий автомобиль из нашего автопарка', 'm-rent'),
		],
		[
			'icon'  => $mrent_uri . '/assets/images/instructions/step-3.png',
			'title' => __('Внесите оплату и депозит', 'm-rent'),
			'text'  => __('Оплатите аренду автомобиля и оставьте страховой депозит', 'm-rent'),
		],
		[
			'icon'  => $mrent_uri . '/assets/images/instructions/step-4.png',
			'title' => __('Получите автомобиль', 'm-rent'),
			'text'  => __('Подпишите договор, получите ключи и наслаждайтесь поездкой', 'm-rent'),
		],
	];
}
?>

<section class="bg-mrent-black pt-[40px] xl:pt-[80px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto">

			<?php /* Заголовок + (десктопная) CTA */ ?>
			<div class="flex items-center justify-between gap-[20px] xl:h-[89px]">
				<h2 class="text-mrent-white font-[700] text-[28px] xl:text-[74px] leading-[1.2]">
					<?php echo esc_html($mrent_title); ?>
				</h2>
				<a
					href="<?php echo esc_url($mrent_cta_url); ?>"
					class="hidden xl:flex bg-mrent-yellow hover:bg-[#FFF831] rounded-[15px] h-[65px] w-[300px] items-center justify-center text-mrent-black font-[500] text-[20px] whitespace-nowrap transition-colors shrink-0">
					<?php echo esc_html($mrent_cta_text); ?>
				</a>
			</div>

			<?php /* Контейнер шагов.
			         Мобайл: обычный flow, колонка карточек на всю ширину.
			         Десктоп: фоновое изображение + градиент, карточки overlay слева. */ ?>
			<div class="mt-[30px] xl:mt-[60px] relative xl:h-[860px] xl:rounded-[15px] xl:overflow-hidden">
				<img
					src="<?php echo esc_url($mrent_bg_url); ?>"
					alt=""
					class="hidden xl:block absolute inset-0 size-full object-cover"
					loading="lazy"
					decoding="async">
				<span
					class="hidden xl:block absolute inset-0 bg-gradient-to-r from-[rgba(30,29,31,0.5)] from-[50%] to-transparent to-[62.849%]"
					aria-hidden="true"></span>

				<ul class="relative flex flex-col gap-[10px] xl:absolute xl:inset-y-0 xl:left-0 xl:w-1/2 xl:justify-center xl:gap-[20px] xl:p-[40px]">
					<?php foreach ($mrent_steps as $i => $step) : ?>
						<li class="relative flex items-center gap-[15px] rounded-[15px] border border-mrent-white/50 bg-[rgba(37,36,38,0.44)] backdrop-blur-[15px] p-[25px] w-full">
							<div class="relative flex items-start shrink-0">
								<div class="w-[30.417px] xl:w-[73px] h-[50px] xl:h-[120px] flex items-center justify-center">
									<img
										src="<?php echo esc_url($step['icon']); ?>"
										alt=""
										class="w-full h-full object-contain"
										loading="lazy"
										decoding="async">
								</div>
								<span class="flex items-center justify-center size-[15px] xl:size-[36px] rounded-[3.333px] bg-mrent-yellow text-mrent-black text-[10px] xl:text-[16px] leading-[1.3] font-[600]">
									<?php echo esc_html((string) ($i + 1)); ?>
								</span>
							</div>
							<div class="flex flex-col gap-[10px] flex-1 min-w-0 text-mrent-white leading-[1.2]">
								<p class="font-[700] text-[20px] xl:text-[35px]"><?php echo esc_html($step['title']); ?></p>
								<p class="font-[400] text-[14px] xl:text-[24px]"><?php echo esc_html($step['text']); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<?php /* Мобайл: CTA */ ?>
			<a
				href="<?php echo esc_url($mrent_cta_url); ?>"
				class="xl:hidden mt-[30px] flex h-[55px] items-center justify-center rounded-[15px] bg-mrent-yellow hover:bg-[#FFF831] px-[15px] text-mrent-black font-[500] text-[14px] whitespace-nowrap transition-colors">
				<?php echo esc_html($mrent_cta_text); ?>
			</a>

		</div>
	</div>
</section>