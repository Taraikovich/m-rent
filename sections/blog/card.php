<?php
/**
 * Карточка записи в архиве блога (Figma 2147:1871 / 2341:3232).
 *
 * Десктоп:
 *   • Карточка bg #252426, rounded-15, gap-40, pb-40, px-40 (image вылезает за padding через -mx-40).
 *   • Image 553×300, скруглён только сверху.
 *   • Дата 18 (Book, white/50), заголовок 34 (Heavy, white, leading-1.1),
 *     описание 24 (Book) с bg-clip-text градиентом → плавное затухание к низу.
 *   • CTA «Подробнее» 30 (Medium), жёлтый, underline.
 *
 * Мобайл (Figma 2341:3232):
 *   • 330×179 image, gap-30, pb-25, px-25.
 *   • Заголовок 20 (Heavy), описание 14, CTA 18.
 *
 * Используется внутри grid.php — берёт текущий пост из глобального цикла.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$mrent_permalink = get_permalink();
$mrent_thumb_url = get_the_post_thumbnail_url( $post, 'large' ) ?: '';
$mrent_thumb_alt = get_the_title();
$mrent_date      = get_the_date( 'j F Y' );
$mrent_excerpt   = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 28, '…' );
?>

<a
	href="<?php echo esc_url( $mrent_permalink ); ?>"
	class="group bg-[#252426] rounded-[15px] flex flex-col h-full gap-[30px] xl:gap-[40px] pb-[25px] xl:pb-[40px] px-[25px] xl:px-[40px] overflow-hidden"
>
	<?php /* Картинка вылезает за внутренний padding (px-25/40), чтобы прилегать
	         к боковым и верхней кромке карточки. Скругляем только верх. */ ?>
	<div class="-mx-[25px] xl:-mx-[40px] h-[179px] xl:h-[300px] rounded-tl-[15px] rounded-tr-[15px] overflow-hidden bg-[#d9d9d9] shrink-0">
		<?php if ( $mrent_thumb_url ) : ?>
			<img
				src="<?php echo esc_url( $mrent_thumb_url ); ?>"
				alt="<?php echo esc_attr( $mrent_thumb_alt ); ?>"
				class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
				loading="lazy"
				decoding="async"
			>
		<?php endif; ?>
	</div>

	<div class="flex flex-col gap-[15px] xl:gap-[20px] w-full flex-1">
		<span class="text-mrent-white/50 font-[400] text-[16px] xl:text-[18px] leading-[1.2]">
			<?php echo esc_html( $mrent_date ); ?>
		</span>
		<h3 class="text-mrent-white font-[800] text-[20px] xl:text-[34px] leading-[1.1] group-hover:text-mrent-yellow transition-colors">
			<?php the_title(); ?>
		</h3>
		<?php if ( $mrent_excerpt ) : ?>
			<?php /* mrent-text-fade: clip-text градиент белый → прозрачный
			         к низу (Figma from-39.68%). Дополнительно ограничиваем высоту,
			         чтобы карточки в свайпере были одного размера. */ ?>
			<p class="mrent-text-fade font-[400] text-[14px] xl:text-[24px] leading-[1.2] line-clamp-3 xl:line-clamp-4">
				<?php echo esc_html( $mrent_excerpt ); ?>
			</p>
		<?php endif; ?>
	</div>

	<span class="text-mrent-yellow font-[500] text-[18px] xl:text-[30px] underline decoration-solid group-hover:text-[#FFF831] transition-colors">
		<?php esc_html_e( 'Подробнее', 'm-rent' ); ?>
	</span>
</a>
