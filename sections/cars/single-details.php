<?php

/**
 * Блок «Описание модели + прайс + депозит + CTA» под галереей.
 *
 * Самодостаточная секция: тянет ACF-поля текущей машины напрямую.
 *
 * Layout:
 *   • Title section: на мобайле название + описание стопкой; на xl+ — в одну
 *     строку (название слева, описание справа), justify-between, gap 60px.
 *   • Prices grid: 16 ячеек (8 длительностей + 8 цен) в одной CSS-сетке.
 *     На мобайле `grid-cols-2` + flow row → 8 строк «длительность | цена».
 *     На xl+ `grid-cols-8 grid-rows-2 grid-flow-col` → 2 строки (длительности
 *     наверху, цены внизу), фон контейнера #302f31 + gap-px = 1px-линии между
 *     ячейками вместо border'ов (чище, чем negative margins).
 *   • Additional services: депозит слева, кнопка «Забронировать» справа.
 *     Mobile: стопкой, кнопка w-full. Desktop: justify-between, кнопка 300×65.
 *
 * Figma: 416:1072 (desktop) / 2326:3905 (mobile).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_short       = (string) get_field('car_short_description');
$mrent_deposit     = (float) get_field('car_deposit');
$mrent_prices      = (array) get_field('car_prices');
$mrent_booking_url = get_field('car_booking_url') ?: '#booking';
?>

<div class="px-[15px] 2xl:px-0 mt-[30px] xl:mt-[60px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

		<?php /* ── Заголовок + описание ── (mobile стопкой, desktop в строку 50/50) */ ?>
		<div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-[15px] xl:gap-[60px] text-mrent-white w-full leading-[1.2]">
			<h1 class="font-[700] text-[28px] xl:text-[70px] xl:flex-1 xl:max-w-[872px]"><?php the_title(); ?></h1>
			<?php if ($mrent_short) : ?>
				<p class="text-[16px] xl:text-[24px] xl:flex-1 xl:max-w-[774px] mt-[20px]"><?php echo nl2br(esc_html($mrent_short)); ?></p>
			<?php endif; ?>
		</div>

		<?php /* ── Прайс-таблица ──
		         Mobile (2 кол × 8 строк): длительности слева, цены справа, по строке на тариф.
		         Desktop (8 кол × 2 строки): длительности сверху, цены снизу — переключение
		         через `xl:grid-flow-col` при `xl:grid-cols-8 xl:grid-rows-2`.
		         HTML-порядок ячеек [D1, P1, D2, P2, ..., D8, P8] подходит обоим:
		           • mobile (flow row): D1|P1 / D2|P2 / ... / D8|P8
		           • desktop (flow col): col1[D1,P1], col2[D2,P2], ..., col8[D8,P8]
		         Расстояние между ячейками — `gap-px` поверх `bg-[#302f31]` контейнера.
		         Скруглённые углы только у контейнера + `overflow-hidden` обрезает
		         внутренние ячейки. ── */ ?>
		<?php if (! empty($mrent_prices)) : ?>
			<div class="grid grid-cols-2 xl:grid-cols-8 xl:grid-rows-2 xl:grid-flow-col gap-px bg-[#302f31] rounded-[15px] overflow-hidden w-full text-mrent-white">
				<?php foreach ($mrent_prices as $row) : ?>
					<div class="bg-[#252426] flex items-center justify-center px-[10px] py-[15px] xl:p-[20px] text-[16px] xl:text-[24px] text-center leading-[1.2] min-h-[50px]">
						<?php echo esc_html($row['duration']); ?>
					</div>
					<div class="bg-[#252426] flex items-center justify-center px-[10px] py-[15px] xl:p-[20px] text-[16px] xl:text-[20px] text-center leading-[1.2] min-h-[50px] font-[800] whitespace-nowrap">
						<?php echo esc_html(mrent_byn_price((float) $row['price'], (string) ($row['price_suffix'] ?? ''))); ?>
						<?php if (! empty($row['discount'])) : ?>
							<sup class="ml-[2px] text-mrent-yellow text-[9px] xl:text-[13px] font-[800]"><?php echo esc_html($row['discount']); ?></sup>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php /* ── Депозит + кнопка ── */ ?>
		<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] w-full">
			<?php if ($mrent_deposit > 0) : ?>
				<div class="flex items-center gap-[15px] xl:gap-[20px] text-mrent-white">
					<?php /* bi:cash-coin — иконка-«монета» из дизайна. */ ?>
					<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
						<g clip-path="url(#clip0_2924_7435)">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M17.875 24.375C19.5989 24.375 21.2522 23.6902 22.4712 22.4712C23.6902 21.2522 24.375 19.5989 24.375 17.875C24.375 16.1511 23.6902 14.4978 22.4712 13.2788C21.2522 12.0598 19.5989 11.375 17.875 11.375C16.1511 11.375 14.4978 12.0598 13.2788 13.2788C12.0598 14.4978 11.375 16.1511 11.375 17.875C11.375 19.5989 12.0598 21.2522 13.2788 22.4712C14.4978 23.6902 16.1511 24.375 17.875 24.375ZM26 17.875C26 20.0299 25.144 22.0965 23.6202 23.6202C22.0965 25.144 20.0299 26 17.875 26C15.7201 26 13.6535 25.144 12.1298 23.6202C10.606 22.0965 9.75 20.0299 9.75 17.875C9.75 15.7201 10.606 13.6535 12.1298 12.1298C13.6535 10.606 15.7201 9.75 17.875 9.75C20.0299 9.75 22.0965 10.606 23.6202 12.1298C25.144 13.6535 26 15.7201 26 17.875Z" fill="#FFD700" />
							<path d="M15.3376 19.409C15.4139 20.3775 16.1793 21.1315 17.5524 21.2225V21.9375H18.1618V21.2176C19.5837 21.1185 20.4141 20.358 20.4141 19.2563C20.4141 18.252 19.7803 17.7352 18.6428 17.4688L18.1618 17.355V15.405C18.7728 15.4749 19.1596 15.808 19.2522 16.2695H20.3214C20.2451 15.3351 19.4439 14.6055 18.1618 14.5259V13.8125H17.5524V14.5437C16.3386 14.6624 15.5131 15.392 15.5131 16.4255C15.5131 17.3387 16.1273 17.9205 17.1494 18.1577L17.5524 18.2569V20.3239C16.9284 20.2296 16.5141 19.8851 16.4214 19.409H15.3376ZM17.5476 17.2088C16.9479 17.0706 16.6229 16.7862 16.6229 16.3605C16.6229 15.8827 16.9739 15.5252 17.5524 15.4212V17.2088H17.5476ZM18.2496 18.421C18.9792 18.59 19.3139 18.863 19.3139 19.3456C19.3139 19.8965 18.8963 20.2735 18.1618 20.3434V18.4015L18.2496 18.421Z" fill="#FFD700" />
							<path d="M1.625 0C1.19402 0 0.780698 0.171205 0.475951 0.475951C0.171205 0.780698 0 1.19402 0 1.625L0 14.625C0 15.056 0.171205 15.4693 0.475951 15.774C0.780698 16.0788 1.19402 16.25 1.625 16.25H8.25988C8.35521 15.6899 8.49496 15.1482 8.67912 14.625H4.875C4.875 13.763 4.53259 12.9364 3.9231 12.3269C3.3136 11.7174 2.48695 11.375 1.625 11.375V4.875C2.48695 4.875 3.3136 4.53259 3.9231 3.9231C4.53259 3.3136 4.875 2.48695 4.875 1.625H21.125C21.125 2.48695 21.4674 3.3136 22.0769 3.9231C22.6864 4.53259 23.513 4.875 24.375 4.875V10.608C24.9925 11.1605 25.5401 11.791 26 12.4833V1.625C26 1.19402 25.8288 0.780698 25.524 0.475951C25.2193 0.171205 24.806 0 24.375 0L1.625 0Z" fill="#FFD700" />
							<path d="M16.2468 8.2599L16.25 8.12502C16.2497 7.56536 16.1048 7.01526 15.8295 6.52802C15.5541 6.04077 15.1576 5.63288 14.6784 5.34386C14.1991 5.05484 13.6533 4.89448 13.0939 4.87831C12.5345 4.86214 11.9803 4.99071 11.4852 5.25157C10.99 5.51242 10.5706 5.89673 10.2676 6.36726C9.96458 6.83779 9.7882 7.37859 9.75554 7.9373C9.72288 8.49601 9.83505 9.05369 10.0812 9.55632C10.3273 10.059 10.6991 10.4895 11.1605 10.8063C12.5651 9.47081 14.3359 8.58428 16.2468 8.2599Z" fill="#FFD700" />
						</g>
						<defs>
							<clipPath id="clip0_2924_7435">
								<rect width="26" height="26" fill="white" />
							</clipPath>
						</defs>
					</svg>
					<p class="leading-[1.2] text-[16px] xl:text-[30px]">
						<span class="font-[700]"><?php esc_html_e('Депозит:', 'm-rent'); ?></span>
						<?php echo esc_html(mrent_byn_price($mrent_deposit)); ?>
					</p>
				</div>
			<?php endif; ?>

			<a href="<?php echo esc_url($mrent_booking_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center w-full xl:w-[300px] h-[55px] xl:h-[65px] rounded-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] transition-colors">
				<?php esc_html_e('Забронировать', 'm-rent'); ?>
			</a>
		</div>

	</div>
</div>