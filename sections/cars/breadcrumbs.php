<?php
/**
 * Хлебные крошки раздела «Автопарк».
 *
 * Уровни строятся по queried object:
 *   • archive-car             → Главная > Все автомобили
 *   • taxonomy-car_category   → Главная > Все автомобили > {Категория}
 *   • single-car              → Главная > Все автомобили > {Название модели}
 *
 * Последний элемент рисуется жёлтым (Demi), без ссылки. Между — chevron 9×18.
 *
 * Figma: 388:356 (archive), 392:637 (single).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_crumbs = [
	[ 'label' => __( 'Главная', 'm-rent' ), 'url' => home_url( '/' ) ],
	[ 'label' => __( 'Все автомобили', 'm-rent' ), 'url' => get_post_type_archive_link( 'car' ) ?: home_url( '/cars/' ) ],
];

if ( is_tax( 'car_category' ) ) {
	$mrent_crumbs[] = [ 'label' => single_term_title( '', false ), 'url' => '' ];
} elseif ( is_singular( 'car' ) ) {
	$mrent_crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];
}

// Последний элемент всегда без ссылки — это текущая страница.
$mrent_crumbs[ count( $mrent_crumbs ) - 1 ]['url'] = '';
?>

<nav class="bg-mrent-black pt-[20px] xl:pt-[40px]" aria-label="<?php esc_attr_e( 'Хлебные крошки', 'm-rent' ); ?>">
	<div class="px-[15px] xl:px-[100px]">
		<ol class="max-w-[1720px] mx-auto flex flex-wrap items-center gap-[10px] text-[16px] xl:text-[24px] leading-[1.2]">
			<?php foreach ( $mrent_crumbs as $i => $crumb ) :
				$is_last = ( $crumb['url'] === '' );
			?>
				<li class="flex items-center gap-[10px]">
					<?php if ( $is_last ) : ?>
						<span class="text-mrent-yellow font-[600]"><?php echo esc_html( $crumb['label'] ); ?></span>
					<?php else : ?>
						<a href="<?php echo esc_url( $crumb['url'] ); ?>" class="text-mrent-white hover:text-mrent-yellow transition-colors font-[400]">
							<?php echo esc_html( $crumb['label'] ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $i < count( $mrent_crumbs ) - 1 ) : ?>
						<?php /* chevron 9×18 — тонкая стрелка вправо (Figma 388:358 weui:arrow-outlined) */ ?>
						<svg class="shrink-0 w-[7px] h-[14px] xl:w-[9px] xl:h-[18px] text-mrent-white/70" viewBox="0 0 9 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="1 1 8 9 1 17"/>
						</svg>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</nav>
