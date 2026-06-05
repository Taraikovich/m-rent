<?php

/**
 * Сетка карточек автомобилей.
 *
 * Берёт `have_posts()` из текущего main query (CPT-archive или taxonomy-archive),
 * рендерит каждую через sections/cars/card.php.
 *
 * Раскладка:
 *   • <xl  — 2 колонки (Figma 2326:3532, gap 10px, 360px viewport — две карточки 160px).
 *   • xl+  — 4 колонки (Figma 390:385, gap 29px, контейнер 1720px). На очень узких
 *            экранах между sm и xl используется 3 колонки для более мягкого перехода.
 */

if (! defined('ABSPATH')) {
	exit;
}
?>

<?php /* Двухуровневая обёртка:
         • внешний div держит боковые отступы (15px на мобайле, 100px на xl+) —
           это «воздух» между сеткой и краем вьюпорта на любых ширинах;
         • внутренний — max-w-[1720px] и mx-auto, чтобы сама сетка карточек
           растягивалась до 1720px (по дизайну 390:385: 4×408 + 3×29 = 1719).
         Раньше padding жил внутри max-w-обёртки и съедал у сетки 200px,
         поэтому карточки никогда не доходили до 1720. */ ?>
<section class="bg-mrent-black pt-[30px] xl:pt-[50px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto">
			<?php if (have_posts()) : ?>
				<div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-[30px] xl:gap-[29px]" data-mrent-reveal>
					<?php while (have_posts()) :
						the_post();
						get_template_part('sections/cars/card');
					endwhile; ?>
				</div>

				<?php
				$mrent_pagination = paginate_links([
					'mid_size'  => 1,
					'prev_text' => __('← Назад', 'm-rent'),
					'next_text' => __('Вперёд →', 'm-rent'),
					'type'      => 'array',
				]);
				if ($mrent_pagination) : ?>
					<nav class="mt-[40px] xl:mt-[60px] flex flex-wrap gap-[10px] justify-center text-mrent-white">
						<?php foreach ($mrent_pagination as $link) : ?>
							<span class="[&_a]:px-3 [&_a]:py-2 [&_a]:rounded-md [&_a]:bg-[#252426] [&_a:hover]:bg-[#2f2e30] [&_.current]:px-3 [&_.current]:py-2 [&_.current]:rounded-md [&_.current]:bg-mrent-yellow [&_.current]:text-mrent-black"><?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — paginate_links уже экранирует 
																																																													?></span>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>
			<?php else : ?>
				<p class="text-mrent-white text-center text-[18px] py-[80px]">
					<?php esc_html_e('В этой категории пока нет автомобилей.', 'm-rent'); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>