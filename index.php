<?php get_header(); ?>

<main class="pt-[60px] xl:pt-[93px]">
	<section class="container mx-auto px-6 py-16">
		<h1 class="text-4xl font-bold tracking-tight text-mrent-white">m-rent</h1>
		<p class="mt-4 text-mrent-white/70">
			Тема <code class="rounded bg-white/10 px-1.5 py-0.5 text-sm">m-rent</code>
			на Tailwind v4 + Vite. Шапка готова — собирай дальше.
		</p>

		<?php if ( have_posts() ) : ?>
			<section class="mt-10 grid gap-6 md:grid-cols-2">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="rounded-lg border border-white/10 p-5">
						<h2 class="text-xl font-semibold">
							<a href="<?php the_permalink(); ?>" class="hover:underline"><?php the_title(); ?></a>
						</h2>
						<div class="mt-2 text-sm text-mrent-white/60"><?php the_excerpt(); ?></div>
					</article>
				<?php endwhile; ?>
			</section>
		<?php endif; ?>
	</section>
</main>

<?php get_footer();
