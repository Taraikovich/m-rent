<?php

/**
 * Дефолтный шаблон страницы.
 *
 *   1. Хлебные крошки: Главная > {заголовок}.
 *   2. Заголовок + контент стандартного редактора (`the_content`),
 *      стилизованный через `.mrent-article` (см. src/main.css).
 *   3. Общая секция «Есть вопросы?» (sections/common/contact).
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

while (have_posts()) :
	the_post();
?>

	<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[80px] xl:pb-[100px]">

		<nav class="bg-mrent-black pt-[20px] xl:pt-[40px]" aria-label="<?php esc_attr_e('Хлебные крошки', 'm-rent'); ?>">
			<div class="px-[15px] xl:px-[100px]">
				<ol class="max-w-[1720px] mx-auto flex flex-wrap items-center gap-[10px] text-[16px] xl:text-[24px] leading-[1.2]">
					<li class="flex items-center gap-[10px]">
						<a href="<?php echo esc_url(home_url('/')); ?>" class="text-mrent-white hover:text-mrent-yellow transition-colors font-[400]">
							<?php esc_html_e('Главная', 'm-rent'); ?>
						</a>
						<svg class="shrink-0 w-[7px] h-[14px] xl:w-[9px] xl:h-[18px] text-mrent-white/70" viewBox="0 0 9 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="1 1 8 9 1 17" />
						</svg>
					</li>
					<li class="flex items-center gap-[10px]">
						<span class="text-mrent-yellow font-[600]"><?php echo esc_html(get_the_title()); ?></span>
					</li>
				</ol>
			</div>
		</nav>

		<section class="bg-mrent-black px-[15px] xl:px-[100px] mt-[40px] xl:mt-[80px]">
			<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px] text-mrent-white">
				<h1 class="font-[700] text-[28px] xl:text-[74px] leading-[1.2] w-full">
					<?php the_title(); ?>
				</h1>

				<?php if (get_the_content()) : ?>
					<article class="mrent-page-content w-full">
						<?php the_content(); ?>
					</article>
				<?php endif; ?>
			</div>
		</section>

		<?php get_template_part('sections/common/contact'); ?>

	</main>

<?php endwhile; ?>

<?php get_footer();
