<?php
/**
 * Hero страницы услуги (Figma 2083:1816 / 2535:5988).
 *
 * Раскладка по макету (точные top/sизы):
 *
 *   Десктоп (1920px wide):
 *     • Картинка 1920×805, full-bleed, top:0
 *     • Градиент → #1E1D1F (60%..91.7%)
 *     • Текст-блок (h1 + p), w:915, абсолютно, top:574 (≈71% высоты), text-center
 *     • Кнопка 300×65, top:805 — прикреплена к нижнему краю картинки и
 *       свешивается под неё на всю свою высоту
 *
 *   Мобайл (360px wide):
 *     • Картинка 360×450, top:0
 *     • Градиент 38%..80.6%
 *     • Текст-блок w-[330px] absolute top:325 (≈72%)
 *     • Кнопка w-full × 55, идёт в потоке после картинки с gap-30 от текста
 *
 * Источники данных:
 *   • картинка — ACF service_card_image, иначе featured image
 *   • подзаголовок — ACF service_subtitle (если пусто, p не выводится)
 *   • CTA-текст — ACF service_cta_text, default «Забронировать услугу»
 *   • CTA-ссылка — ACF service_cta_url, default «#booking» (заглушка)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$mrent_subtitle = (string) get_field( 'service_subtitle' );
$mrent_cta_text = get_field( 'service_cta_text' );
if ( ! $mrent_cta_text ) {
	$mrent_cta_text = __( 'Забронировать услугу', 'm-rent' );
}
$mrent_cta_url = get_field( 'service_cta_url' ) ?: '#booking';

// Источник картинки hero: service_hero_image → service_card_image → featured.
$mrent_image_url = '';
foreach ( [ 'service_hero_image', 'service_card_image' ] as $mrent_field ) {
	$mrent_value = get_field( $mrent_field );
	if ( is_array( $mrent_value ) && ! empty( $mrent_value['url'] ) ) {
		$mrent_image_url = $mrent_value['url'];
		break;
	}
}
if ( $mrent_image_url === '' ) {
	$mrent_image_url = get_the_post_thumbnail_url( $post, 'full' ) ?: '';
}
?>

<section class="relative bg-mrent-black">

	<?php /* Картинка hero. overflow-hidden — клипает <img> по краям блока,
	         но не клипает текст-overlay (он сиблинг этому блоку, а не ребёнок). */ ?>
	<div class="relative w-full h-[450px] xl:h-[805px] overflow-hidden">
		<?php if ( $mrent_image_url ) : ?>
			<img
				src="<?php echo esc_url( $mrent_image_url ); ?>"
				alt="<?php echo esc_attr( get_the_title() ); ?>"
				class="absolute inset-0 size-full object-cover"
				loading="eager"
				decoding="async"
			>
		<?php endif; ?>
		<?php /* Градиент → #1E1D1F. На мобайле начало плавнее (38%), на десктопе
		         позже (60%) — чтобы не съедать центр картинки. */ ?>
		<span class="absolute inset-0 bg-gradient-to-b from-transparent from-[38%] xl:from-[60%] to-mrent-black to-[80%] xl:to-[92%]" aria-hidden="true"></span>
	</div>

	<?php /* Текст-overlay. Абсолютное позиционирование относительно <section>
	         (а не картинки) — чтобы при коротких/длинных подзаголовках текст
	         мог выходить за нижнюю границу картинки на мобайле без клиппинга. */ ?>
	<div class="absolute inset-x-0 top-[325px] xl:top-[574px] px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex justify-center">
			<div class="w-full max-w-[330px] xl:w-[915px] xl:max-w-none flex flex-col gap-[15px] xl:gap-[20px] items-center text-center">
				<h1 class="text-mrent-white font-[700] text-[28px] xl:text-[74px] leading-[1.2] w-full">
					<?php the_title(); ?>
				</h1>
				<?php if ( $mrent_subtitle !== '' ) : ?>
					<p class="text-mrent-white font-[400] text-[16px] xl:text-[30px] leading-[1.2] w-full">
						<?php echo esc_html( $mrent_subtitle ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php /* CTA-кнопка.
	          Мобайл: в потоке после картинки. mt-[30px] от низа картинки —
	            стыкуется с переносом текста (~5px ниже картинки) ≈ gap 25-30 как в макете.
	          Десктоп: mt-0 → начинается ровно от низа картинки (top:805 в макете),
	            и свешивается на свою высоту (65px) ниже неё. */ ?>
	<div class="px-[15px] xl:px-[100px] mt-[30px] xl:mt-0">
		<div class="max-w-[1720px] mx-auto flex justify-center">
			<a
				href="<?php echo esc_url( $mrent_cta_url ); ?>"
				class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[55px] xl:h-[65px] w-full max-w-[330px] xl:w-[300px] xl:max-w-none px-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] whitespace-nowrap transition-colors"
			>
				<?php echo esc_html( $mrent_cta_text ); ?>
			</a>
		</div>
	</div>

	<?php /* Нижний отступ section. На десктопе кнопка свешивается на 65px ниже
	         картинки → дальше нужен ещё небольшой gap до следующей секции. */ ?>
	<div class="h-[40px]"></div>

</section>
