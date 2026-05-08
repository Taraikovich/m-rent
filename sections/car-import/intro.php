<?php
/**
 * «Авто под заказ»: заголовок + лид.
 *
 * Figma:
 *   • desktop 2989:7222 — H1 Futura PT Bold 74 / Book 24, gap-30, текст white.
 *   • mobile  3004:9337 — H1 28 / 16, gap-15.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_title = (string) get_field( 'car_import_intro_title' );
$mrent_lead  = (string) get_field( 'car_import_intro_lead' );

if ( $mrent_title === '' && $mrent_lead === '' ) {
	return;
}
?>

<section class="bg-mrent-black px-3.75 xl:px-25 pt-7.5 xl:pt-15">
	<div class="max-w-430 mx-auto flex flex-col gap-3.75 xl:gap-7.5 text-mrent-white leading-[1.2]">
		<?php if ( $mrent_title !== '' ) : ?>
			<h1 class="font-display font-bold text-[28px] xl:text-[74px]">
				<?php echo esc_html( $mrent_title ); ?>
			</h1>
		<?php endif; ?>
		<?php if ( $mrent_lead !== '' ) : ?>
			<p class="font-normal text-base xl:text-2xl">
				<?php echo esc_html( $mrent_lead ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>
