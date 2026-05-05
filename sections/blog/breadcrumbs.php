<?php
/**
 * Хлебные крошки раздела «Блог».
 *
 *   • home (страница записей) / archive  → Главная > Блог
 *   • category / tag                     → Главная > Блог > {терм}
 *   • single                             → Главная > Блог > {заголовок}
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_blog_url = '';
$mrent_posts_page_id = (int) get_option( 'page_for_posts' );
if ( $mrent_posts_page_id > 0 ) {
	$mrent_blog_url = get_permalink( $mrent_posts_page_id );
}
if ( ! $mrent_blog_url ) {
	$mrent_blog_url = home_url( '/blog/' );
}

$mrent_crumbs = [
	[ 'label' => __( 'Главная', 'm-rent' ), 'url' => home_url( '/' ) ],
	[ 'label' => __( 'Блог', 'm-rent' ), 'url' => $mrent_blog_url ],
];

if ( is_category() || is_tag() || is_tax() ) {
	$mrent_crumbs[] = [ 'label' => single_term_title( '', false ), 'url' => '' ];
} elseif ( is_singular( 'post' ) ) {
	$mrent_crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];
}

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
						<svg class="shrink-0 w-[7px] h-[14px] xl:w-[9px] xl:h-[18px] text-mrent-white/70" viewBox="0 0 9 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="1 1 8 9 1 17"/>
						</svg>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</nav>
