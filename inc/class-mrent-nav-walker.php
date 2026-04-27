<?php
/**
 * Кастомный walker для главного меню m-rent.
 *
 * Чем отличается от стандартного:
 *   • Добавляет класс `mrent-nav-link` на <a>.
 *   • Если у пункта есть дочерние — после ссылки вставляет кнопку-шеврон
 *     `.mrent-submenu-toggle` для разворачивания подменю (mobile + 2-й уровень desktop).
 *   • Добавляет класс `sub-menu` (стандартный WP класс) — стили берёт CSS.
 *
 * Используется в header.php через wp_nav_menu( [ 'walker' => new MRent_Nav_Walker() ] ).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MRent_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"sub-menu\">\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "{$indent}</ul>\n";
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent       = str_repeat( "\t", $depth );
		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );

		$classes   = empty( $item->classes ) ? [] : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$class_str = join( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );

		$output .= sprintf(
			'%s<li class="menu-item %s" data-mrent-menu-item>',
			$indent,
			esc_attr( $class_str )
		);

		$atts = [
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'class'  => 'mrent-nav-link',
		];

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( $value === '' ) {
				continue;
			}
			$attributes .= sprintf( ' %s="%s"', $attr, esc_attr( $value ) );
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = '<a' . $attributes . '>';
		$item_output .= '<span class="mrent-nav-link-text">' . esc_html( $title ) . '</span>';
		$item_output .= '</a>';
		if ( $has_children ) {
			$item_output .= '<button type="button" class="mrent-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Раскрыть подменю', 'm-rent' ) . '">';
			$item_output .= '<span class="mrent-chevron-icon" aria-hidden="true">';
			$item_output .= mrent_icon( 'chevron-down', [ 'width' => '10', 'height' => '10' ] );
			$item_output .= '</span>';
			$item_output .= '</button>';
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";
	}
}
