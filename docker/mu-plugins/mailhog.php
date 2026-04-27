<?php
/**
 * Plugin Name: MailHog SMTP redirect (dev only)
 * Description: Перенаправляет всю исходящую почту WordPress на MailHog (mailhog:1025).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Подменяем дефолтный From до того, как WP вызовет PHPMailer::setFrom().
// Иначе адрес "wordpress@localhost" не проходит валидацию PHPMailer и письмо отбрасывается.
add_filter( 'wp_mail_from', function () {
	return 'wordpress@m-rent.local';
} );

add_filter( 'wp_mail_from_name', function () {
	return 'm-rent dev';
} );

add_action( 'phpmailer_init', function ( $phpmailer ) {
	$phpmailer->isSMTP();
	$phpmailer->Host        = 'mailhog';
	$phpmailer->Port        = 1025;
	$phpmailer->SMTPAuth    = false;
	$phpmailer->SMTPSecure  = '';
	$phpmailer->SMTPAutoTLS = false;
} );
