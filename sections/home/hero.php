<?php

/**
 * Hero-секция главной — фото BMW + заголовок/CTA + плитка категорий.
 *
 * Контент берётся из ACF-группы «Главная», таб «Hero».
 * Если поле «Заголовок» (home_hero_title) не заполнено — секция не выводится.
 *
 * Две раскладки в одной секции:
 *   • < 2xl  — категории-пилюли сверху, фото с двойным градиентом, текст и CTA.
 *   • ≥ 2xl  — фото 802px + центрированный заголовок/CTA + 4 преимущества + 7 карточек категорий.
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

<?php /* ─── Мобильная раскладка (< 2xl) ─── */ ?>
<section class="2xl:hidden relative bg-mrent-black overflow-hidden">
	<?php /* Категории-пилюли. pt-[72px] = высота фикс-хедера (60) + 12px воздуха.
	         Ширины зашиты по позиции (162/106) — по дизайну 2316:3361. С gap-[6px]
	         получаются ряды 2-3-2 в 330px-контейнере (360 - 2×15 паддингов).
	         Классы записаны литералами через match, чтобы Tailwind v4 их видел. */ ?>
	<div class="relative z-10 pt-[72px] px-[15px] flex flex-wrap gap-[6px] items-center justify-center">
		<?php foreach ($mrent_categories as $i => $cat) :
			$slug = $cat['category_slug'] ?: sanitize_title($cat['category_label']);
			$mrent_pill_width_class = match ($i) {
				0, 1, 5, 6 => 'w-[162px]',
				2, 3, 4    => 'w-[106px]',
				default    => 'w-[162px]',
			};
		?>
			<a href="<?php echo esc_attr($slug); ?>" class="<?php echo $mrent_pill_width_class; ?> bg-[#252426] hover:bg-[#2f2e30] transition-colors rounded-[15px] px-[15px] py-[12px] flex items-center justify-center text-white text-[14px] leading-[1.2] whitespace-nowrap">
				<?php echo esc_html($cat['category_label']); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<?php /* Картинка + оверлей — отдельный блок между плитками и текстом.
	         Картинка масштабируется на 200% высоты и прижимается к низу — верхнюю
	         половину обрезает overflow-hidden. Текст ниже подтянут на ~10% высоты
	         блока (-mt-[74px]) и слегка заходит на нижнюю кромку картинки. */ ?>
	<div class="relative mt-[12px] h-[340px] overflow-hidden">
		<img src="<?php echo esc_url($mrent_hero_url); ?>" alt="<?php echo esc_attr($mrent_hero_alt); ?>" class="absolute inset-x-0 bottom-0 w-full h-[150%] object-cover object-bottom" loading="eager" decoding="async" fetchpriority="high">
		<div class="absolute inset-0 mrent-hero-overlay-mobile"></div>
	</div>

	<div class="relative z-10 -mt-[74px] pb-[30px] px-[15px] flex flex-col gap-[30px] items-center text-white">
		<div class="flex flex-col gap-[15px] items-center text-center max-w-[330px]">
			<h1 class="font-display font-[700] text-[28px] leading-[1.2]"><?php echo esc_html($mrent_title); ?></h1>
			<p class="text-[16px] leading-[1.2]"><?php echo esc_html($mrent_lead); ?></p>
		</div>
		<a href="<?php echo esc_url($mrent_cta_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center w-full max-w-[330px] h-[55px] px-[15px] rounded-[15px] font-display font-medium text-[14px] leading-none transition-colors">
			<?php echo esc_html($mrent_cta_label); ?>
		</a>
	</div>
</section>

<?php /* ─── Десктопная раскладка (≥ 2xl) ─── */ ?>
<section class="hidden 2xl:block relative bg-mrent-black">
	<?php /* Hero-фото в полную ширину 1920+, высота 802px по дизайну.
	         object-position смещает кадр чуть ниже середины — авто оказываются по центру. */ ?>
	<div class="relative min-h-[802px]">
		<div class="absolute inset-0 overflow-hidden">
			<img src="<?php echo esc_url($mrent_hero_url); ?>" alt="<?php echo esc_attr($mrent_hero_alt); ?>" class="absolute inset-0 size-full object-cover object-[center_90%]" loading="eager" decoding="async" fetchpriority="high">
			<div class="absolute inset-0 mrent-hero-overlay-desktop"></div>
		</div>
		<div class="relative pt-[457px] pb-[30px] flex flex-col gap-[19px] items-center text-center text-white px-[100px]">
			<h1 class="font-display font-[700] text-[74px] leading-[1.24] max-w-[1488px]"><?php echo esc_html($mrent_title); ?></h1>
			<p class="text-[30px] leading-[1.24] max-w-[1488px]"><?php echo esc_html($mrent_lead); ?></p>
			<a href="<?php echo esc_url($mrent_cta_url); ?>" class="bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center w-[300px] h-[65px] rounded-[15px] font-display font-medium text-[20px] leading-none transition-colors mt-[55px]">
				<?php echo esc_html($mrent_cta_label); ?>
			</a>
		</div>
	</div>

	<?php /* 4 преимущества в строку. Ширины блоков подобраны по Figma 236:517 — фиксированы по позиции.
	         Классы записаны литералами через match, чтобы Tailwind v4 их видел при сканировании исходников. */ ?>
	<div class="max-w-[1720px] mx-auto px-[100px] mt-[70px] flex items-start justify-between gap-[40px] text-white text-[24px] leading-[1.2] text-center">
		<?php foreach ($mrent_advantages as $i => $adv) :
			$mrent_adv_width_class = match ($i) {
				0       => 'w-[353px]',
				1       => 'w-[318px]',
				2       => 'w-[260px]',
				3       => 'w-[202px]',
				default => 'w-[260px]',
			};
		?>
			<p class="<?php echo $mrent_adv_width_class; ?> shrink-0"><span class="font-[700]"><?php echo esc_html($adv['advantage_bold']); ?></span> <?php echo esc_html($adv['advantage_rest']); ?></p>
		<?php endforeach; ?>
	</div>

	<?php /* Карточки категорий: бренд-серый #252426 + название сверху + силуэт.
	         flex-1 + min-w-0 распределяет ширину поровну, картинка тянется по ширине карточки. */ ?>
	<div class="max-w-[1720px] mx-auto px-[100px] mt-[80px] pb-[100px] flex items-stretch gap-[20px]">
		<?php foreach ($mrent_categories as $cat) :
			$slug = $cat['category_slug'] ?: sanitize_title($cat['category_label']);
			$img  = $cat['category_image'];
		?>
			<a href="<?php echo esc_attr($slug); ?>" class="bg-[#252426] hover:bg-[#2f2e30] transition-colors rounded-[15px] p-[20px] flex flex-col gap-[20px] items-center flex-1 min-w-0">
				<p class="text-white text-[18px] leading-[1.2] text-center whitespace-nowrap"><?php echo esc_html($cat['category_label']); ?></p>
				<div class="h-[65px] w-full">
					<img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt'] ?: $cat['category_label']); ?>" class="size-full object-contain" loading="lazy" decoding="async">
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>