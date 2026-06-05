<?php

/**
 * Плавающая кнопка мессенджеров (только мобайл).
 *
 * Закрытый вид — жёлтый круг с иконкой phone-chat в правом нижнем углу.
 * По клику — выезжают вертикальным стеком иконки мессенджеров из ACF Options.
 *
 * Ссылки берутся из mrent_options_messengers(); набор иконок совпадает с тем,
 * что используется в футере (sections/footer.php).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_fab_links = mrent_options_messengers();
if (! $mrent_fab_links) {
	return;
}

$mrent_fab_icons = [
	'whatsapp'  => ['src' => 'whatsapp.png',          'label' => 'WhatsApp'],
	'viber'     => ['src' => 'viber.png',             'label' => 'Viber'],
	'messenger' => ['src' => 'contact-messenger.png', 'label' => 'Messenger'],
	'telegram'  => ['src' => 'telegram-color.svg',    'label' => 'Telegram'],
];
$mrent_fab_icons_url = get_stylesheet_directory_uri() . '/assets/icons/';
?>

<div
	class="mrent-msg-fab xl:hidden fixed right-4 bottom-4 z-[60] flex flex-col items-center gap-3"
	data-mrent-msg-fab
	data-state="closed">

	<ul
		class="mrent-msg-fab__list flex flex-col items-center gap-3 transition-all duration-300 ease-out opacity-0 translate-y-2 pointer-events-none"
		data-mrent-msg-fab-list>
		<?php foreach ($mrent_fab_links as $mrent_fab_slug => $mrent_fab_url) : ?>
			<?php if (! isset($mrent_fab_icons[$mrent_fab_slug])) {
				continue;
			} ?>
			<?php $mrent_fab_icon = $mrent_fab_icons[$mrent_fab_slug]; ?>
			<li>
				<a
					href="<?php echo esc_url($mrent_fab_url); ?>"
					target="_blank"
					rel="noopener"
					class="block size-12 rounded-full shadow-md hover:scale-110 transition-transform"
					aria-label="<?php echo esc_attr($mrent_fab_icon['label']); ?>">
					<img
						src="<?php echo esc_url($mrent_fab_icons_url . $mrent_fab_icon['src']); ?>"
						alt="<?php echo esc_attr($mrent_fab_icon['label']); ?>"
						class="size-full object-contain"
						width="48" height="48" loading="lazy">
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<button
		type="button"
		class="mrent-msg-fab__btn relative flex items-center justify-center size-14 rounded-2xl bg-[#1f1e20] text-white border border-white shadow-lg transition-transform active:scale-95"
		aria-label="Связаться в мессенджере"
		aria-expanded="false"
		data-mrent-msg-fab-toggle>
		<span class="mrent-msg-fab__icon-closed inline-flex" aria-hidden="true">
			<?php echo mrent_icon('phone-call', ['class' => 'w-7 h-7']); ?>
		</span>
		<span class="mrent-msg-fab__icon-open absolute inset-0 hidden items-center justify-center" aria-hidden="true">
			<?php echo mrent_icon('close', ['class' => 'w-6 h-6']); ?>
		</span>
	</button>
</div>
