<?php

/**
 * Hero-секция главной — фото BMW + заголовок/CTA + плитка категорий.
 *
 * Контент берётся из ACF-группы «Главная», таб «Hero».
 * Если поле «Заголовок» (home_hero_title) не заполнено — секция не выводится.
 *
 * Единая раскладка с двумя режимами:
 *   • < xl  — категории-пилюли сверху, фото с градиентом-mobile, текст и CTA под фото.
 *   • ≥ xl  — фото 802px с градиентом-desktop, центрированный заголовок/CTA поверх,
 *             4 преимущества и 7 карточек категорий ниже.
 *
 * Figma: 235:447 (desktop) и 5037:7516 (mobile).
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_page_id = get_queried_object_id();
$mrent_title   = get_field('home_hero_title', $mrent_page_id);

if (! $mrent_title) {
	return;
}

$mrent_hero_image = get_field('home_hero_image', $mrent_page_id);
$mrent_lead       = get_field('home_hero_lead', $mrent_page_id);
$mrent_cta_url    = get_field('home_hero_cta_url', $mrent_page_id);
$mrent_cta_label  = get_field('home_hero_cta_label', $mrent_page_id);
$mrent_advantages = (array) get_field('home_advantages', $mrent_page_id);
$mrent_categories = (array) get_field('home_categories', $mrent_page_id);

$mrent_hero_url = $mrent_hero_image['url'] ?? '';
$mrent_hero_alt = $mrent_hero_image['alt'] ?? '';
?>

<section class="relative bg-mrent-black overflow-hidden xl:overflow-visible">
	<?php /* ─── Пилюли категорий — только мобайл (< xl) ─── */ ?>
	<div class="xl:hidden relative z-10 pt-[72px] px-[15px] flex flex-wrap gap-[6px] items-center justify-center">
		<?php foreach ($mrent_categories as $i => $cat) :
			$slug = $cat['category_slug'] ?: sanitize_title($cat['category_label']);
			$mrent_pill_width_class = match ($i) {
				0, 1, 5, 6 => 'w-[162px]',
				2, 3, 4    => 'w-[106px]',
				default    => 'w-[162px]',
			};
		?>
			<a href="<?php echo esc_attr($slug); ?>" class="<?php echo $mrent_pill_width_class; ?> bg-[#252426] hover:bg-[#2f2e30] transition-colors rounded-[15px] px-[15px] py-[12px] flex items-center justify-center text-white text-[clamp(0.875rem,0.826rem+0.217vw,1rem)] leading-[1.2] whitespace-nowrap">
				<?php echo esc_html($cat['category_label']); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<?php /* ─── Фото + текст: общий блок ───
	         На мобайле картинка идёт отдельным блоком 340px, текст ниже с -mt-[74px] (наезжает на низ фото).
	         На xl картинка абсолютно растягивается на весь контейнер (min-h 802px), а текст лежит в потоке
	         поверх (z-10) и оттолкнут вниз через pt-[457px]. */ ?>
	<div class="relative xl:min-h-[802px]">
		<div class="relative mt-[12px] h-[340px] overflow-hidden xl:absolute xl:inset-0 xl:mt-0 xl:h-auto">
			<img src="<?php echo esc_url($mrent_hero_url); ?>" alt="<?php echo esc_attr($mrent_hero_alt); ?>" class="absolute inset-x-0 bottom-0 w-full h-full object-cover object-bottom xl:inset-0 xl:size-full xl:h-full xl:object-[center_90%]" loading="eager" decoding="async" fetchpriority="high">
			<div class="absolute inset-0 mrent-hero-overlay-mobile xl:hidden"></div>
			<div class="absolute inset-0 hidden xl:block mrent-hero-overlay-desktop"></div>
		</div>

		<div class="relative z-10 -mt-[74px] pb-[20px] px-[15px] flex flex-col gap-[30px] items-center text-white xl:mt-0 xl:pt-[457px] xl:px-[100px] xl:gap-0">
			<div class="flex flex-col gap-[15px] items-center text-center xl:max-w-[1488px] xl:gap-[19px]">
				<h1 class="font-display font-[700] text-[clamp(28px,1.456rem+1.304vw,74px)] xl:text-[clamp(3.7rem,3.854vw,4.625rem)] leading-[1.2] xl:leading-[1.24]"><?php echo esc_html($mrent_title); ?></h1>
				<p class="text-[clamp(1rem,0.902rem+0.435vw,1.25rem)] xl:text-[clamp(1.5rem,1.5625vw,1.875rem)] leading-[1.2] xl:leading-[1.24]"><?php echo esc_html($mrent_lead); ?></p>
			</div>
			<a href="<?php echo esc_url($mrent_cta_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center w-full max-w-[330px] h-[55px] px-[15px] rounded-[15px] font-display font-medium text-[clamp(0.875rem,0.826rem+0.217vw,1rem)] xl:text-[clamp(1rem,1.0417vw,1.25rem)] leading-none transition-colors xl:w-[300px] xl:max-w-none xl:h-[65px] xl:px-0 xl:mt-[50px]">
				<?php echo esc_html($mrent_cta_label); ?>
			</a>
		</div>
	</div>

	<?php /* ─── 4 преимущества — только xl. Колонки равной ширины через grid. ─── */ ?>
	<div class="hidden xl:grid grid-cols-4 max-w-[1720px] mx-auto px-[100px] mt-[20px] gap-[50px] text-white text-[clamp(1.2rem,1.25vw,24px)] leading-[1.2] text-center">
		<?php foreach ($mrent_advantages as $adv) : ?>
			<p><span class="font-[700]"><?php echo esc_html($adv['advantage_bold']); ?></span> <?php echo esc_html($adv['advantage_rest']); ?></p>
		<?php endforeach; ?>
	</div>

	<?php /* ─── Карточки категорий — только xl. flex-1 + min-w-0 распределяет ширину поровну. ─── */ ?>
	<div class="hidden xl:flex max-w-[1720px] mx-auto px-[100px] mt-[80px] pb-[100px] items-stretch gap-[20px]">
		<?php foreach ($mrent_categories as $cat) :
			$slug = $cat['category_slug'] ?: sanitize_title($cat['category_label']);
			$img  = $cat['category_image'];
		?>
			<a href="<?php echo esc_attr($slug); ?>" class="bg-[#252426] hover:bg-[#2f2e30] transition-colors rounded-[15px] p-[20px] flex flex-col gap-[20px] items-center flex-1 min-w-0">
				<p class="text-white text-[clamp(0.9rem,0.9375vw,1.125rem)] leading-[1.2] text-center whitespace-nowrap"><?php echo esc_html($cat['category_label']); ?></p>
				<div class="h-[65px] w-full">
					<img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt'] ?: $cat['category_label']); ?>" class="size-full object-contain" loading="lazy" decoding="async">
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>