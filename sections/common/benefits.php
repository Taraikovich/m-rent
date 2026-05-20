<?php

/**
 * Общая секция «Преимущества» — 6 пунктов с иконками вокруг центрального фото.
 *
 * Используется на главной (`sections/home/benefits.php`) и на «О нас»
 * (`sections/about/benefits.php`). Эти файлы — тонкие обёртки: достают
 * нужный ACF-репитер и фото, и передают сюда через args:
 *
 *     get_template_part( 'sections/common/benefits', null, [
 *         'benefits' => [ ['benefit_icon'=>..., 'benefit_title'=>..., 'benefit_description'=>...], ...6 ],
 *         'image'    => [ 'url' => ..., 'alt' => ... ],
 *     ] );
 *
 * Если `benefits` пустой — секция не выводится.
 *
 * Иконки — SVG/PNG из медиатеки. SVG-MIME включён в inc/svg.php.
 *
 * Раскладка одна, адаптив через `xl:`-варианты:
 *   • < xl  — стек: 3 пункта → фото → 3 пункта (всё icon-left, text-left).
 *   • ≥ xl  — три колонки 548/458/598px по Figma 2084:1824. Гэпы 58px возникают
 *              сами через `justify-between` в контейнере 1720px. Фото центрируется
 *              по вертикали. Левые items — icon|text, правые — text|icon
 *              (через flex-row-reverse), текст справа right-aligned.
 *
 * Figma главная: 2084:1824 (desktop), 2317:3383 (mobile).
 * Figma «О нас»:  2169:2101 (desktop).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_benefits = isset($args['benefits']) && is_array($args['benefits']) ? $args['benefits'] : [];

if (! $mrent_benefits) {
	return;
}

$mrent_image     = isset($args['image']) && is_array($args['image']) ? $args['image'] : [];
$mrent_image_url = $mrent_image['url'] ?? '';
$mrent_image_alt = $mrent_image['alt'] ?? '';

$mrent_left  = array_slice($mrent_benefits, 0, 3);
$mrent_right = array_slice($mrent_benefits, 3, 3);
?>

<section class="bg-mrent-black px-3.75 py-15 xl:px-25 xl:py-25">
	<div class="max-w-[1720px] mx-auto flex flex-col items-center gap-7.5 xl:flex-row xl:items-stretch xl:justify-center xl:gap-[clamp(20px,3vw,58px)]">

		<?php /* Левая колонка. На мобайле — w-full стек, на десктопе — фикс 548px по Figma. */ ?>
		<div class="w-full xl:w-[clamp(336px,26.3vw,548px)] flex flex-col gap-7.5 xl:gap-12.5 xl:items-start xl:shrink-0">
			<?php foreach ($mrent_left as $b) : ?>
				<div class="flex items-start gap-3.75 xl:gap-7.5">
					<img src="<?php echo esc_url($b['benefit_icon']['url'] ?? ''); ?>" alt="<?php echo esc_attr($b['benefit_icon']['alt'] ?? ''); ?>" class="size-7.5 xl:size-12.5 shrink-0" loading="lazy" decoding="async" aria-hidden="true">
					<div class="flex-1 min-w-0 flex flex-col gap-3.75 xl:gap-5 text-white leading-[1.2]">
						<p class="font-display font-extrabold text-xl xl:text-[clamp(16px,1.68vw,24px)]"><?php echo esc_html($b['benefit_title']); ?></p>
						<p class="text-sm xl:text-[clamp(14px,1.15vw,18px)]"><?php echo esc_html($b['benefit_description']); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php /* Центральное фото — 244×229 на мобайле, 458×419 на десктопе.
		         object-contain: картинка целиком вписывается в бокс, без обрезки. */ ?>
		<div class="w-61 h-57.25 xl:w-[clamp(400px,21.97vw,458px)] xl:h-auto shrink-0">
			<img src="<?php echo esc_url($mrent_image_url); ?>" alt="<?php echo esc_attr($mrent_image_alt); ?>" class="block w-full h-full object-contain xl:object-cover" loading="lazy" decoding="async">
		</div>

		<?php /* Правая колонка — фикс 598px по Figma. Items right-aligned, icon идёт справа от текста (через flex-row-reverse). */ ?>
		<div class="w-full xl:w-[clamp(367px,28.7vw,598px)] flex flex-col gap-7.5 xl:gap-12.5 xl:items-end xl:shrink-0">
			<?php foreach ($mrent_right as $b) : ?>
				<div class="flex items-start gap-3.75 xl:gap-7.5 xl:flex-row-reverse">
					<img src="<?php echo esc_url($b['benefit_icon']['url'] ?? ''); ?>" alt="<?php echo esc_attr($b['benefit_icon']['alt'] ?? ''); ?>" class="size-7.5 xl:size-12.5 shrink-0" loading="lazy" decoding="async" aria-hidden="true">
					<div class="flex-1 min-w-0 flex flex-col gap-3.75 xl:gap-5 text-white leading-[1.2] xl:items-end xl:text-right">
						<p class="font-display font-extrabold text-xl xl:text-[clamp(16px,1.68vw,24px)]"><?php echo esc_html($b['benefit_title']); ?></p>
						<p class="text-sm xl:text-[clamp(14px,1.15vw,18px)]"><?php echo esc_html($b['benefit_description']); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>