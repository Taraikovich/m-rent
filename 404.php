<?php

/**
 * Шаблон 404 (страница не найдена).
 *
 * Логика:
 *   • Гигантская полупрозрачная цифра «404» как фоновый акцент.
 *   • Центрированный заголовок + подзаголовок + жёлтая кнопка возврата на главную.
 *   • Хедер и футер тянутся стандартно — отдельной разметки тут нет.
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="bg-mrent-black text-white relative overflow-hidden pt-[60px] 2xl:pt-[93px]">
	<section class="relative px-[15px] 2xl:px-[100px] py-[80px] 2xl:py-[147px] flex items-center justify-center min-h-[calc(100vh-60px)] 2xl:min-h-[853px]">
		<span aria-hidden="true" class="pointer-events-none select-none absolute inset-0 flex items-center justify-center font-display font-[700] text-white/[0.04] leading-none whitespace-nowrap text-[60vw] 2xl:text-[613px] tracking-[-0.05em]">
			404
		</span>

		<div class="relative z-[1] flex flex-col items-center gap-[30px] 2xl:gap-[50px] w-full max-w-[330px] 2xl:max-w-[1068px] text-center">
			<div class="flex flex-col items-center gap-[15px] 2xl:gap-[19px] text-white">
				<h1 class="font-display font-[700] text-[28px] 2xl:text-[74px] leading-[1.2]">
					<?php esc_html_e('Кажется, вы свернули не туда', 'm-rent'); ?>
				</h1>
				<p class="font-[400] text-[16px] 2xl:text-[30px] leading-[1.2] 2xl:max-w-[798px]">
					<?php esc_html_e('Страница не найдена, но это не повод останавливать поездку. Перейдите на главную или выберите автомобиль из каталога', 'm-rent'); ?>
				</p>
			</div>

			<a href="<?php echo esc_url(home_url('/')); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center w-full 2xl:w-[300px] h-[55px] 2xl:h-[65px] px-[15px] rounded-[15px] font-display font-medium text-[14px] 2xl:text-[20px] leading-none transition-colors">
				<?php esc_html_e('Вернуться на главную', 'm-rent'); ?>
			</a>
		</div>
	</section>
</main>

<?php get_footer();
