<?php

/**
 * Шаблон шапки темы m-rent.
 *
 * Логика:
 *   • Десктоп (≥ lg): nav слева | логотип в центре | контакты+lang+социалки справа.
 *   • Мобайл  (< lg): логотип | RU/USD | бургер. Открытие — оверлей с полным меню.
 *
 * Контакты и соцсети тянутся из ACF Options («Контакты и соцсети»).
 */

// Контакты и соцсети — из ACF Options. Хелперы из inc/options.php возвращают
// '' / [] при незаполненных полях; шаблон ниже скрывает пустые ссылки/блоки.
$mrent_phone     = mrent_options_get('contact_phone');
$mrent_phone_url = mrent_options_phone_url();
$mrent_email     = mrent_options_get('contact_email');
$mrent_email_url = mrent_options_email_url();
$mrent_socials   = mrent_options_socials();

$mrent_logo_url = get_stylesheet_directory_uri() . '/assets/images/logo.png';
$mrent_home_url = esc_url(home_url('/'));

$mrent_menu_args_desktop = [
	'theme_location' => 'primary',
	'container'      => false,
	'menu_class'     => 'mrent-nav-list mrent-nav-desktop',
	'fallback_cb'    => 'mrent_nav_fallback',
	'walker'         => new MRent_Nav_Walker(),
];

$mrent_menu_args_mobile = [
	'theme_location' => 'primary',
	'container'      => false,
	'menu_class'     => 'mrent-nav-list mrent-nav-mobile',
	'fallback_cb'    => 'mrent_nav_fallback',
	'walker'         => new MRent_Nav_Walker(),
];

if (! function_exists('mrent_nav_fallback')) {
	function mrent_nav_fallback()
	{
		echo '<p class="text-white/60 text-sm">Меню «Главное меню» не назначено. Создайте его в WP Admin → Внешний вид → Меню.</p>';
	}
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<?php /* Страховка scroll-reveal: без JS карточки [data-mrent-reveal] не должны
	         остаться скрытыми (скрытие задаёт CSS в src/main.css). */ ?>
	<noscript>
		<style>[data-mrent-reveal]>*{opacity:1 !important;transform:none !important;}</style>
	</noscript>
</head>

<body <?php body_class('min-h-screen antialiased'); ?>>
	<?php wp_body_open(); ?>

	<header class="mrent-header-surface fixed top-0 inset-x-0 z-50 bg-mrent-black/50 backdrop-blur-[11.8px]">
		<?php /* ─── Десктопная раскладка ─── */ ?>
		<div class="hidden header:grid header:grid-cols-[1fr_auto_1fr] items-center py-[24px] px-[10px] gap-8 header:gap-12 max-w-[1720px] mx-auto w-full">
			<nav aria-label="<?php esc_attr_e('Главное меню', 'm-rent'); ?>">
				<?php wp_nav_menu($mrent_menu_args_desktop); ?>
			</nav>

			<a href="<?php echo $mrent_home_url; ?>" class="block shrink-0 justify-self-center" aria-label="<?php esc_attr_e('M-RENT.BY — на главную', 'm-rent'); ?>">
				<img src="<?php echo esc_url($mrent_logo_url); ?>" alt="M-RENT.BY" class="h-[45px] w-auto" width="203" height="45">
			</a>

			<div class="flex items-center justify-between gap-6 2xl:gap-8 w-full">
				<div class="flex gap-[10px] items-center shrink-0">
					<?php echo mrent_lang_switcher(); ?>
				</div>

				<div class="flex gap-[24px] items-center shrink-0">
					<?php if ($mrent_phone_url) : ?>
						<a href="<?php echo esc_url($mrent_phone_url); ?>" class="flex gap-[8px] items-center text-white text-[16px] leading-[1.2] whitespace-nowrap hover:text-mrent-yellow transition-colors">
							<span class="text-white"><?php echo mrent_icon('phone', ['width' => '14', 'height' => '20']); ?></span>
							<?php echo esc_html($mrent_phone); ?>
						</a>
					<?php endif; ?>
					<?php if ($mrent_email_url) : ?>
						<a href="<?php echo esc_url($mrent_email_url); ?>" class="flex gap-[4px] items-center text-white text-[16px] leading-[1.2] whitespace-nowrap hover:text-mrent-yellow transition-colors">
							<span class="text-white"><?php echo mrent_icon('mail', ['width' => '24', 'height' => '24']); ?></span>
							<?php echo esc_html($mrent_email); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ($mrent_socials) : ?>
					<div class="flex gap-[10px] items-center shrink-0 text-white">
						<?php foreach ($mrent_socials as $name => $href) : ?>
							<a href="<?php echo esc_url($href); ?>" class="hover:text-mrent-yellow transition-colors" aria-label="<?php echo esc_attr(ucfirst($name)); ?>">
								<?php echo mrent_icon($name, ['width' => '24', 'height' => '24']); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php /* ─── Мобильная раскладка ─── */ ?>
		<div class="flex header:hidden items-center justify-between p-[15px] gap-3">
			<a href="<?php echo $mrent_home_url; ?>" class="block shrink-0" aria-label="<?php esc_attr_e('M-RENT.BY — на главную', 'm-rent'); ?>">
				<img src="<?php echo esc_url($mrent_logo_url); ?>" alt="M-RENT.BY" class="h-[30px] w-auto" width="136" height="30">
			</a>

			<div class="flex gap-[5px] items-center shrink-0">
				<?php echo mrent_lang_switcher(true); ?>
			</div>

			<button type="button" class="text-white shrink-0 inline-flex items-center justify-center w-[40px] h-[24px]" aria-label="<?php esc_attr_e('Открыть меню', 'm-rent'); ?>" aria-expanded="false" aria-controls="mrent-mobile-menu" data-mrent-burger>
				<?php echo mrent_icon('burger', ['width' => '40', 'height' => '24']); ?>
			</button>
		</div>
	</header>

	<?php /* ─── Мобильный оверлей ─── */ ?>
	<div id="mrent-mobile-menu" class="mrent-mobile-panel header:hidden" data-mrent-mobile-menu aria-hidden="true">
		<div class="flex flex-col items-center gap-[40px] px-[15px] pt-[15px] pb-[30px] min-h-screen">
			<div class="flex items-center justify-between w-full">
				<a href="<?php echo $mrent_home_url; ?>" class="block shrink-0" aria-label="<?php esc_attr_e('M-RENT.BY — на главную', 'm-rent'); ?>">
					<img src="<?php echo esc_url($mrent_logo_url); ?>" alt="M-RENT.BY" class="h-[30px] w-auto" width="136" height="30">
				</a>

				<div class="flex gap-[5px] items-center shrink-0">
					<?php echo mrent_lang_switcher(true); ?>
				</div>

				<button type="button" class="text-white shrink-0 inline-flex items-center justify-center w-[23px] h-[23px]" aria-label="<?php esc_attr_e('Закрыть меню', 'm-rent'); ?>" data-mrent-burger-close>
					<?php echo mrent_icon('close', ['width' => '23', 'height' => '23']); ?>
				</button>
			</div>

			<nav class="w-full" aria-label="<?php esc_attr_e('Главное меню', 'm-rent'); ?>">
				<?php wp_nav_menu($mrent_menu_args_mobile); ?>
			</nav>

			<div class="flex flex-col items-center gap-[30px]">
				<?php if ($mrent_phone_url || $mrent_email_url) : ?>
					<div class="flex flex-col items-center gap-[24px]">
						<?php if ($mrent_phone_url) : ?>
							<a href="<?php echo esc_url($mrent_phone_url); ?>" class="flex gap-[10px] items-center text-white text-[16px] leading-[1.2] whitespace-nowrap">
								<span class="text-white"><?php echo mrent_icon('phone', ['width' => '14', 'height' => '20']); ?></span>
								<?php echo esc_html($mrent_phone); ?>
							</a>
						<?php endif; ?>
						<?php if ($mrent_email_url) : ?>
							<a href="<?php echo esc_url($mrent_email_url); ?>" class="flex gap-[10px] items-center text-white text-[16px] leading-[1.2] whitespace-nowrap">
								<span class="text-white"><?php echo mrent_icon('mail', ['width' => '24', 'height' => '24']); ?></span>
								<?php echo esc_html($mrent_email); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ($mrent_socials) : ?>
					<div class="flex gap-[10px] items-center text-white">
						<?php foreach ($mrent_socials as $name => $href) : ?>
							<a href="<?php echo esc_url($href); ?>" aria-label="<?php echo esc_attr(ucfirst($name)); ?>">
								<?php echo mrent_icon($name, ['width' => '24', 'height' => '24']); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>