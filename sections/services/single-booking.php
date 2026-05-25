<?php

/**
 * Секция «Как забронировать?» на странице услуги
 * (Figma desktop 2120:1448, mobile 2535:6217).
 *
 * Раскладка:
 *   Desktop (xl+, max-w-1720): левая колонка (заголовок 74px Bold + список шагов
 *     24px Book с круглыми маркерами 8px + жёлтая CTA 300×65) и картинка 850px
 *     справа во всю высоту, rounded-15. items-center, justify-between.
 *   Mobile: вертикальная стопка — заголовок 28px → список 14px (маркеры 5px) →
 *     CTA на всю ширину h-55 → картинка h-175 rounded-15.
 *
 * ACF (group_mrent_service):
 *   • service_booking_title    — Text (плейсхолдер «Как забронировать?»)
 *   • service_booking_steps    — Repeater {text}; если пусто — секция не выводится
 *   • service_booking_cta_text — Text; по умолчанию «Оставить заявку»
 *   • service_booking_cta_url  — Text; пусто → `#booking`
 *   • service_booking_image    — Image; пусто → блок-картинка не выводится
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_booking_title    = (string) get_field('service_booking_title');
$mrent_booking_steps    = (array) get_field('service_booking_steps');
$mrent_booking_cta_text = (string) get_field('service_booking_cta_text');
$mrent_booking_cta_url  = (string) get_field('service_booking_cta_url');
$mrent_booking_image    = get_field('service_booking_image');

if (empty($mrent_booking_steps)) {
	return;
}

if ($mrent_booking_cta_text === '') {
	$mrent_booking_cta_text = __('Оставить заявку', 'm-rent');
}
if ($mrent_booking_cta_url === '') {
	$mrent_booking_cta_url = '#booking';
}
$mrent_booking_has_image = is_array($mrent_booking_image) && ! empty($mrent_booking_image['url']);
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
	<div class="max-w-[1720px] mx-auto flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] xl:gap-[30px] text-mrent-white">

		<div class="flex flex-col gap-[30px] xl:gap-[50px] xl:shrink-0">
			<div class="flex flex-col gap-[15px] xl:gap-[30px]">
				<?php if ($mrent_booking_title !== '') : ?>
					<h2 class="font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2]">
						<?php echo esc_html($mrent_booking_title); ?>
					</h2>
				<?php endif; ?>

				<ul class="flex flex-col gap-[15px] m-0 p-0 list-none xl:w-[644px]">
					<?php foreach ($mrent_booking_steps as $mrent_booking_step) :
						$mrent_booking_step_text = (string) ($mrent_booking_step['text'] ?? '');
						if ($mrent_booking_step_text === '') {
							continue;
						}
					?>
						<li class="flex items-center gap-[10px] xl:gap-[15px]">
							<span class="shrink-0 size-[5px] xl:size-[8px] rounded-full bg-mrent-white" aria-hidden="true"></span>
							<span class="font-[400] text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.2]">
								<?php echo esc_html($mrent_booking_step_text); ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<a
				href="<?php echo esc_url($mrent_booking_cta_url); ?>"
				class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full xl:w-[300px] px-[15px] text-mrent-black font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] whitespace-nowrap transition-colors">
				<?php echo esc_html($mrent_booking_cta_text); ?>
			</a>
		</div>

		<?php if ($mrent_booking_has_image) : ?>
			<div class="h-[175px] xl:h-auto xl:self-stretch xl:flex-1 xl:min-w-0 xl:max-w-[850px] rounded-[15px] overflow-hidden">
				<img
					src="<?php echo esc_url($mrent_booking_image['url']); ?>"
					alt="<?php echo esc_attr($mrent_booking_image['alt'] ?? ''); ?>"
					class="w-full h-full object-cover"
					loading="lazy"
					decoding="async">
			</div>
		<?php endif; ?>

	</div>
</section>