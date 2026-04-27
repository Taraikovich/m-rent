<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen bg-white text-slate-900 antialiased' ); ?>>
<?php wp_body_open(); ?>

<header class="border-b border-slate-200">
	<div class="container mx-auto flex items-center justify-between p-6">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-xl font-bold tracking-tight">
			<?php bloginfo( 'name' ); ?>
		</a>
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => 'nav',
				'menu_class'     => 'flex gap-6 text-sm font-medium',
				'depth'          => 1,
			] );
		}
		?>
	</div>
</header>
