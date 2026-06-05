<?php

/**
 * Шаблон футера темы m-rent.
 *
 * Раскладка единая для всех брейкпоинтов — различия десктопа и мобайла
 * отыгрываются Tailwind-префиксом `xl:`:
 *   • < xl: всё центрируется и стекается по вертикали,
 *     кнопка «Связаться с нами» — после колонок, во всю ширину.
 *   • ≥ xl: логотип + кнопка в шапке футера, ниже — колонки в строку,
 *     внизу — линия и юридический текст в две колонки.
 * Кнопка переставляется через `order`: обёртка логотип+кнопка на мобайле
 * имеет `display:contents`, поэтому кнопка участвует в общем потоке колонки.
 *
 * Данные пока захардкожены — позже вынесем в ACF Options / меню WP.
 */

if (! defined('ABSPATH')) {
	exit;
}

// Контакты — из ACF Options («Контакты и соцсети»). Хелперы из inc/options.php
// возвращают '' при незаполненных полях — шаблон ниже скрывает пустые ссылки.
$mrent_phone        = mrent_options_get('contact_phone');
$mrent_phone_url    = mrent_options_phone_url();
$mrent_email        = mrent_options_get('contact_email');
$mrent_email_url    = mrent_options_email_url();

$mrent_logo_url   = get_stylesheet_directory_uri() . '/assets/images/logo.png';
$mrent_home_url   = esc_url(home_url('/'));
$mrent_contact_url = '#booking-form-short';

// Колонки футера тянутся из WP-меню локации `footer` (Внешний вид → Меню).
// Верхний уровень меню = колонки (title — заголовок), дочерние = ссылки.
// Доп. CSS-классы пункта `desktop-only` / `mobile-only` управляют видимостью.
$mrent_footer_columns = mrent_get_footer_columns();

// Мессенджеры: ссылки — из ACF Options, иконки — файлы в assets/icons/.
// В цикле ниже рендерятся только те, у кого в Options заполнена ссылка.
$mrent_messenger_links = mrent_options_messengers();
$mrent_messenger_icons = [
	'whatsapp'  => ['src' => 'whatsapp.png',          'label' => 'WhatsApp'],
	'viber'     => ['src' => 'viber.png',             'label' => 'Viber'],
	'messenger' => ['src' => 'contact-messenger.png', 'label' => 'Messenger'],
	'telegram'  => ['src' => 'telegram-color.svg',    'label' => 'Telegram'],
];
$mrent_icons_url = get_stylesheet_directory_uri() . '/assets/icons/';

// Ссылки на соцсети — из ACF Options. [slug => url], только непустые.
$mrent_socials_footer = mrent_options_socials();

$mrent_legal_copy   = sprintf('© Все права защищены. %s %s', get_bloginfo('name'), date_i18n('Y'));
$mrent_privacy_url  = '#privacy';
?>

<footer class="bg-[#252426] text-white">
	<div class="flex flex-col gap-[30px] xl:gap-[100px] items-center xl:items-end pt-[30px] xl:pt-[100px] px-[15px] xl:px-[100px] max-w-[1920px] mx-auto w-full">

		<div class="flex flex-col gap-[30px] xl:gap-[50px] items-center xl:items-start w-full xl:max-w-[1720px]">

			<?php /* Обёртка логотип + кнопка. На мобайле display:contents — кнопка уходит вниз через order. */ ?>
			<div class="contents xl:flex xl:items-center xl:justify-between xl:w-full">
				<a href="<?php echo $mrent_home_url; ?>" class="order-1 block shrink-0" aria-label="<?php esc_attr_e('M-RENT.BY — на главную', 'm-rent'); ?>">
					<img src="<?php echo esc_url($mrent_logo_url); ?>" alt="M-RENT.BY" class="h-[45px] xl:h-[65px] w-auto" width="295" height="65">
				</a>
				<a href="<?php echo esc_url($mrent_contact_url); ?>" class="order-3 bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center px-[15px] h-[55px] xl:h-[65px] w-full xl:w-[300px] rounded-[15px] font-display font-medium text-[clamp(14px,calc(12.54px+0.39vw),20px)] leading-none transition-colors">
					<?php esc_html_e('Связаться с нами', 'm-rent'); ?>
				</a>
			</div>

			<?php /* Колонки меню + Контакты */ ?>
			<div class="order-2 flex flex-col xl:flex-row gap-[30px] xl:gap-0 items-center xl:items-start xl:justify-between w-full">
				<?php foreach ($mrent_footer_columns as $col) : ?>
					<?php
					// desktop-only / mobile-only из CSS-классов пункта меню → видимость колонки.
					$mrent_col_visibility = 'flex';
					if (in_array('desktop-only', $col['classes'], true)) {
						$mrent_col_visibility = 'hidden xl:flex';
					} elseif (in_array('mobile-only', $col['classes'], true)) {
						$mrent_col_visibility = 'flex xl:hidden';
					}
					?>
					<div class="<?php echo $mrent_col_visibility; ?> flex-col gap-[15px] xl:gap-[30px] items-center xl:items-start text-center xl:text-left text-white w-full xl:w-auto">
						<p class="font-display font-[800] text-[clamp(16px,calc(16.36px+0.97vw),24px)] leading-[1.2] xl:whitespace-nowrap"><?php echo esc_html($col['title']); ?></p>
						<ul class="flex flex-wrap xl:flex-col gap-[10px] xl:gap-[15px] items-start justify-center xl:justify-start w-full xl:w-auto text-[clamp(14px,calc(12.6px+0.91vw),18px)] leading-[1.2] font-normal xl:whitespace-nowrap">
							<?php foreach ($col['items'] as $item) : ?>
								<?php
								$mrent_item_visibility = '';
								if (in_array('desktop-only', $item['classes'], true)) {
									$mrent_item_visibility = 'hidden xl:block';
								} elseif (in_array('mobile-only', $item['classes'], true)) {
									$mrent_item_visibility = 'xl:hidden';
								}
								?>
								<li class="<?php echo $mrent_item_visibility; ?>">
									<a href="<?php echo esc_url($item['url']); ?>" class="hover:text-mrent-yellow transition-colors"><?php echo esc_html($item['label']); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>

				<?php /* Колонка Контакты */ ?>
				<div class="flex flex-col gap-[15px] xl:gap-[30px] items-center xl:items-start text-center xl:text-left w-full xl:w-[297px]">
					<p class="font-display font-[800] text-[clamp(16px,calc(16.36px+0.97vw),24px)] leading-[1.2] xl:whitespace-nowrap">Контакты</p>

					<div class="flex flex-col gap-[30px] xl:gap-[50px] items-center xl:items-start w-full">
						<?php if ($mrent_phone_url || $mrent_email_url) : ?>
							<div class="flex flex-col gap-[15px] items-center xl:items-start">
								<p class="text-[clamp(14px,calc(12.6px+0.91vw),18px)] leading-[1.2] xl:whitespace-nowrap">Реквизиты</p>
								<?php if ($mrent_phone_url) : ?>
									<a href="<?php echo esc_url($mrent_phone_url); ?>" class="flex gap-[8px] items-center text-[clamp(14px,calc(12.6px+0.91vw),18px)] leading-[1.2] whitespace-nowrap hover:text-mrent-yellow transition-colors">
										<span class="inline-flex items-center justify-center size-[18px] xl:size-[30px] shrink-0 text-white">
											<?php echo mrent_icon('phone', ['class' => 'size-full']); ?>
										</span>
										<?php echo esc_html($mrent_phone); ?>
									</a>
								<?php endif; ?>
								<?php if ($mrent_email_url) : ?>
									<a href="<?php echo esc_url($mrent_email_url); ?>" class="flex gap-[8px] items-center text-[clamp(14px,calc(12.6px+0.91vw),18px)] leading-[1.2] whitespace-nowrap hover:text-mrent-yellow transition-colors">
										<span class="inline-flex items-center justify-center size-[18px] xl:size-[30px] shrink-0 text-white">
											<?php echo mrent_icon('mail', ['class' => 'w-[18px] h-[15px] xl:w-[30px] xl:h-[25px]']); ?>
										</span>
										<?php echo esc_html($mrent_email); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ($mrent_messenger_links) : ?>
							<div class="flex gap-[7px] xl:gap-[11.229px] items-center">
								<?php foreach ($mrent_messenger_links as $mrent_slug => $mrent_url) : ?>
									<?php if (! isset($mrent_messenger_icons[$mrent_slug])) {
										continue;
									} ?>
									<?php $mrent_msgr = $mrent_messenger_icons[$mrent_slug]; ?>
									<a href="<?php echo esc_url($mrent_url); ?>" class="block size-7.5 xl:size-[38px] hover:opacity-80 transition-opacity" aria-label="<?php echo esc_attr($mrent_msgr['label']); ?>">
										<img src="<?php echo esc_url($mrent_icons_url . $mrent_msgr['src']); ?>" alt="<?php echo esc_attr($mrent_msgr['label']); ?>" class="size-full object-contain" width="56" height="56" loading="lazy">
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ($mrent_socials_footer) : ?>
							<div class="flex flex-col gap-[15px] items-center xl:items-start">
								<p class="font-display font-[800] text-[clamp(16px,calc(16.36px+0.97vw),24px)] leading-[1.2] xl:whitespace-nowrap">Мы в соцсетях</p>
								<div class="flex gap-[10px] xl:gap-[15px] items-center text-white">
									<?php foreach ($mrent_socials_footer as $name => $href) : ?>
										<a href="<?php echo esc_url($href); ?>" class="hover:text-mrent-yellow transition-colors" aria-label="<?php echo esc_attr(ucfirst($name)); ?>">
											<?php echo mrent_icon($name, ['class' => 'w-[30px] h-[30px] xl:w-[38px] xl:h-[38px]']); ?>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<?php /* Юридический блок */ ?>
		<div class="flex flex-col gap-[20px] xl:gap-[25px] items-end pb-[25px] w-full xl:max-w-[1720px]">
			<div class="h-px w-full bg-white/50"></div>
			<div class="flex flex-col xl:flex-row gap-[10px] xl:gap-0 items-center xl:justify-between text-center xl:text-left w-full text-[clamp(14px,calc(11.57px+0.65vw),18px)] leading-[1.2] xl:whitespace-nowrap">
				<p class="w-full xl:w-auto"><?php echo esc_html($mrent_legal_copy); ?></p>
				<a href="<?php echo esc_url($mrent_privacy_url); ?>" class="w-full xl:w-auto hover:text-mrent-yellow transition-colors"><?php esc_html_e('Политика конфиденциальности', 'm-rent'); ?></a>
			</div>
		</div>
	</div>
</footer>

<?php get_template_part('sections/common/booking-modal', null, [
	'id'          => 'booking-form',
	'form_titles' => ['Заявка (модальное окно)', 'Бронирование автомобиля'],
]); ?>

<?php get_template_part('sections/common/booking-modal', null, [
	'id'          => 'booking-form-short',
	'form_titles' => ['Заявка (короткая)'],
]); ?>

<?php get_template_part('sections/common/messenger-fab'); ?>

<?php wp_footer(); ?>
</body>

</html>