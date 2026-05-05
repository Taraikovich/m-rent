<?php
/**
 * Одиночная запись блога (Figma 2253:2829 / 2345:3401).
 *
 *   1. Хлебные крошки: Главная > Блог > {заголовок}.
 *   2. Шапка: заголовок + дата + лид (the_excerpt).
 *   3. Контент редактора (`the_content`) — стилизован через `.mrent-article`
 *      (см. src/main.css): h2 35/20px Heavy, p 24/14px Book, картинки 600/220px
 *      высоты, full-width, rounded-15.
 *
 * Featured-image на single-странице НЕ выводится — в дизайне 2253:2829
 * картинка идёт внутри контента между секциями (в виде image-блока редактора).
 * Featured остаётся обложкой для архивных карточек.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
?>

	<main class="bg-mrent-black pt-[60px] xl:pt-[93px] pb-[60px] xl:pb-[100px]">
		<?php get_template_part( 'sections/blog/breadcrumbs' ); ?>
		<?php get_template_part( 'sections/blog/single-header' ); ?>

		<?php if ( get_the_content() ) : ?>
			<section class="bg-mrent-black pt-[30px] xl:pt-[60px]">
				<div class="px-[15px] xl:px-[100px]">
					<article class="mrent-article max-w-[1720px] mx-auto text-mrent-white">
						<?php the_content(); ?>
					</article>
				</div>
			</section>
		<?php endif; ?>
	</main>

<?php endwhile; ?>

<?php get_footer();
