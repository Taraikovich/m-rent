<?php
/**
 * «Аренда для юр. лиц»: секция «Часто задаваемые вопросы»
 * (Figma 2809:7286 desktop / 2846:7060 mobile).
 *
 * Раскладка:
 *   Desktop (xl+, фрейм 1720px): h2 74px → карточки p-[40px], rounded-[15px],
 *     gap-[20px] между карточками, gap-[60px] между h2 и списком.
 *     Вопрос 35px, иконка-шеврон 36×36 (жёлтая, #FFD700) справа.
 *   Mobile: h2 28px → карточки p-[20px], gap-[10px] между ними, gap-[30px] до h2.
 *     Вопрос 18px, иконка 25×25.
 *
 * Аккордеон — нативные <details>, иконка крутится через .mrent-faq-icon
 * (см. src/main.css, тот же класс что в car-import/tabs.php).
 *
 * ACF (group_mrent_biz_services):
 *   • biz_faq_title — Text;     default «Часто задаваемые вопросы»
 *   • biz_faq_items — Repeater (question + answer); если пусто — fallback из 8 вопросов дизайна
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_title = (string) get_field( 'biz_faq_title' );
if ( $mrent_title === '' ) {
	$mrent_title = __( 'Часто задаваемые вопросы', 'm-rent' );
}

$mrent_items_raw = (array) get_field( 'biz_faq_items' );
$mrent_items     = [];
foreach ( $mrent_items_raw as $mrent_row ) {
	$mrent_q = (string) ( $mrent_row['question'] ?? '' );
	$mrent_a = (string) ( $mrent_row['answer'] ?? '' );
	if ( $mrent_q === '' ) {
		continue;
	}
	$mrent_items[] = [ 'question' => $mrent_q, 'answer' => $mrent_a ];
}

if ( ! $mrent_items ) {
	$mrent_faq_demo = [
		'Можно ли оформить аренду автомобиля на юридическое лицо?',
		'На какой срок можно арендовать автомобиль?',
		'Какие документы нужны для оформления аренды?',
		'Можно ли заказать автомобиль для руководителя или важного гостя?',
		'Можно ли заказать автомобиль с подачей по нужному адресу?',
		'Что входит в стоимость аренды для юридических лиц?',
		'Нужен ли депозит при аренде на юридическое лицо?',
		'Подходит ли услуга для долгосрочной аренды?',
	];
	foreach ( $mrent_faq_demo as $mrent_demo_q ) {
		$mrent_items[] = [ 'question' => $mrent_demo_q, 'answer' => '' ];
	}
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[30px] xl:pt-[60px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]">

		<h2 class="font-display font-[700] text-[28px] xl:text-[74px] leading-[1.2] text-mrent-white">
			<?php echo esc_html( $mrent_title ); ?>
		</h2>

		<div class="flex flex-col gap-[10px] xl:gap-[20px]">
			<?php foreach ( $mrent_items as $mrent_qa ) : ?>
				<details class="mrent-faq-item bg-[#252426] border border-[#302f31] rounded-[15px]">
					<summary class="flex items-center justify-between gap-[15px] xl:gap-[20px] p-[20px] xl:p-[40px] cursor-pointer select-none">
						<h3 class="font-[800] text-[18px] xl:text-[35px] leading-[1.2] text-mrent-white">
							<?php echo esc_html( $mrent_qa['question'] ); ?>
						</h3>
						<span class="mrent-faq-icon shrink-0 flex items-center justify-center bg-mrent-yellow rounded-[8px] size-[25px] xl:size-[36px] text-mrent-black">
							<svg class="w-[10px] h-[10px] xl:w-[14px] xl:h-[14px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
								<path d="M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
							</svg>
						</span>
					</summary>
					<?php if ( $mrent_qa['answer'] !== '' ) : ?>
						<div class="px-[20px] xl:px-[40px] pb-[20px] xl:pb-[40px] pt-0 text-mrent-white font-[400] text-[14px] xl:text-[24px] leading-[1.4]">
							<?php echo wp_kses_post( $mrent_qa['answer'] ); ?>
						</div>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>
		</div>

	</div>
</section>
