<?php

/**
 * Карточка услуги в архиве.
 *
 * Используется внутри grid.php в `while ( $query->have_posts() )` — берёт
 * текущий пост из глобального цикла.
 *
 * Дизайн (Figma 2218:3173 / 2535:5627):
 *   • Десктоп: высота 450px, padding 50px, заголовок 35px (Heavy), ссылка 30px.
 *              Высота и padding на xl НЕ фиксированные: grid.php прокидывает
 *              `card_width` (дизайн-ширину карточки в ряду при контейнере 1720px),
 *              а тут aspect-ratio = card_width/450 тянет высоту от текущей ширины,
 *              padding = 5000%/card_width — пропорционально (% padding'а считается
 *              от ширины элемента). Так карточка масштабируется целиком при
 *              сужении xl-вьюпорта, а не остаётся в фикс. 450px.
 *   • Мобайл:  высота 233px, padding 25px, заголовок 20px, ссылка 18px.
 *   • Фон — картинка с overlay rgba(30,29,31,0.8), бордер #302F31, rounded-15.
 *   • Текст-блок — сверху-слева (НЕ снизу). Заголовок ограничен ~50% ширины
 *     карточки → переносится на 2 строки, как в макете.
 */

if (! defined('ABSPATH')) {
	exit;
}

global $post;

$mrent_permalink = get_permalink();

// «Дизайн-ширина» карточки в ряду (из grid.php). Кладём в CSS-переменную
// --mrent-cw: по ней xl-классы строят aspect-ratio и пропорциональный padding.
$mrent_card_width = isset($args['card_width']) ? (float) $args['card_width'] : 0;
$mrent_card_style = ($mrent_card_width > 0) ? '--mrent-cw:' . $mrent_card_width . ';' : '';

// Картинка карточки: ACF-поле, иначе — featured image.
$mrent_image_field = get_field('service_card_image');
$mrent_image_url   = '';
$mrent_image_alt   = get_the_title();

if (is_array($mrent_image_field) && ! empty($mrent_image_field['url'])) {
	$mrent_image_url = $mrent_image_field['url'];
	if (! empty($mrent_image_field['alt'])) {
		$mrent_image_alt = $mrent_image_field['alt'];
	}
} else {
	$mrent_image_url = get_the_post_thumbnail_url($post, 'large') ?: '';
}
?>

<a
	href="<?php echo esc_url($mrent_permalink); ?>"
	style="<?php echo esc_attr($mrent_card_style); ?>"
	class="group relative flex flex-col items-start h-[233px] p-[25px] w-full xl:h-auto xl:aspect-[calc(var(--mrent-cw,415)/450)] xl:p-[calc(5000%/var(--mrent-cw,415))] rounded-[15px] border border-[#302f31] overflow-hidden bg-[#252426]">
	<?php if ($mrent_image_url) : ?>
		<img
			src="<?php echo esc_url($mrent_image_url); ?>"
			alt="<?php echo esc_attr($mrent_image_alt); ?>"
			class="absolute inset-0 size-full object-cover"
			loading="lazy"
			decoding="async">
	<?php endif; ?>
	<?php /* Тёмный overlay по дизайну (rgba(30,29,31,0.8)). На hover ослабляем,
	         чтобы фотография немного «проявилась». */ ?>
	<span class="absolute inset-0 bg-mrent-black/80 transition-colors group-hover:bg-mrent-black/70" aria-hidden="true"></span>

	<?php /* max-w-[55%] на текстовом блоке — переносит заголовок на 2 строки,
	         как в макете (Figma: title `w-[144..159px]` при card 330px = ~45-50%,
	         desktop title `w-[215..295px]` при card 415..560px = 38-50%). */ ?>
	<div class="relative flex flex-col gap-[15px] xl:gap-[30px] items-start max-w-[85%]">
		<h3 class="text-mrent-white font-[800] text-[clamp(16px,14.06px+0.52vw,24px)] leading-[1.2]">
			<?php the_title(); ?>
		</h3>
		<span class="text-mrent-white font-[500] text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.2] underline whitespace-nowrap group-hover:text-mrent-yellow transition-colors">
			<?php esc_html_e('Подробнее', 'm-rent'); ?>
		</span>
	</div>
</a>