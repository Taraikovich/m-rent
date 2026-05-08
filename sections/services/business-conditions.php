<?php
/**
 * «Аренда для юр. лиц»: секция «Условия аренды» (Figma 2809:7264 desktop / 2846:7047 mobile).
 *
 * Раскладка:
 *   Desktop (xl+, фрейм 1720px): 2-кол row, justify-between.
 *     • Текст-колонка w-[717px] (на ≥1720), gap-[60px]; sub-h2 35px → list 30px, gap-[20px], items gap-[15px].
 *     • Фото справа 854×658, shrink-0, object-bottom.
 *   Mobile: col, текст → фото.
 *     • h2 28px → sub-h2 16px → list 14px, gap-[20px], items gap-[10px].
 *     • Фото h-[254px], во всю ширину контейнера.
 *
 * ACF (group_mrent_biz_services):
 *   • biz_conditions_title    — Text;     default «Условия аренды для юридических лиц»
 *   • biz_conditions_subtitle — Text;     default «Для оформления аренды необходимы:»
 *   • biz_conditions_items    — Repeater; если пусто — fallback из 6 пунктов дизайна
 *   • biz_conditions_image    — Image;    fallback assets/images/business-conditions-bmw.png
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_title = (string) get_field( 'biz_conditions_title' );
if ( $mrent_title === '' ) {
	$mrent_title = __( 'Условия аренды для юридических лиц', 'm-rent' );
}

$mrent_subtitle = (string) get_field( 'biz_conditions_subtitle' );
if ( $mrent_subtitle === '' ) {
	$mrent_subtitle = __( 'Для оформления аренды необходимы:', 'm-rent' );
}

$mrent_items_raw = (array) get_field( 'biz_conditions_items' );
$mrent_items     = [];
foreach ( $mrent_items_raw as $mrent_row ) {
	$mrent_text = (string) ( $mrent_row['text'] ?? '' );
	if ( $mrent_text !== '' ) {
		$mrent_items[] = $mrent_text;
	}
}
if ( ! $mrent_items ) {
	$mrent_items = [
		__( 'реквизиты юридического лица;', 'm-rent' ),
		__( 'свидетельство о регистрации юридического лица;', 'm-rent' ),
		__( 'доверенность на право подписания договоров аренды и актов приема-передачи транспортного средства;', 'm-rent' ),
		__( 'паспорт доверенного лица;', 'm-rent' ),
		__( 'водительское удостоверение категории «B»;', 'm-rent' ),
		__( 'оплата полного срока аренды и залоговой стоимости автомобиля.', 'm-rent' ),
	];
}

$mrent_image_acf = get_field( 'biz_conditions_image' );
$mrent_image_url = '';
$mrent_image_alt = '';
if ( is_array( $mrent_image_acf ) ) {
	if ( ! empty( $mrent_image_acf['url'] ) ) {
		$mrent_image_url = (string) $mrent_image_acf['url'];
	}
	if ( ! empty( $mrent_image_acf['alt'] ) ) {
		$mrent_image_alt = (string) $mrent_image_acf['alt'];
	}
}
if ( $mrent_image_url === '' ) {
	$mrent_image_url = get_template_directory_uri() . '/assets/images/business-conditions-bmw.png';
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[30px] xl:pt-[60px]">
	<div class="max-w-[1720px] mx-auto flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] xl:gap-[100px]">

		<div class="flex flex-col gap-[20px] xl:gap-[60px] xl:w-[717px] xl:shrink-0">
			<h2 class="font-display font-[700] text-[28px] xl:text-[74px] leading-[1.2] text-mrent-white">
				<?php echo esc_html( $mrent_title ); ?>
			</h2>
			<div class="flex flex-col gap-[15px] xl:gap-[20px]">
				<p class="font-display font-[600] text-[16px] xl:text-[35px] leading-[1.2] text-mrent-white">
					<?php echo esc_html( $mrent_subtitle ); ?>
				</p>
				<ul class="flex flex-col gap-[10px] xl:gap-[15px] font-[400] text-[14px] xl:text-[30px] leading-[1.2] text-mrent-white">
					<?php foreach ( $mrent_items as $mrent_item ) : ?>
						<li class="list-disc ms-[21px] xl:ms-[45px]">
							<?php echo esc_html( $mrent_item ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="relative w-full h-[254px] xl:w-[854px] xl:h-[658px] xl:shrink-0">
			<img
				src="<?php echo esc_url( $mrent_image_url ); ?>"
				alt="<?php echo esc_attr( $mrent_image_alt ); ?>"
				class="absolute inset-0 size-full object-bottom object-contain"
				loading="lazy"
				decoding="async"
			>
		</div>

	</div>
</section>
