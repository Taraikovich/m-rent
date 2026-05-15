<?php

/**
 * Главная: секция «Клубные карты».
 *
 * Контент: ACF-группа «Главная», таб «Клубные карты».
 * Если поле «Заголовок» (home_club_title) не заполнено — секция не выводится.
 *
 * Один DOM, адаптив через `2xl:`-варианты (Figma 334:302 desktop / 2324:2839 mobile):
 *   • < 2xl  — flex-col gap-30: текст-блок (заголовок 28px Bold + лид 16px Book,
 *              gap-15 внутри) → жёлтая ссылка 18px Medium underline → две карточки
 *              в относительном контейнере 330×214 со «лестничным» сдвигом
 *              (gold сверху-справа, silver снизу-слева).
 *   • ≥ 2xl  — flex-row justify-between items-center: слева текст-колонка
 *              (заголовок 74px + лид 30px max-w-369; gap-30 внутри текст-блока,
 *              gap-50 до жёлтой ссылки 30px Medium underline); справа контейнер
 *              801×398 с теми же двумя карточками 430×257 в «лестнице».
 *
 * Карточки — ACF home_club_image_1 (gold/верхняя) и home_club_image_2
 * (silver/нижняя, рендерится после gold → перекрывает её на пересечении).
 * Если поля пустые — fallback на assets/images/club-card-{gold,silver}.png.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field('home_club_title', $mrent_page_id);

if ($mrent_title === '') {
	return;
}

$mrent_lead      = (string) get_field('home_club_lead', $mrent_page_id);
$mrent_link_text = (string) get_field('home_club_link_text', $mrent_page_id);
$mrent_link_url  = (string) get_field('home_club_link_url', $mrent_page_id);

$mrent_dir = get_template_directory_uri() . '/assets/images';

$mrent_image_1     = get_field('home_club_image_1', $mrent_page_id);
$mrent_image_1_url = $mrent_image_1['url'] ?? $mrent_dir . '/club-card-gold.png';
$mrent_image_1_alt = $mrent_image_1['alt'] ?? '';

$mrent_image_2     = get_field('home_club_image_2', $mrent_page_id);
$mrent_image_2_url = $mrent_image_2['url'] ?? $mrent_dir . '/club-card-silver.png';
$mrent_image_2_alt = $mrent_image_2['alt'] ?? '';
?>

<section class="bg-mrent-black px-3.75 py-15 2xl:px-25 2xl:py-25">
	<div class="max-w-430 mx-auto flex flex-col gap-7.5 2xl:flex-row 2xl:items-center 2xl:justify-between 2xl:gap-10">

		<?php /* Текстовая колонка. На мобайле — gap-30 между текст-блоком и ссылкой,
		         внутри текст-блока gap-15. На ≥2xl — gap-50 между текст-блоком и
		         ссылкой, внутри текст-блока gap-30 (Figma 329:294/295). */ ?>
		<div class="flex flex-col gap-7.5 2xl:gap-12.5 2xl:shrink-0">
			<div class="flex flex-col gap-3.75 2xl:gap-7.5 text-mrent-white leading-[1.2]">
				<h2 class="font-display font-bold text-[28px] 2xl:text-[74px]">
					<?php echo esc_html($mrent_title); ?>
				</h2>
				<?php if ($mrent_lead) : ?>
					<p class="text-base 2xl:text-3xl 2xl:max-w-[369px]">
						<?php echo esc_html($mrent_lead); ?>
					</p>
				<?php endif; ?>
			</div>

			<?php if ($mrent_link_text) : ?>
				<a href="<?php echo esc_url($mrent_link_url ?: '#'); ?>" class="self-start font-display font-medium text-mrent-yellow text-lg 2xl:text-3xl underline underline-offset-2 hover:opacity-80 transition-opacity">
					<?php echo esc_html($mrent_link_text); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php /* Контейнер карточек.
		         • Mobile (<2xl): w-full с aspect-[330/214] — занимает всю ширину
		           секции (как фото в about/delivery), карточки масштабируются
		           вместе с ним. Сдвиги/размеры — в %, посчитаны из Figma 2324:2839
		           (ml-110/330=33.33%, mt-83/214=38.785%, w-220/330=66.67%,
		           аспект карточки 220/131).
		         • ≥2xl: фикс 801×398 c пиксельными офсетами 371/141 и карточками
		           430×257 (Figma 334:302), `aspect-auto` сбрасывает мобильное
		           aspect-ratio. DOM-порядок gold → silver — silver сверху
		           в зоне пересечения. */ ?>
		<div class="relative w-full aspect-[330/214] 2xl:flex-1 2xl:min-w-0 2xl:max-w-[801px] 2xl:aspect-[801/398]">
			<img
				src="<?php echo esc_url($mrent_image_1_url); ?>"
				alt="<?php echo esc_attr($mrent_image_1_alt); ?>"
				class="absolute top-0 left-1/3 block w-2/3 aspect-[220/131] object-contain 2xl:left-[46.317%] 2xl:w-[53.683%] 2xl:aspect-[430/257]"
				loading="lazy"
				decoding="async">
			<img
				src="<?php echo esc_url($mrent_image_2_url); ?>"
				alt="<?php echo esc_attr($mrent_image_2_alt); ?>"
				class="absolute top-[38.785%] left-0 block w-2/3 aspect-[220/131] object-contain 2xl:top-[35.427%] 2xl:w-[53.683%] 2xl:aspect-[430/257]"
				loading="lazy"
				decoding="async">
		</div>

	</div>
</section>
