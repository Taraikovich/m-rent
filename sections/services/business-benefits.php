<?php

/**
 * «Аренда для юр. лиц»: секция «Почему это удобно для бизнеса»
 * (Figma 2809:7261 desktop / 2846:7044 mobile).
 *
 * Раскладка:
 *   Desktop (xl+): h2 74px → p 30px, gap-[30px], текст w-[1656px] в фрейме 1720.
 *   Mobile:        h2 28px → p 16px, gap-[15px], текст шириной контейнера.
 *
 * ACF (group_mrent_biz_services):
 *   • biz_benefits_title — Text;     default «Почему это удобно для бизнеса»
 *   • biz_benefits_text  — Textarea; default — описание из дизайна
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title = (string) get_field('biz_benefits_title');
if ($mrent_title === '') {
	$mrent_title = __('Почему это удобно для бизнеса', 'm-rent');
}

$mrent_text = (string) get_field('biz_benefits_text');
if ($mrent_text === '') {
	$mrent_text = __('Аренда автомобиля позволяет решать транспортные задачи компании без покупки, содержания и обслуживания собственного автопарка. Вы получаете готовый автомобиль именно тогда, когда он нужен: для руководителя, сотрудника, важного гостя, командировки, деловой встречи или корпоративного события. Это гибко, удобно и позволяет подобрать автомобиль под нужный уровень, формат и статус поездки.', 'm-rent');
}
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[30px] xl:pt-[60px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[5px] xl:gap-[10px]">
		<h2 class="font-display font-[700] text-[clamp(24px,17.69px+1.68vw,50px)] leading-[1.2] text-mrent-white">
			<?php echo esc_html($mrent_title); ?>
		</h2>
		<p class="font-[400] text-[clamp(14px,12.54px+0.39vw,20px)] leading-[1.2] text-mrent-white whitespace-pre-line">
			<?php echo esc_html($mrent_text); ?>
		</p>
	</div>
</section>