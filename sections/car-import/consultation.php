<?php
/**
 * «Авто под заказ»: секция «Закажите консультацию» + Contact Form 7.
 *
 * Figma: desktop 2989:7746 / mobile 3020:7855.
 * Desktop (xl+): 2 колонки — левая с заголовком + контактами, правая с формой.
 * Mobile: 1 колонка — заголовок → контакты → форма.
 *
 * CF7-форма берётся по ACF-полю `car_import_consultation_form_id`.
 * Если поле пусто — ищет первую доступную wpcf7_contact_form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_form_id = (int) get_field( 'car_import_consultation_form_id' );

if ( ! $mrent_form_id ) {
	$mrent_cf7_posts = get_posts( [
		'post_type'   => 'wpcf7_contact_form',
		'post_status' => 'publish',
		'title'       => 'Консультация (авто под заказ)',
		'numberposts' => 1,
	] );
	$mrent_form_id = $mrent_cf7_posts ? (int) $mrent_cf7_posts[0]->ID : 0;
}

if ( ! $mrent_form_id ) {
	return;
}

$mrent_messengers   = mrent_options_messengers();
$mrent_socials      = mrent_options_socials();
$mrent_phone        = mrent_options_get( 'contact_phone' );
$mrent_phone_url    = mrent_options_phone_url();
$mrent_subscribe_ig = $mrent_socials['instagram'] ?? '';
$mrent_subscribe_tg = $mrent_socials['telegram'] ?? '';

$mrent_icons_url = get_template_directory_uri() . '/assets/icons/';

$mrent_messenger_icons = [
	'telegram'  => 'contact-telegram-chat.png',
	'whatsapp'  => 'contact-whatsapp.png',
	'viber'     => 'contact-viber.png',
	'messenger' => 'contact-messenger.png',
];
?>

<section class="bg-mrent-black px-[15px] xl:px-[40px] 2xl:px-[100px] py-[60px] xl:py-[100px]">
	<div class="max-w-[1720px] mx-auto flex flex-col xl:flex-row xl:items-center xl:justify-between gap-[30px] xl:gap-[40px] min-w-0">

		<?php /* Левая колонка: заголовок + контакты. На desktop растягивается на всю высоту, justify-between. */ ?>
		<div class="flex flex-col xl:justify-between xl:self-stretch gap-[30px] xl:gap-0">

			<?php /* Заголовок + лид. */ ?>
			<div class="flex flex-col gap-[20px] text-mrent-white leading-[1.2]">
				<h2 class="font-display font-bold text-[28px] xl:text-[74px] xl:max-w-[577px]">
					<?php esc_html_e( 'Закажите консультацию', 'm-rent' ); ?>
				</h2>
				<p class="font-display text-base xl:text-[30px] xl:max-w-[639px] hidden xl:block">
					<?php esc_html_e( 'Заполните форму, и наш специалист свяжется с вами, чтобы подробно проконсультировать и предложить оптимальное решение для ваших задач', 'm-rent' ); ?>
				</p>
			</div>

			<?php /* Альтернативные способы связи. */ ?>
			<div class="flex flex-col gap-[20px] xl:gap-[40px]">

				<div class="flex flex-col gap-[15px] xl:gap-[30px]">
					<p class="font-display font-bold text-[20px] xl:text-[30px] text-mrent-white leading-[1.24]">
						<?php esc_html_e( 'Либо свяжитесь с нами удобным для вас способом', 'm-rent' ); ?>
					</p>

					<div class="flex items-center justify-between">

						<?php /* Телефон. */ ?>
						<?php if ( $mrent_phone !== '' ) : ?>
							<a href="<?php echo esc_url( $mrent_phone_url ); ?>" class="flex items-center gap-[15px] group">
								<img
									src="<?php echo esc_url( $mrent_icons_url . 'phone.svg' ); ?>"
									alt=""
									class="w-[13px] h-[20px] xl:w-[20px] xl:h-[30px] shrink-0 object-contain"
									width="20" height="30"
									loading="lazy" decoding="async" aria-hidden="true">
								<span class="font-display text-[16px] xl:text-[30px] text-mrent-white leading-[1.2] whitespace-nowrap group-hover:opacity-80 transition-opacity">
									<?php echo esc_html( $mrent_phone ); ?>
								</span>
							</a>
						<?php endif; ?>

						<?php /* Иконки мессенджеров. */ ?>
						<?php if ( $mrent_messengers ) : ?>
							<div class="flex items-center gap-[10px] xl:gap-[18.75px]">
								<?php foreach ( $mrent_messengers as $mrent_slug => $mrent_url ) : ?>
									<?php $mrent_file = $mrent_messenger_icons[ $mrent_slug ] ?? null; ?>
									<?php if ( ! $mrent_file || $mrent_url === '' ) continue; ?>
									<a
										href="<?php echo esc_url( $mrent_url ); ?>"
										class="block size-[40px] xl:size-[55px] hover:opacity-80 transition-opacity shrink-0"
										aria-label="<?php echo esc_attr( ucfirst( $mrent_slug ) ); ?>"
										target="_blank" rel="noopener noreferrer">
										<img
											src="<?php echo esc_url( $mrent_icons_url . $mrent_file ); ?>"
											alt=""
											class="size-full object-contain"
											width="55" height="55"
											loading="lazy" decoding="async">
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					</div>
				</div>

				<?php /* «Подписаться» ссылки. */ ?>
				<?php if ( $mrent_subscribe_ig || $mrent_subscribe_tg ) : ?>
					<div class="flex flex-col xl:flex-row gap-[10px] xl:gap-[30px]">
						<?php if ( $mrent_subscribe_ig ) : ?>
							<a href="<?php echo esc_url( $mrent_subscribe_ig ); ?>" class="self-start font-display font-medium text-[18px] xl:text-[30px] text-mrent-white underline underline-offset-2 hover:opacity-80 transition-opacity" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Подписаться в Instagram', 'm-rent' ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $mrent_subscribe_tg ) : ?>
							<a href="<?php echo esc_url( $mrent_subscribe_tg ); ?>" class="self-start font-display font-medium text-[18px] xl:text-[30px] text-mrent-white underline underline-offset-2 hover:opacity-80 transition-opacity" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Подписаться в Telegram', 'm-rent' ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php /* Правая колонка: CF7 форма. */ ?>
		<div class="mrent-consultation-form w-full xl:w-[845px] xl:max-w-full xl:min-w-0 xl:shrink">
			<?php echo do_shortcode( '[contact-form-7 id="' . $mrent_form_id . '"]' ); ?>
		</div>

	</div>
</section>
