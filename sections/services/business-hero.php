<?php
/**
 * Hero — «Аренда для юридических лиц» (Figma 2809:7253 desktop / 2846:7009 mobile).
 *
 * Раскладка:
 *   Десктоп (1920px) — фрейм 2809:7253, h=870:
 *     • Секция h-[870px], картинка absolute inset-0
 *     • Градиент: transparent 49% → rgba(30,29,31,0.8) 64% → #1E1D1F 100%
 *     • Текст+кнопка absolute bottom-0, max-w-[1316px] centered, flex-col gap-[50px]
 *       ∟ h1 74px / subtitle 30px / gap-[20px] между ними
 *       ∟ Кнопка 300×65, text 20px
 *
 *   Мобайл (360px) — фрейм 2846:7009, h=603:
 *     • Секция h-[603px] (450 картинка + 153 чёрная область)
 *     • Картинка absolute top-0 h-[450px]
 *     • Градиент: transparent 38% → #1E1D1F 81% (без via-стопа)
 *     • Текст+кнопка absolute bottom-0, w-full (=330px с px-[15px]), flex-col gap-[30px]
 *       ∟ h1 28px / subtitle 16px / gap-[15px] между ними
 *       ∟ Кнопка w-full × 55, text 14px
 *
 * ACF (group_mrent_biz_services):
 *   • biz_hero_image — Image; fallback featured image
 *   • biz_subtitle   — Textarea (опц.; если пусто — <p> не выводится)
 *   • biz_cta_text   — Text; default «Выбрать автомобиль»
 *   • biz_cta_url    — Text; default «#contact»
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_subtitle = (string) get_field( 'biz_subtitle' );
$mrent_cta_text = get_field( 'biz_cta_text' );
if ( ! $mrent_cta_text ) {
	$mrent_cta_text = __( 'Выбрать автомобиль', 'm-rent' );
}
$mrent_cta_url = get_field( 'biz_cta_url' ) ?: '#contact';

$mrent_image_url = '';
$mrent_hero_img  = get_field( 'biz_hero_image' );
if ( is_array( $mrent_hero_img ) && ! empty( $mrent_hero_img['url'] ) ) {
	$mrent_image_url = $mrent_hero_img['url'];
}
if ( $mrent_image_url === '' ) {
	$mrent_image_url = get_the_post_thumbnail_url( null, 'full' ) ?: '';
}
?>

<section class="relative bg-mrent-black h-[603px] xl:h-[870px]">

	<?php /* Картинка.
	         Мобайл: h-[450px], сверху — ниже идёт чёрная область секции.
	         Десктоп: h-full — заполняет всю секцию (870px). */ ?>
	<div class="absolute inset-x-0 top-0 h-[450px] xl:h-full overflow-hidden">
		<?php if ( $mrent_image_url ) : ?>
			<img
				src="<?php echo esc_url( $mrent_image_url ); ?>"
				alt="<?php echo esc_attr( get_the_title() ); ?>"
				class="absolute inset-0 size-full object-cover"
				loading="eager"
				decoding="async"
			>
		<?php endif; ?>
		<?php /* Мобайл: transparent 38% → #1E1D1F 81% (Figma 2846:7010).
		         Десктоп: transparent 49% → rgba(30,29,31,0.8) 64% → #1E1D1F 100% (Figma 2809:7254). */ ?>
		<span
			class="absolute inset-0 bg-gradient-to-b from-transparent from-[38%] to-mrent-black to-[81%] xl:from-[49%] xl:via-[rgba(30,29,31,0.8)] xl:via-[64%] xl:to-[100%]"
			aria-hidden="true"
		></span>
	</div>

	<?php /* Текст + кнопка.
	         absolute bottom-0: нижний край блока = нижний край секции.
	         Высота блока определяется контентом, поэтому на обоих брейкпоинтах
	         верхний край встаёт ровно туда, куда нужно:
	           мобайл  603 − 320 (235 текст + 30 gap + 55 кнопка) = 283px ✓
	           десктоп 870 − 368 (253 текст + 50 gap + 65 кнопка) = 502px ✓ */ ?>
	<div class="absolute bottom-0 inset-x-0 px-[15px] xl:px-0">
		<div class="flex flex-col gap-[30px] xl:gap-[50px] items-center w-full xl:max-w-[1316px] xl:mx-auto">

			<div class="flex flex-col gap-[15px] xl:gap-[20px] items-center text-center w-full">
				<h1 class="text-mrent-white font-[700] text-[28px] xl:text-[74px] leading-[1.2] w-full">
					<?php the_title(); ?>
				</h1>
				<?php if ( $mrent_subtitle !== '' ) : ?>
					<p class="text-mrent-white font-[400] text-[16px] xl:text-[30px] leading-[1.2] w-full">
						<?php echo esc_html( $mrent_subtitle ); ?>
					</p>
				<?php endif; ?>
			</div>

			<a
				href="<?php echo esc_url( $mrent_cta_url ); ?>"
				class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full xl:w-[300px] px-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] whitespace-nowrap transition-colors shrink-0"
			>
				<?php echo esc_html( $mrent_cta_text ); ?>
			</a>

		</div>
	</div>

</section>
