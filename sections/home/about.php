<?php

/**
 * Главная: секция «Знакомьтесь с нами» (about).
 *
 * Контент: ACF-группа «Главная», таб «О нас».
 * Если поле «Заголовок» (home_about_title) не заполнено — секция не выводится.
 *
 * Один DOM, адаптив через `xl:`-варианты (Figma 2994:9047 desktop / 2324:2801 mobile):
 *   • < xl  — flex-col gap-30: текст-блок (заголовок 28px Bold + лид 16px Book,
 *              gap-15 внутри) → жёлтая ссылка 18px Medium underline → фото снизу
 *              w-full h-auto, rounded-15px, aspect 806/433.
 *   • ≥ xl  — flex-row gap-70 items-center: слева текст-колонка 844px
 *              (gap-60 между текст-блоком и ссылкой; gap-30 между заголовком 74px
 *              и лидом 30px); справа фото 806×433 rounded-15px object-cover.
 *
 * Лид — textarea с nl2br: одиночный перевод строки = `<br>` (без отступа), как в Figma
 * (`mb-0` на первом параграфе). Ссылка и фото — опциональны: рендерятся только при наличии.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field('home_about_title', $mrent_page_id);

if ($mrent_title === '') {
	return;
}

$mrent_lead      = (string) get_field('home_about_lead', $mrent_page_id);
$mrent_link_text = (string) get_field('home_about_link_text', $mrent_page_id);
$mrent_link_url  = (string) get_field('home_about_link_url', $mrent_page_id);

$mrent_image     = get_field('home_about_image', $mrent_page_id);
$mrent_image_url = $mrent_image['url'] ?? '';
$mrent_image_alt = $mrent_image['alt'] ?? '';
?>

<section class="bg-mrent-black px-3.75 py-15 xl:px-25 xl:py-25">
	<div class="max-w-430 mx-auto flex flex-col gap-7.5 xl:flex-row xl:items-center xl:gap-17.5">

		<?php /* Текстовая колонка. На мобайле — w-full, gap-30 между текст-блоком и ссылкой;
		         на ≥xl — фикс w-211 (844px) по Figma 243:717, gap-60 между текст-блоком и ссылкой. */ ?>
		<div class="flex flex-col gap-7.5 xl:gap-[15px] w-full xl:w-1/2 xl:shrink-0">
			<div class="flex flex-col gap-3.75 xl:gap-7.5 text-mrent-white leading-[1.2]">
				<h2 class="font-display font-bold text-[clamp(24px,calc(16.83px+2.98vw),50px)]">
					<?php echo esc_html($mrent_title); ?>
				</h2>
				<?php if ($mrent_lead) : ?>
					<p class="text-[clamp(14px,calc(12.6px+0.91vw),20px)]"><?php echo nl2br(esc_html($mrent_lead)); ?></p>
				<?php endif; ?>
			</div>

			<?php if ($mrent_link_text) : ?>
				<a href="<?php echo esc_url($mrent_link_url ?: '#'); ?>" class="self-start font-display font-medium text-mrent-yellow text-[clamp(14px,calc(15.09px+0.78vw),18px)] underline underline-offset-2 hover:opacity-80 transition-opacity">
					<?php echo esc_html($mrent_link_text); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php /* Фото. На мобайле — w-full с aspect 806/433 (≈ Figma 330×177).
		         На ≥xl — flex-1 + max-w 806px (Figma 2994:9046); аспект 806/433
		         сохраняется, чтобы блок не вылезал за контейнер при недостатке ширины
		         (текст-колонка 844px фиксирована). */ ?>
		<?php if ($mrent_image_url) : ?>
			<div class="w-full aspect-[806/433] xl:w-1/2 xl:aspect-auto xl:flex-1 xl:min-w-0 rounded-[15px] overflow-hidden">
				<img
					src="<?php echo esc_url($mrent_image_url); ?>"
					alt="<?php echo esc_attr($mrent_image_alt); ?>"
					class="block w-full h-full object-cover"
					loading="lazy"
					decoding="async">
			</div>
		<?php endif; ?>

	</div>
</section>