<?php
/**
 * Универсальный компонент хлебных крошек.
 *
 * Крошки передаются через третий аргумент get_template_part():
 *   get_template_part( 'sections/common/breadcrumbs', null, [ 'crumbs' => [
 *       [ 'label' => '…', 'url' => '…' ],   // ссылка
 *       [ 'label' => '…', 'url' => '' ],    // текущая страница (жёлтая, без ссылки)
 *   ] ] );
 *
 * Без аргумента 'crumbs' строит «Главная > {заголовок текущей записи}».
 * Последний элемент всегда рисуется без ссылки независимо от переданного url.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_crumbs = ( isset( $args['crumbs'] ) && is_array( $args['crumbs'] ) && $args['crumbs'] )
	? array_values( $args['crumbs'] )
	: [
		[ 'label' => __( 'Главная', 'm-rent' ), 'url' => home_url( '/' ) ],
		[ 'label' => get_the_title(), 'url' => '' ],
	];

// Последний элемент всегда без ссылки — это текущая страница.
$mrent_crumbs[ array_key_last( $mrent_crumbs ) ]['url'] = '';
?>

<nav class="bg-mrent-black pt-[20px] xl:pt-[40px]" aria-label="<?php esc_attr_e( 'Хлебные крошки', 'm-rent' ); ?>">
	<div class="px-[15px] xl:px-[100px]">
		<ol class="max-w-[1720px] mx-auto flex flex-wrap items-center gap-[10px] text-[clamp(14px,13.03px+0.26vw,18px)] leading-[1.2]">
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
						<svg class="shrink-0 w-[7px] h-[14px] xl:w-[9px] xl:h-[18px] text-mrent-white/70" viewBox="0 0 9 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="1 1 8 9 1 17"/>
						</svg>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</nav>
