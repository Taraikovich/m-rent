<?php
/**
 * About: секция «О нас и нашей команде».
 *
 * Контент: ACF-группа «О нас», таб «Команда».
 * Если поле «Заголовок» (about_team_title) не заполнено — секция не выводится.
 *
 * Один DOM, адаптив через `2xl:`-варианты (Figma 2218:3280 desktop / 2345:3792 mobile):
 *
 *   < 2xl  — flex-col gap-30: заголовок 28px Bold → лид 16px (Bold-префикс
 *            + остальной текст) → второй параграф 16px → карточка списка
 *            (bg #252426, p-25, rounded-15) → фото (full-width, aspect 330/270,
 *            rounded-15). Карточка и фото стыкуются вплотную (gap-0), как в макете.
 *
 *   ≥ 2xl  — flex-row gap-60 items-center: слева текст-колонка 620px, справа
 *            блок 1040px (relative). Карточка 580px в нормальном потоке —
 *            задаёт высоту блока, фото w-500 absolute right-0 top-0 bottom-0
 *            тянется на всю высоту карточки. Карточка z-10 наплывает на фото
 *            ~40px слева (фото начинается с x=540, карточка от x=0 до x=580).
 *
 * Лид собирается из двух полей: `about_team_lead_bold` (жирный префикс) +
 * `about_team_lead_rest` (остальной текст). Пункты — repeater `about_team_list`
 * с подполем `item`. По примеру `home_advantages`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = (string) get_field( 'about_team_title', $mrent_page_id );

if ( $mrent_title === '' ) {
	return;
}

$mrent_lead_bold  = (string) get_field( 'about_team_lead_bold', $mrent_page_id );
$mrent_lead_rest  = (string) get_field( 'about_team_lead_rest', $mrent_page_id );
$mrent_body       = (string) get_field( 'about_team_body', $mrent_page_id );
$mrent_image      = get_field( 'about_team_image', $mrent_page_id );
$mrent_list_title = (string) get_field( 'about_team_list_title', $mrent_page_id );
$mrent_list_raw   = get_field( 'about_team_list', $mrent_page_id );

$mrent_image_url = $mrent_image['url'] ?? '';
$mrent_image_alt = $mrent_image['alt'] ?? '';

$mrent_list = [];
if ( is_array( $mrent_list_raw ) ) {
	foreach ( $mrent_list_raw as $mrent_row ) {
		$mrent_item = isset( $mrent_row['item'] ) ? (string) $mrent_row['item'] : '';
		if ( $mrent_item !== '' ) {
			$mrent_list[] = $mrent_item;
		}
	}
}
?>

<section class="bg-mrent-black py-[60px] 2xl:py-[100px]">
	<div class="px-[15px] 2xl:px-[100px]">
		<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] 2xl:gap-[60px]">

			<h2 class="font-display font-[700] text-[28px] 2xl:text-[74px] leading-[1.2] text-mrent-white">
				<?php echo esc_html( $mrent_title ); ?>
			</h2>

			<div class="flex flex-col gap-[30px] 2xl:flex-row 2xl:items-center 2xl:gap-[60px]">

				<?php /* Текст-колонка. Mobile — full-width; 2xl — фикс 620px. */ ?>
				<?php if ( $mrent_lead_bold !== '' || $mrent_lead_rest !== '' || $mrent_body !== '' ) : ?>
					<div class="flex flex-col gap-[30px] text-mrent-white leading-[1.2] 2xl:w-[620px] 2xl:shrink-0">
						<?php if ( $mrent_lead_bold !== '' || $mrent_lead_rest !== '' ) : ?>
							<p class="text-[16px] 2xl:text-[30px]">
								<?php if ( $mrent_lead_bold !== '' ) : ?>
									<strong class="font-[700]"><?php echo esc_html( $mrent_lead_bold ); ?></strong><?php endif; ?>
								<?php echo esc_html( $mrent_lead_rest ); ?>
							</p>
						<?php endif; ?>
						<?php if ( $mrent_body !== '' ) : ?>
							<p class="text-[16px] 2xl:text-[30px]"><?php echo nl2br( esc_html( $mrent_body ) ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php /* Правый блок:
				         • mobile — flex-col (карточка → фото), фото с aspect-ratio.
				         • 2xl   — relative-контейнер 1040px. Карточка w-580 в нормальном
				                   потоке (z-10) задаёт высоту блока. Фото — absolute
				                   right-0 top-0 bottom-0 w-500: высота = высоте карточки,
				                   карточка наплывает на фото ~40px слева. */ ?>
				<div class="flex flex-col w-full 2xl:block 2xl:relative 2xl:w-260 2xl:shrink-0">

					<?php if ( ! empty( $mrent_list ) ) : ?>
						<div class="bg-[#252426] rounded-[15px] p-[25px] 2xl:p-[40px] w-full 2xl:relative 2xl:z-10 2xl:w-145">
							<div class="flex flex-col gap-[20px]">
								<?php if ( $mrent_list_title !== '' ) : ?>
									<p class="font-display font-[600] text-[20px] 2xl:text-[35px] leading-[1.2] text-mrent-white">
										<?php echo esc_html( $mrent_list_title ); ?>
									</p>
								<?php endif; ?>
								<ul class="flex flex-col gap-[10px]">
									<?php foreach ( $mrent_list as $mrent_item ) : ?>
										<li class="flex gap-[15px] items-center text-mrent-white">
											<span class="size-[6px] rounded-full bg-mrent-white shrink-0" aria-hidden="true"></span>
											<span class="text-[14px] 2xl:text-[24px] leading-[1.2]"><?php echo esc_html( $mrent_item ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $mrent_image_url ) : ?>
						<div class="w-full aspect-[330/270] rounded-[15px] overflow-hidden 2xl:absolute 2xl:inset-y-0 2xl:right-0 2xl:w-125 2xl:h-auto 2xl:aspect-auto">
							<img
								src="<?php echo esc_url( $mrent_image_url ); ?>"
								alt="<?php echo esc_attr( $mrent_image_alt ); ?>"
								class="block w-full h-full object-cover"
								loading="lazy"
								decoding="async"
							>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</section>
