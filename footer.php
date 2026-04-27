<?php

/**
 * Шаблон футера темы m-rent.
 *
 * Логика:
 *   • Десктоп (≥ 2xl): логотип + кнопка «Связаться с нами» в шапке футера,
 *     ниже — 4 колонки (Меню / Автопарк / Мероприятия / Контакты),
 *     внизу — горизонтальная линия и юридический текст в две колонки.
 *   • Мобайл  (< 2xl): всё центрируется и стекается по вертикали,
 *     кнопка «Связаться с нами» — после колонок, во всю ширину.
 *
 * Данные пока захардкожены — позже вынесем в ACF Options / меню WP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_phone        = '+375 (29) 123-45-67';
$mrent_phone_url    = 'tel:+375291234567';
$mrent_email        = 'info@m-rent.by';
$mrent_email_url    = 'mailto:' . $mrent_email;

$mrent_logo_url   = get_stylesheet_directory_uri() . '/assets/images/logo.png';
$mrent_home_url   = esc_url( home_url( '/' ) );
$mrent_contact_url = '#contact';

// Колонки футера тянутся из WP-меню локации `footer` (Внешний вид → Меню).
// Верхний уровень меню = колонки (title — заголовок), дочерние = ссылки.
// Доп. CSS-классы пункта `desktop-only` / `mobile-only` управляют видимостью.
$mrent_footer_columns = mrent_get_footer_columns();

// Цветные иконки мессенджеров — отдельные файлы в assets/icons/.
// Telegram — SVG (масштабируется без потерь), остальные — PNG.
$mrent_messengers = [
	'telegram' => [ 'href' => '#', 'src' => 'telegram-color.svg', 'label' => 'Telegram' ],
	'whatsapp' => [ 'href' => '#', 'src' => 'whatsapp.png',       'label' => 'WhatsApp' ],
	'viber'    => [ 'href' => '#', 'src' => 'viber.png',          'label' => 'Viber'    ],
	'max'      => [ 'href' => '#', 'src' => 'max.png',            'label' => 'MAX'      ],
];
$mrent_icons_url = get_stylesheet_directory_uri() . '/assets/icons/';

$mrent_socials_footer = [
	'instagram' => '#',
	'youtube'   => '#',
	'tiktok'    => '#',
	'telegram'  => '#',
];

$mrent_legal_copy   = sprintf( '© Все права защищены. %s %s', get_bloginfo( 'name' ), date_i18n( 'Y' ) );
$mrent_privacy_url  = '#privacy';
?>

<footer class="bg-mrent-black text-white mt-[100px]">
	<?php /* ─── Десктопная раскладка ─── */ ?>
	<div class="hidden 2xl:flex flex-col gap-[100px] items-end pt-[100px] px-[100px] max-w-[1920px] mx-auto w-full">
		<div class="flex flex-col gap-[50px] items-start w-full max-w-[1720px]">
			<div class="flex items-center justify-between w-full">
				<a href="<?php echo $mrent_home_url; ?>" class="block shrink-0" aria-label="<?php esc_attr_e( 'M-RENT.BY — на главную', 'm-rent' ); ?>">
					<img src="<?php echo esc_url( $mrent_logo_url ); ?>" alt="M-RENT.BY" class="h-[65px] w-auto" width="295" height="65">
				</a>
				<a href="<?php echo esc_url( $mrent_contact_url ); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center px-[15px] h-[65px] w-[300px] rounded-[15px] font-display font-medium text-[20px] leading-none transition-colors">
					<?php esc_html_e( 'Связаться с нами', 'm-rent' ); ?>
				</a>
			</div>

			<div class="flex items-start justify-between w-full">
				<?php foreach ( $mrent_footer_columns as $col ) : ?>
					<?php if ( in_array( 'mobile-only', $col['classes'], true ) ) { continue; } ?>
					<div class="flex flex-col gap-[30px] items-start text-white">
						<p class="font-display font-[800] text-[35px] leading-[1.2] whitespace-nowrap"><?php echo esc_html( $col['title'] ); ?></p>
						<ul class="flex flex-col gap-[15px] items-start text-[30px] leading-[1.2] font-normal whitespace-nowrap">
							<?php foreach ( $col['items'] as $item ) : ?>
								<?php if ( in_array( 'mobile-only', $item['classes'], true ) ) { continue; } ?>
								<li>
									<a href="<?php echo esc_url( $item['url'] ); ?>" class="hover:text-mrent-yellow transition-colors"><?php echo esc_html( $item['label'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>

				<div class="flex flex-col gap-[30px] items-start w-[297px]">
					<p class="font-display font-[800] text-[35px] leading-[1.2] whitespace-nowrap">Контакты</p>

					<div class="flex flex-col gap-[50px] items-start w-full">
						<div class="flex flex-col gap-[15px] items-start">
							<p class="text-[30px] leading-[1.2] whitespace-nowrap">Реквизиты</p>
							<a href="<?php echo esc_url( $mrent_phone_url ); ?>" class="flex gap-[8px] items-center text-[30px] leading-[1.2] whitespace-nowrap hover:text-mrent-yellow transition-colors">
								<span class="inline-flex items-center justify-center size-[30px] shrink-0 text-white">
									<?php echo mrent_icon( 'phone', [ 'width' => '18', 'height' => '27' ] ); ?>
								</span>
								<?php echo esc_html( $mrent_phone ); ?>
							</a>
							<a href="<?php echo esc_url( $mrent_email_url ); ?>" class="flex gap-[8px] items-center text-[30px] leading-[1.2] whitespace-nowrap hover:text-mrent-yellow transition-colors">
								<span class="inline-flex items-center justify-center size-[30px] shrink-0 text-white">
									<?php echo mrent_icon( 'mail', [ 'width' => '30', 'height' => '25' ] ); ?>
								</span>
								<?php echo esc_html( $mrent_email ); ?>
							</a>
						</div>

						<div class="flex gap-[11.229px] items-center">
							<?php foreach ( $mrent_messengers as $messenger ) : ?>
								<a href="<?php echo esc_url( $messenger['href'] ); ?>" class="block size-[56.143px] hover:opacity-80 transition-opacity" aria-label="<?php echo esc_attr( $messenger['label'] ); ?>">
									<img src="<?php echo esc_url( $mrent_icons_url . $messenger['src'] ); ?>" alt="<?php echo esc_attr( $messenger['label'] ); ?>" class="size-full object-contain" width="56" height="56" loading="lazy">
								</a>
							<?php endforeach; ?>
						</div>

						<div class="flex flex-col gap-[15px] items-start">
							<p class="font-display font-[800] text-[35px] leading-[1.2] whitespace-nowrap">Мы в соцсетях</p>
							<div class="flex gap-[15px] items-center text-white">
								<?php foreach ( $mrent_socials_footer as $name => $href ) : ?>
									<a href="<?php echo esc_url( $href ); ?>" class="hover:text-mrent-yellow transition-colors" aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>">
										<?php echo mrent_icon( $name, [ 'width' => '40', 'height' => '40' ] ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="flex flex-col gap-[25px] items-end pb-[25px] w-full max-w-[1720px]">
			<div class="h-px w-full bg-white/50"></div>
			<div class="flex items-center justify-between w-full text-[24px] leading-[1.2] whitespace-nowrap">
				<p><?php echo esc_html( $mrent_legal_copy ); ?></p>
				<a href="<?php echo esc_url( $mrent_privacy_url ); ?>" class="hover:text-mrent-yellow transition-colors"><?php esc_html_e( 'Политика конфиденциальности', 'm-rent' ); ?></a>
			</div>
		</div>
	</div>

	<?php /* ─── Мобильная раскладка ─── */ ?>
	<div class="2xl:hidden flex flex-col gap-[30px] items-center pt-[30px] px-[15px]">
		<a href="<?php echo $mrent_home_url; ?>" class="block" aria-label="<?php esc_attr_e( 'M-RENT.BY — на главную', 'm-rent' ); ?>">
			<img src="<?php echo esc_url( $mrent_logo_url ); ?>" alt="M-RENT.BY" class="h-[45px] w-auto" width="204" height="45">
		</a>

		<div class="flex flex-col gap-[30px] items-center w-full">
			<?php foreach ( $mrent_footer_columns as $col ) : ?>
				<?php if ( in_array( 'desktop-only', $col['classes'], true ) ) { continue; } ?>
				<div class="flex flex-col gap-[15px] items-center text-center w-full">
					<p class="font-display font-[800] text-[20px] leading-[1.2]"><?php echo esc_html( $col['title'] ); ?></p>
					<ul class="flex flex-wrap gap-[10px] items-start justify-center text-[16px] leading-[1.2] font-normal w-full">
						<?php foreach ( $col['items'] as $item ) : ?>
							<?php if ( in_array( 'desktop-only', $item['classes'], true ) ) { continue; } ?>
							<li>
								<a href="<?php echo esc_url( $item['url'] ); ?>" class="hover:text-mrent-yellow transition-colors"><?php echo esc_html( $item['label'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>

			<div class="flex flex-col gap-[15px] items-center text-center w-full">
				<p class="font-display font-[800] text-[20px] leading-[1.2]">Контакты</p>
				<div class="flex flex-col gap-[30px] items-center">
					<div class="flex flex-col gap-[15px] items-center">
						<p class="text-[16px] leading-[1.2]">Реквизиты</p>
						<a href="<?php echo esc_url( $mrent_phone_url ); ?>" class="flex gap-[8px] items-center text-[16px] leading-[1.2] whitespace-nowrap">
							<span class="inline-flex items-center justify-center size-[18px] shrink-0 text-white">
								<?php echo mrent_icon( 'phone', [ 'width' => '11', 'height' => '16' ] ); ?>
							</span>
							<?php echo esc_html( $mrent_phone ); ?>
						</a>
						<a href="<?php echo esc_url( $mrent_email_url ); ?>" class="flex gap-[8px] items-center text-[16px] leading-[1.2] whitespace-nowrap">
							<span class="inline-flex items-center justify-center size-[18px] shrink-0 text-white">
								<?php echo mrent_icon( 'mail', [ 'width' => '18', 'height' => '15' ] ); ?>
							</span>
							<?php echo esc_html( $mrent_email ); ?>
						</a>
					</div>

					<div class="flex gap-[7px] items-center">
						<?php foreach ( $mrent_messengers as $messenger ) : ?>
							<a href="<?php echo esc_url( $messenger['href'] ); ?>" class="block size-[35px] hover:opacity-80 transition-opacity" aria-label="<?php echo esc_attr( $messenger['label'] ); ?>">
								<img src="<?php echo esc_url( $mrent_icons_url . $messenger['src'] ); ?>" alt="<?php echo esc_attr( $messenger['label'] ); ?>" class="size-full object-contain" width="35" height="35" loading="lazy">
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="flex flex-col gap-[15px] items-center text-center">
				<p class="font-display font-[800] text-[20px] leading-[1.2]">Мы в соцсетях</p>
				<div class="flex gap-[10px] items-center text-white">
					<?php foreach ( $mrent_socials_footer as $name => $href ) : ?>
						<a href="<?php echo esc_url( $href ); ?>" aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>">
							<?php echo mrent_icon( $name, [ 'width' => '32', 'height' => '32' ] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<a href="<?php echo esc_url( $mrent_contact_url ); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center w-full h-[55px] px-[15px] rounded-[15px] font-display font-medium text-[14px] leading-none transition-colors">
				<?php esc_html_e( 'Связаться с нами', 'm-rent' ); ?>
			</a>
		</div>

		<div class="flex flex-col gap-[20px] items-end pb-[25px] w-full">
			<div class="h-px w-full bg-white/50"></div>
			<div class="flex flex-col gap-[10px] items-center text-center w-full text-[14px] leading-[1.2]">
				<p class="w-full"><?php echo esc_html( $mrent_legal_copy ); ?></p>
				<a href="<?php echo esc_url( $mrent_privacy_url ); ?>" class="w-full hover:text-mrent-yellow transition-colors"><?php esc_html_e( 'Политика конфиденциальности', 'm-rent' ); ?></a>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
