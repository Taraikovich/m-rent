<?php

/**
 * Карточка автомобиля в каталоге.
 *
 * Используется внутри grid.php в `while ( have_posts() )` — берёт текущий пост
 * из глобального цикла, ничего из аргументов не принимает.
 *
 * Десктоп (390:386): ширина 408px, фото 300px, подпись 26px, цена 30px,
 *                   две кнопки в ряд по 60px.
 * Мобайл  (2326:3641): ширина 160px, фото 130–300px (5:7), название 16px, цена 16px,
 *                     две кнопки одна под другой по 55px.
 */

if (! defined('ABSPATH')) {
	exit;
}

global $post;

$mrent_price       = (float) get_field('car_price_per_day');
$mrent_booking_url = get_field('car_booking_url') ?: '#booking';
$mrent_permalink   = get_permalink();
$mrent_thumb_url   = get_the_post_thumbnail_url($post, 'large');

if (! $mrent_thumb_url) {
	// Fallback: первое фото из галереи, если featured не установлен.
	$mrent_gallery = (array) get_field('car_gallery');
	if (! empty($mrent_gallery[0]['url'])) {
		$mrent_thumb_url = $mrent_gallery[0]['url'];
	}
}

$mrent_hover_image = get_field('car_hover_image');
$mrent_hover_url   = is_array($mrent_hover_image) && ! empty($mrent_hover_image['url']) ? $mrent_hover_image['url'] : '';
?>

<?php /* h-full + flex-col, чтобы карточки внутри grid-row растягивались до общей
         высоты, а блок «цена + кнопки» прижимался к низу через mt-auto.
         Тогда длина названия больше не двигает кнопки и цену по вертикали. */ ?>
<article class="flex flex-col gap-[15px] xl:gap-[30px] items-stretch w-full h-full">
	<?php /* Мобайл: высота фото тянется за шириной карточки (соотношение 5:7),
	         но зажата в диапазон 130–300px. На xl+ — фиксированные 300px. */ ?>
	<a href="<?php echo esc_url($mrent_permalink); ?>" class="group block relative rounded-[15px] overflow-hidden aspect-[5/7] min-h-[130px] max-h-[300px] xl:aspect-auto xl:h-[300px] xl:min-h-0 xl:max-h-none bg-[#252426] shrink-0">
		<?php if ($mrent_thumb_url) : ?>
			<img
				src="<?php echo esc_url($mrent_thumb_url); ?>"
				alt="<?php echo esc_attr(get_the_title()); ?>"
				class="absolute inset-0 size-full object-cover transition-opacity duration-300<?php echo $mrent_hover_url ? ' group-hover:opacity-0' : ''; ?>"
				loading="lazy"
				decoding="async">
		<?php endif; ?>
		<?php if ($mrent_hover_url) : ?>
			<img
				src="<?php echo esc_url($mrent_hover_url); ?>"
				alt=""
				aria-hidden="true"
				class="absolute inset-0 size-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-300"
				loading="lazy"
				decoding="async">
		<?php endif; ?>
	</a>

	<div class="flex flex-col gap-[15px] xl:gap-[30px] items-start w-full flex-1">
		<?php /* Название показываем и на мобайле (16px), и на xl+ (26px Bold, Figma 390:386). */ ?>
		<h3 class="block w-full font-[700] text-[clamp(12px,9.09px+0.78vw,24px)] leading-[1.2] text-mrent-white">
			<a href="<?php echo esc_url($mrent_permalink); ?>" class="hover:text-mrent-yellow transition-colors">
				<?php the_title(); ?>
			</a>
		</h3>

		<?php /* Прайс + кнопки — единый блок, прилипший к низу карточки.
		         mt-auto активен на всех брейкпойнтах: название теперь есть и на мобайле,
		         так что разная длина заголовка не двигает цену и кнопки по вертикали. */ ?>
		<div class="flex flex-col gap-[15px] xl:gap-[30px] items-start w-full mt-auto">
			<p class="w-full font-[400] text-[clamp(12px,11.27px+0.19vw,15px)] leading-[1.2] text-mrent-white">
				<?php echo esc_html(mrent_byn_price($mrent_price, '/' . __('сутки', 'm-rent'))); ?>
			</p>

			<?php /* Мобайл (2326:3645): кнопки колонкой, каждая w-full + shrink-0 + h-55,
			         текст whitespace-nowrap. Десктоп (199:117): в ряд, flex-1 делит ширину.
			         Использовать flex-1 на мобайле опасно — в flex-col оно конфликтует с
			         фиксированной h-55 при растянутом родителе (например, через mt-auto). */ ?>
			<div class="flex flex-col xl:flex-row gap-[10px] items-stretch w-full">
				<a href="<?php echo esc_url($mrent_booking_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] w-full xl:w-auto xl:flex-1 shrink-0 h-[55px] xl:h-[60px] flex items-center justify-center rounded-[15px] text-mrent-black font-[500] text-[clamp(12px,10.54px+0.39vw,18px)] whitespace-nowrap transition-colors">
					<?php esc_html_e('Забронировать', 'm-rent'); ?>
				</a>
				<a href="<?php echo esc_url($mrent_permalink); ?>" class="border border-mrent-white hover:bg-mrent-white/10 w-full xl:w-auto xl:flex-1 shrink-0 h-[55px] xl:h-[60px] flex items-center justify-center rounded-[15px] text-mrent-white font-[500] text-[clamp(12px,10.54px+0.39vw,18px)] whitespace-nowrap transition-colors">
					<?php esc_html_e('Подробнее', 'm-rent'); ?>
				</a>
			</div>
		</div>
	</div>
</article>