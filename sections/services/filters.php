<?php
/**
 * Фильтры по категориям услуг.
 *
 * Десктоп (Figma 5095:7367 / 2218:3153): один горизонтальный ряд пилюль —
 * «Все услуги» + все термы. Все пилюли равной ширины (flex-1), активная —
 * белый фон + чёрный текст, остальные — #252426 + белый.
 *
 * Мобайл (Figma 5095:7370 / 2535:5621): нативный <select> в виде «дропдауна»,
 * подменяет собой ряд пилюль. При выборе — переход через querystring `?cat=...`.
 *
 * Активная категория определяется так:
 *   • $_GET['cat'] === slug терма → этот терм
 *   • $_GET['cat'] === 'all' или отсутствует → «Все услуги»
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_terms       = mrent_get_service_categories();
$mrent_archive_url = get_post_type_archive_link( 'service' ) ?: home_url( '/services/' );

$mrent_active_slug = isset( $_GET['cat'] ) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : '';
if ( $mrent_active_slug === 'all' ) {
	$mrent_active_slug = '';
}

// Список «всё + термы» для общей итерации.
$mrent_pills = [];
$mrent_pills[] = [
	'slug'   => '',
	'label'  => __( 'Все услуги', 'm-rent' ),
	'url'    => add_query_arg( 'cat', 'all', $mrent_archive_url ),
];
foreach ( $mrent_terms as $term ) {
	$mrent_pills[] = [
		'slug'  => $term->slug,
		'label' => $term->name,
		'url'   => add_query_arg( 'cat', $term->slug, $mrent_archive_url ),
	];
}
?>

<section class="bg-mrent-black pt-[30px] xl:pt-[50px]">
	<div class="px-[15px] xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto">

			<?php /* Mobile: дропдаун. Превращается в обычный <select> поверх кастомной
			         «пилюли» — нативный пикер для UX, кастомный визуал. Стрелка справа —
			         через background-image SVG, как в Figma 2535:5623. */ ?>
			<div class="xl:hidden relative">
				<label class="block bg-white rounded-[15px] h-[55px] px-[15px] flex items-center justify-between">
					<span class="text-[#1e1d1f] text-[14px] font-[500] truncate pointer-events-none">
						<?php
						$mrent_current = $mrent_pills[0]['label'];
						foreach ( $mrent_pills as $pill ) {
							if ( $pill['slug'] === $mrent_active_slug ) {
								$mrent_current = $pill['label'];
								break;
							}
						}
						echo esc_html( $mrent_current );
						?>
					</span>
					<svg class="shrink-0 w-[10px] h-[10px] text-[#1e1d1f] pointer-events-none" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<polyline points="1 3 5 7 9 3"/>
					</svg>
					<?php /* Нативный <select> рисуется системой. Опции наследуют color от
					         body (у нас color:white) → на белом системном фоне дропдауна
					         текст белый на белом. Жёстко задаём чёрный текст на select и
					         каждом option (через inline style — единственный способ
					         перебить системный пользовательский агент в Safari/Chrome). */ ?>
					<select
						class="absolute inset-0 w-full h-full opacity-0 cursor-pointer text-[#1e1d1f]"
						onchange="if(this.value){window.location.href=this.value}"
						aria-label="<?php esc_attr_e( 'Категория услуг', 'm-rent' ); ?>"
					>
						<?php foreach ( $mrent_pills as $pill ) : ?>
							<option
								value="<?php echo esc_url( $pill['url'] ); ?>"
								<?php selected( $pill['slug'], $mrent_active_slug ); ?>
								style="color:#1e1d1f;background:#ffffff;"
							>
								<?php echo esc_html( $pill['label'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
			</div>

			<?php /* Desktop: ряд пилюль. flex-1 на каждой → равные ширины (Figma 2218:3153). */ ?>
			<div class="hidden xl:flex gap-[20px] items-stretch">
				<?php foreach ( $mrent_pills as $pill ) :
					$is_active = ( $pill['slug'] === $mrent_active_slug );
					$skin = $is_active
						? 'bg-mrent-white text-mrent-black'
						: 'bg-[#252426] text-mrent-white hover:bg-[#2f2e30]';
				?>
					<a
						href="<?php echo esc_url( $pill['url'] ); ?>"
						class="flex-1 min-w-0 h-[65px] flex items-center justify-center rounded-[15px] px-[15px] text-[20px] font-[500] leading-[1.2] text-center whitespace-nowrap transition-colors <?php echo esc_attr( $skin ); ?>"
					>
						<?php echo esc_html( $pill['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
