<?php

/**
 * Секция «Преимущества» главной — 6 пунктов с иконками вокруг центрального фото BMW.
 *
 * Контент: ACF-группа «Главная», таб «Преимущества».
 * Если репитер пустой — секция не выводится.
 *
 * Иконки — image-поле `benefit_icon` (SVG из медиатеки). SVG-MIME включён в inc/svg.php.
 *
 * Раскладка одна, адаптив через `2xl:`-варианты:
 *   • < 2xl  — стек: 3 пункта → фото → 3 пункта (всё icon-left, text-left).
 *   • ≥ 2xl  — три колонки 548/458/598px по Figma 2084:1824. Гэпы 58px между
 *              колонками и фото возникают сами через `justify-between` в контейнере 1720px.
 *              Фото центрируется по вертикали (items-center). Левые items — icon|text,
 *              правые items — text|icon (через flex-row-reverse), текст справа right-aligned.
 *
 * Figma: 2084:1824 (desktop) и 2317:3383 (mobile).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id  = get_queried_object_id();
$mrent_benefits = (array) get_field('home_benefits', $mrent_page_id);

if (! $mrent_benefits) {
	return;
}

$mrent_benefits_image = get_field('home_benefits_image', $mrent_page_id);
$mrent_benefits_url   = $mrent_benefits_image['url'] ?? '';
$mrent_benefits_alt   = $mrent_benefits_image['alt'] ?? '';

$mrent_left  = array_slice($mrent_benefits, 0, 3);
$mrent_right = array_slice($mrent_benefits, 3, 3);
?>

<section class="bg-mrent-black px-3.75 py-15 2xl:px-25 2xl:py-25">
	<div class="max-w-430 mx-auto flex flex-col items-center gap-7.5 2xl:flex-row 2xl:items-center 2xl:justify-between 2xl:gap-0">

		<?php /* Левая колонка. На мобайле — w-full стек, на десктопе — фикс 548px по Figma. */ ?>
		<div class="w-full 2xl:w-137 flex flex-col gap-7.5 2xl:gap-12.5 2xl:items-start 2xl:shrink-0">
			<?php foreach ($mrent_left as $b) : ?>
				<div class="flex items-start gap-3.75 2xl:gap-7.5">
					<img src="<?php echo esc_url($b['benefit_icon']['url'] ?? ''); ?>" alt="<?php echo esc_attr($b['benefit_icon']['alt'] ?? ''); ?>" class="size-7.5 2xl:size-12.5 shrink-0" loading="lazy" decoding="async" aria-hidden="true">
					<div class="flex-1 2xl:flex-none min-w-0 flex flex-col gap-3.75 2xl:gap-5 text-white leading-[1.2]">
						<p class="font-display font-extrabold text-xl 2xl:text-[35px] 2xl:whitespace-nowrap"><?php echo esc_html($b['benefit_title']); ?></p>
						<p class="text-sm 2xl:text-2xl 2xl:max-w-117"><?php echo esc_html($b['benefit_description']); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php /* Центральное фото — 244×229 на мобайле, 458×419 на десктопе.
		         Картинка увеличена и сдвинута по Figma 2751:6658 (overflow-hidden + абсолютное позиционирование). */ ?>
		<div class="relative w-61 h-57.25 2xl:w-114.5 2xl:h-104.75 overflow-hidden shrink-0">
			<img src="<?php echo esc_url($mrent_benefits_url); ?>" alt="<?php echo esc_attr($mrent_benefits_alt); ?>" class="absolute h-[112.53%] w-[154.39%] left-[-27.22%] top-[-6.5%] max-w-none" loading="lazy" decoding="async">
		</div>

		<?php /* Правая колонка — фикс 598px по Figma. Items right-aligned, icon идёт справа от текста (через flex-row-reverse). */ ?>
		<div class="w-full 2xl:w-149.5 flex flex-col gap-7.5 2xl:gap-12.5 2xl:items-end 2xl:shrink-0">
			<?php foreach ($mrent_right as $b) : ?>
				<div class="flex items-start gap-3.75 2xl:gap-7.5 2xl:flex-row-reverse">
					<img src="<?php echo esc_url($b['benefit_icon']['url'] ?? ''); ?>" alt="<?php echo esc_attr($b['benefit_icon']['alt'] ?? ''); ?>" class="size-7.5 2xl:size-12.5 shrink-0" loading="lazy" decoding="async" aria-hidden="true">
					<div class="flex-1 2xl:flex-none min-w-0 flex flex-col gap-3.75 2xl:gap-5 text-white leading-[1.2] 2xl:items-end 2xl:text-right">
						<p class="font-display font-extrabold text-xl 2xl:text-[35px] 2xl:whitespace-nowrap"><?php echo esc_html($b['benefit_title']); ?></p>
						<p class="text-sm 2xl:text-2xl 2xl:max-w-129.5"><?php echo esc_html($b['benefit_description']); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
