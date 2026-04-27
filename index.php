<?php get_header(); ?>

<main class="container mx-auto p-6">
	<h1 class="text-4xl font-bold tracking-tight">m-rent</h1>
	<p class="mt-4 text-slate-600">
		Тема <code class="rounded bg-slate-100 px-1.5 py-0.5 text-sm">m-rent</code>
		на Tailwind v4 + Vite. Активирована и готова к разработке.
	</p>

	<?php if ( have_posts() ) : ?>
		<section class="mt-10 grid gap-6 md:grid-cols-2">
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="rounded-lg border border-slate-200 p-5">
					<h2 class="text-xl font-semibold">
						<a href="<?php the_permalink(); ?>" class="hover:underline"><?php the_title(); ?></a>
					</h2>
					<div class="mt-2 text-sm text-slate-600"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</section>
	<?php endif; ?>
</main>

<?php get_footer();
