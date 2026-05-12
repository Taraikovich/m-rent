<?php
/**
 * Секция «Карта» на странице контактов.
 *
 * iframe Яндекс.Карт собирается из координат в ACF Options
 * («Контакты и соцсети» → вкладка «Карта»). Если координаты не заданы —
 * секция не выводится.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$map_src = function_exists( 'mrent_options_map_iframe_src' ) ? mrent_options_map_iframe_src() : '';

if ( $map_src === '' ) {
	return;
}
?>

<section class="bg-mrent-black pt-[40px] xl:pt-[80px] pb-[60px] xl:pb-[120px]" aria-label="<?php esc_attr_e( 'Карта', 'm-rent' ); ?>">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto overflow-hidden rounded-[15px]">
			<iframe
				src="<?php echo esc_url( $map_src ); ?>"
				class="block w-full h-[360px] md:h-[480px] xl:h-[600px] border-0"
				loading="lazy"
				allowfullscreen
				title="<?php esc_attr_e( 'Карта офиса M-Rent на Яндекс.Картах', 'm-rent' ); ?>"
			></iframe>
		</div>
	</div>
</section>
