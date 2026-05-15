<?php

/**
 * «Авто под заказ»: блок с тремя табами под фото-каруселью.
 *
 * Figma:
 *   • desktop 2989:7346 — три pill-таба 330×65 с gap-20, активный bg-white текст #1E1D1F.
 *     Таб-панель: 2 колонки (659 + 972) с gap-89. Левая — H1 74 / lead 24 / cost-line.
 *     Правая — карточка #252426 с тайминами и жёлтым CTA, плюс фото 553×383 справа absolute.
 *   • mobile  5203:7377 — нативный <select> 330×55; ниже текст блок (H1 28 / lead 16 / cost 16),
 *     ниже карточка с таймингами + CTA, и фото 330×330 ниже.
 *
 * Таб-логика — `[data-mrent-services-tabbed]` (см. src/services.js).
 * У панели нет внутреннего swiper'а → init просто переключает `hidden` + `aria-current`.
 *
 * Сейчас контент есть только у первого таба; для остальных — placeholder, подключим позже.
 */

if (! defined('ABSPATH')) {
	exit;
}

$mrent_title      = (string) get_field('car_import_intro_title');
$mrent_lead       = (string) get_field('car_import_intro_lead');
$mrent_cost       = (string) mrent_convert_usd_in_text(get_field('car_import_tab1_cost'));
$mrent_card_title = (string) get_field('car_import_tab1_card_title');
$mrent_card_items = (array)  get_field('car_import_tab1_card_items');
$mrent_cta_text   = (string) get_field('car_import_tab1_cta_text');
$mrent_cta_url    = (string) get_field('car_import_tab1_cta_url');
$mrent_image      = get_field('car_import_tab1_image');

$mrent_image_url = '';
$mrent_image_alt = '';
if (is_array($mrent_image) && ! empty($mrent_image['url'])) {
	$mrent_image_url = (string) $mrent_image['url'];
	$mrent_image_alt = (string) ($mrent_image['alt'] ?? '');
}

$mrent_card_title = $mrent_card_title !== '' ? $mrent_card_title : 'Сроки:';
$mrent_cta_text   = $mrent_cta_text !== ''   ? $mrent_cta_text   : 'Заказать услугу';
$mrent_cta_url    = $mrent_cta_url !== ''    ? $mrent_cta_url    : '#';

// Таб 2: страны поставки. Каждый элемент репитера → один слайд.
$mrent_tab2_raw    = (array) get_field('car_import_tab2_slides');
$mrent_tab2_slides = [];
foreach ($mrent_tab2_raw as $mrent_slide) {
	$mrent_slide_title       = (string) ($mrent_slide['title'] ?? '');
	$mrent_slide_description = (string) ($mrent_slide['description'] ?? '');
	$mrent_slide_b_title     = (string) ($mrent_slide['benefits_title'] ?? '');
	$mrent_slide_b_items_raw = (string) ($mrent_slide['benefits_items'] ?? '');
	$mrent_slide_image       = $mrent_slide['image'] ?? null;

	if ($mrent_slide_title === '' && $mrent_slide_description === '' && $mrent_slide_b_items_raw === '') {
		continue;
	}

	$mrent_slide_b_items = array_values(array_filter(
		array_map('trim', preg_split('/\r?\n/', $mrent_slide_b_items_raw)),
		static fn($line) => $line !== ''
	));

	$mrent_slide_image_url = '';
	$mrent_slide_image_alt = '';
	if (is_array($mrent_slide_image) && ! empty($mrent_slide_image['url'])) {
		$mrent_slide_image_url = (string) $mrent_slide_image['url'];
		$mrent_slide_image_alt = (string) ($mrent_slide_image['alt'] ?? '');
	}

	$mrent_tab2_slides[] = [
		'title'          => $mrent_slide_title,
		'description'    => $mrent_slide_description,
		'image_url'      => $mrent_slide_image_url,
		'image_alt'      => $mrent_slide_image_alt,
		'benefits_title' => $mrent_slide_b_title,
		'benefits_items' => $mrent_slide_b_items,
	];
}

// Таб 3: FAQ-аккордеон.
$mrent_tab3_title    = (string) get_field('car_import_tab3_title');
$mrent_tab3_raw      = (array)  get_field('car_import_tab3_items');
$mrent_tab3_title    = $mrent_tab3_title !== '' ? $mrent_tab3_title : 'Часто задаваемые вопросы';

$mrent_tab3_items = [];
foreach ($mrent_tab3_raw as $mrent_qa) {
	$mrent_q = (string) ($mrent_qa['question'] ?? '');
	$mrent_a = (string) ($mrent_qa['answer'] ?? '');
	if ($mrent_q === '') {
		continue;
	}
	$mrent_tab3_items[] = ['question' => $mrent_q, 'answer' => $mrent_a];
}

// Демо-вопросы из дизайна 2989:7612 на случай пустого ACF.
if (! $mrent_tab3_items) {
	$mrent_tab3_demo = [
		'Какие страны доступны для подбора?',
		'Сколько стоит услуга подбора и сопровождения?',
		'Что входит в услугу?',
		'Сколько занимает процесс?',
		'Можно ли подобрать BMW под конкретную комплектацию?',
		'Вы работаете только с BMW?',
		'Можно ли привезти автомобиль в Беларусь или Россию?',
		'Финальный бюджет известен сразу?',
	];
	foreach ($mrent_tab3_demo as $mrent_demo_q) {
		$mrent_tab3_items[] = ['question' => $mrent_demo_q, 'answer' => 'Ответ будет добавлен позже.'];
	}
}

// Если ACF пустой — показываем демо-слайд из дизайна, чтобы вкладка не выглядела
// «сломанной» сразу после деплоя. Заполнение ACF полностью замещает фолбэк.
if (! $mrent_tab2_slides) {
	$mrent_tab2_slides[] = [
		'title'          => 'Европа и европейские аукционы',
		'description'    => 'Оригинальный пробег, богатые комплектации, использование качественного топлива и профессиональный сервис. А покупка авто на европейском аукционе позволит приобрести машину с прозрачной историей, высоким качеством обслуживания и по цене, которая часто оказывается существенно ниже рынка РБ и РФ (экономия может достигать 20-60% в зависимости от класса авто).',
		'image_url'      => '',
		'image_alt'      => '',
		'benefits_title' => 'Преимущества Европы',
		'benefits_items' => [
			'большой выбор автомобилей',
			'понятные комплектации и спецификации',
			'идеальное техническое состояние и пробег',
			'удобно для тех, кто ищет немецкий премиум',
			'хороший вариант для личного и семейного использования',
		],
	];
}

// Названия и порядок табов фиксированы по дизайну.
$mrent_tabs = [
	['id' => 'description', 'name' => 'Описание услуги'],
	['id' => 'countries',   'name' => 'Страны поставки'],
	['id' => 'faq',         'name' => 'Вопрос-ответ'],
];
?>

<section class="bg-mrent-black px-[15px] xl:px-[100px] pt-[30px] xl:pt-[60px]">
	<div class="max-w-[1720px] mx-auto flex flex-col gap-[30px] xl:gap-[60px]" data-mrent-services-tabbed>

		<?php /* Desktop: ряд из трёх pill-табов */ ?>
		<div class="hidden xl:flex gap-[20px]">
			<?php foreach ($mrent_tabs as $mrent_tab) : ?>
				<button
					type="button"
					data-mrent-tab-trigger
					data-mrent-tab-id="<?php echo esc_attr($mrent_tab['id']); ?>"
					class="bg-[#252426] text-mrent-white aria-[current=true]:bg-mrent-white aria-[current=true]:text-mrent-black flex items-center justify-center rounded-[15px] h-[65px] w-[330px] px-[15px] font-[500] text-[20px] whitespace-nowrap transition-colors cursor-pointer">
					<?php echo esc_html($mrent_tab['name']); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php /* Mobile: <select> с шевроном */ ?>
		<div class="xl:hidden relative">
			<select
				data-mrent-tab-select
				class="appearance-none bg-white text-mrent-black w-full h-[55px] rounded-[15px] pl-[15px] pr-[40px] font-[500] text-[14px] cursor-pointer">
				<?php foreach ($mrent_tabs as $mrent_tab) : ?>
					<option value="<?php echo esc_attr($mrent_tab['id']); ?>"><?php echo esc_html($mrent_tab['name']); ?></option>
				<?php endforeach; ?>
			</select>
			<svg class="absolute right-[15px] top-1/2 -translate-y-1/2 pointer-events-none w-[10px] h-[10px] text-mrent-black" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polyline points="1 3 5 8 9 3" />
			</svg>
		</div>

		<?php /* ───── Tab 1: Описание услуги ───── */ ?>
		<div data-mrent-tab-panel data-mrent-tab-id="description">
			<div class="flex flex-col gap-[30px] xl:flex-row xl:items-stretch xl:gap-[40px] 2xl:gap-[89px]">

				<?php /* Левая колонка: H1 + lead сверху, строка стоимости — прижата к низу
				         (xl:justify-between), чтобы цена сидела по нижней кромке правой колонки. */ ?>
				<div class="flex flex-col gap-[30px] xl:gap-[60px] xl:justify-between xl:basis-[659px] xl:shrink xl:min-w-0">
					<?php if ($mrent_title !== '' || $mrent_lead !== '') : ?>
						<div class="flex flex-col gap-[15px] xl:gap-[30px] text-mrent-white leading-[1.2]">
							<?php if ($mrent_title !== '') : ?>
								<h2 class="font-display font-[700] text-[28px] xl:text-[56px] 2xl:text-[74px]">
									<?php echo esc_html($mrent_title); ?>
								</h2>
							<?php endif; ?>
							<?php if ($mrent_lead !== '') : ?>
								<p class="font-[400] text-[16px] xl:text-[24px]">
									<?php echo esc_html($mrent_lead); ?>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ($mrent_cost !== '') : ?>
						<div class="flex items-center gap-[15px] xl:gap-[20px] text-mrent-white">
							<?php /* Иконка bi:cash-coin из Figma 2989:7290 — жёлтая (#FFD700), 16/30. */ ?>
							<svg class="w-[16px] h-[16px] xl:w-[30px] xl:h-[30px] shrink-0 text-mrent-yellow" viewBox="0 0 30 30" fill="currentColor" aria-hidden="true">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M20.625 28.125C22.6141 28.125 24.5218 27.3348 25.9283 25.9283C27.3348 24.5218 28.125 22.6141 28.125 20.625C28.125 18.6359 27.3348 16.7282 25.9283 15.3217C24.5218 13.9152 22.6141 13.125 20.625 13.125C18.6359 13.125 16.7282 13.9152 15.3217 15.3217C13.9152 16.7282 13.125 18.6359 13.125 20.625C13.125 22.6141 13.9152 24.5218 15.3217 25.9283C16.7282 27.3348 18.6359 28.125 20.625 28.125ZM30 20.625C30 23.1114 29.0123 25.496 27.2541 27.2541C25.496 29.0123 23.1114 30 20.625 30C18.1386 30 15.754 29.0123 13.9959 27.2541C12.2377 25.496 11.25 23.1114 11.25 20.625C11.25 18.1386 12.2377 15.754 13.9959 13.9959C15.754 12.2377 18.1386 11.25 20.625 11.25C23.1114 11.25 25.496 12.2377 27.2541 13.9959C29.0123 15.754 30 18.1386 30 20.625Z" />
								<path d="M17.6972 22.395C17.7853 23.5125 18.6684 24.3825 20.2528 24.4875V25.3125H20.9559V24.4819C22.5966 24.3675 23.5547 23.49 23.5547 22.2188C23.5547 21.06 22.8234 20.4638 21.5109 20.1562L20.9559 20.025V17.775C21.6609 17.8556 22.1072 18.24 22.2141 18.7725H23.4478C23.3597 17.6944 22.4353 16.8525 20.9559 16.7606V15.9375H20.2528V16.7812C18.8522 16.9181 17.8997 17.76 17.8997 18.9525C17.8997 20.0062 18.6084 20.6775 19.7878 20.9512L20.2528 21.0656V23.4506C19.5328 23.3419 19.0547 22.9444 18.9478 22.395H17.6972ZM20.2472 19.8563C19.5553 19.6969 19.1803 19.3687 19.1803 18.8775C19.1803 18.3262 19.5853 17.9137 20.2528 17.7937V19.8563H20.2472ZM21.0572 21.255C21.8991 21.45 22.2853 21.765 22.2853 22.3219C22.2853 22.9575 21.8034 23.3925 20.9559 23.4731V21.2325L21.0572 21.255Z" />
								<path d="M1.875 0C1.37772 0 0.900806 0.197544 0.549175 0.549175C0.197544 0.900806 0 1.37772 0 1.875L0 16.875C0 17.3723 0.197544 17.8492 0.549175 18.2008C0.900806 18.5525 1.37772 18.75 1.875 18.75H9.53063C9.64063 18.1038 9.80188 17.4787 10.0144 16.875H5.625C5.625 15.8804 5.22991 14.9266 4.52665 14.2233C3.82339 13.5201 2.86956 13.125 1.875 13.125V5.625C2.86956 5.625 3.82339 5.22991 4.52665 4.52665C5.22991 3.82339 5.625 2.86956 5.625 1.875H24.375C24.375 2.86956 24.7701 3.82339 25.4734 4.52665C26.1766 5.22991 27.1304 5.625 28.125 5.625V12.24C28.8375 12.8775 29.4694 13.605 30 14.4038V1.875C30 1.37772 29.8025 0.900806 29.4508 0.549175C29.0992 0.197544 28.6223 0 28.125 0L1.875 0Z" />
								<path d="M18.7463 9.53063L18.75 9.375C18.7496 8.72923 18.5825 8.09451 18.2648 7.5323C17.9471 6.97009 17.4896 6.49946 16.9366 6.16597C16.3836 5.83249 15.7538 5.64745 15.1083 5.6288C14.4628 5.61014 13.8235 5.75849 13.2521 6.05948C12.6808 6.36047 12.1969 6.80389 11.8472 7.34681C11.4976 7.88973 11.2941 8.51374 11.2564 9.15841C11.2187 9.80307 11.3481 10.4465 11.6322 11.0265C11.9162 11.6065 12.3451 12.1032 12.8775 12.4688C14.4982 10.9278 16.5415 9.90492 18.7463 9.53063Z" />
							</svg>
							<p class="leading-[1.2] text-[16px] xl:text-[28px] 2xl:text-[35px]">
								<span class="font-[800]"><?php esc_html_e('Стоимость услуги', 'm-rent'); ?></span><span class="font-[400]"> — <?php echo esc_html($mrent_cost); ?></span>
							</p>
						</div>
					<?php endif; ?>
				</div>

				<?php /* Правая колонка: на десктопе фото абсолютно сверху-справа, карточка слева. */ ?>
				<div class="flex flex-col gap-[30px] xl:block xl:relative xl:basis-[972px] xl:shrink xl:min-w-0 xl:self-stretch">

					<?php /* Карточка сроков. На мобайле — первой, на десктопе — поверх фото. */ ?>
					<div class="order-1 xl:order-none relative z-10 bg-[#252426] border border-[#302f31] rounded-[15px] p-[25px] xl:p-[30px] flex flex-col gap-[30px] xl:gap-[60px] xl:w-[380px] 2xl:w-[443px] xl:absolute xl:left-0 xl:top-1/2 xl:-translate-y-1/2">
						<div class="flex flex-col gap-[20px]">
							<p class="font-[800] text-[20px] xl:text-[28px] 2xl:text-[35px] leading-[1.2] text-mrent-white">
								<?php echo esc_html($mrent_card_title); ?>
							</p>

							<?php if (! empty($mrent_card_items)) : ?>
								<ul class="flex flex-col gap-[10px]">
									<?php foreach ($mrent_card_items as $mrent_item) :
										$mrent_item_text = is_array($mrent_item) ? (string) ($mrent_item['text'] ?? '') : (string) $mrent_item;
										if ($mrent_item_text === '') {
											continue;
										}
									?>
										<li class="flex items-center gap-[15px] text-mrent-white text-[14px] xl:text-[24px] leading-[1.2]">
											<span class="size-[6px] shrink-0 rounded-full bg-mrent-white" aria-hidden="true"></span>
											<span><?php echo esc_html($mrent_item_text); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>

						<a
							href="<?php echo esc_url($mrent_cta_url); ?>"
							class="bg-mrent-yellow hover:bg-[#FFF831] flex items-center justify-center rounded-[15px] h-[50px] xl:h-[55px] px-[15px] text-mrent-black font-[500] text-[14px] xl:text-[20px] whitespace-nowrap transition-colors">
							<?php echo esc_html($mrent_cta_text); ?>
						</a>
					</div>

					<?php /* Фото. Mobile: ниже карточки, квадрат на всю ширину.
					         Desktop: absolute left-419, тянется по высоте карточки (top-0 bottom-0),
					         чтобы фото и карточка были одной высоты. */ ?>
					<?php if ($mrent_image_url !== '') : ?>
						<div class="order-2 xl:order-none rounded-[15px] overflow-hidden xl:absolute xl:top-0 xl:bottom-0 xl:right-0 xl:left-[356px] 2xl:left-[419px]">
							<img
								src="<?php echo esc_url($mrent_image_url); ?>"
								alt="<?php echo esc_attr($mrent_image_alt); ?>"
								class="w-full aspect-square xl:aspect-auto xl:w-full xl:h-full object-cover"
								loading="lazy"
								decoding="async">
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php /* ───── Tab 2: Страны поставки ─────
		         Figma 2989:7357 desktop / 3004:9366 mobile.
		         Свайпер по странам: 3 колонки на десктопе (описание → фото → преимущества),
		         одна колонка на мобайле (описание + преимущества; фото скрыто).
		         Init — `[data-mrent-car-import-countries]` в src/cars.js. */ ?>
		<div data-mrent-tab-panel data-mrent-tab-id="countries" hidden>
			<div data-mrent-car-import-countries class="flex flex-col gap-[30px] xl:gap-[60px]">

				<div class="mrent-car-import-countries-swiper swiper w-full min-w-0">
					<div class="swiper-wrapper">
						<?php foreach ($mrent_tab2_slides as $mrent_slide) : ?>
							<div class="swiper-slide !h-auto">
								<div class="flex flex-col gap-[30px] xl:flex-row xl:gap-[30px] 2xl:gap-[60px] xl:items-start">

									<?php /* Описание */ ?>
									<div class="flex flex-col gap-[15px] xl:gap-[30px] leading-[1.2] text-mrent-white xl:flex-1 xl:min-w-0">
										<?php if ($mrent_slide['title'] !== '') : ?>
											<h3 class="font-[800] text-[20px] xl:text-[28px] 2xl:text-[35px]"><?php echo esc_html($mrent_slide['title']); ?></h3>
										<?php endif; ?>
										<?php if ($mrent_slide['description'] !== '') : ?>
											<p class="font-[400] text-[14px] xl:text-[24px]"><?php echo esc_html($mrent_slide['description']); ?></p>
										<?php endif; ?>
									</div>

									<?php /* Фото — только на десктопе (370×275). */ ?>
									<?php if ($mrent_slide['image_url'] !== '') : ?>
										<div class="hidden xl:block xl:w-[280px] xl:h-[210px] 2xl:w-[370px] 2xl:h-[275px] xl:shrink-0 rounded-[15px] overflow-hidden">
											<img
												src="<?php echo esc_url($mrent_slide['image_url']); ?>"
												alt="<?php echo esc_attr($mrent_slide['image_alt']); ?>"
												class="size-full object-cover"
												loading="lazy"
												decoding="async">
										</div>
									<?php endif; ?>

									<?php /* Преимущества */ ?>
									<?php if ($mrent_slide['benefits_title'] !== '' || ! empty($mrent_slide['benefits_items'])) : ?>
										<div class="flex flex-col gap-[15px] xl:gap-[20px] leading-[1.2] text-mrent-white xl:flex-1 xl:min-w-0">
											<?php if ($mrent_slide['benefits_title'] !== '') : ?>
												<h3 class="font-[800] text-[20px] xl:text-[28px] 2xl:text-[35px]"><?php echo esc_html($mrent_slide['benefits_title']); ?></h3>
											<?php endif; ?>
											<?php if (! empty($mrent_slide['benefits_items'])) : ?>
												<ul class="flex flex-col gap-[10px]">
													<?php foreach ($mrent_slide['benefits_items'] as $mrent_item) : ?>
														<li class="flex items-center gap-[10px] xl:gap-[15px] text-[14px] xl:text-[24px]">
															<span class="size-[4px] xl:size-[6px] shrink-0 rounded-full bg-mrent-white" aria-hidden="true"></span>
															<span><?php echo esc_html($mrent_item); ?></span>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									<?php endif; ?>

								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if (count($mrent_tab2_slides) > 1) : ?>
					<?php /* Навигация: жёлтые кнопки 55×55 + полосочная пагинация. */ ?>
					<div class="flex items-center justify-between gap-[15px]">
						<button
							type="button"
							class="mrent-car-import-countries-prev bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center rounded-[15px] size-[55px] shrink-0 cursor-pointer transition-colors"
							aria-label="<?php esc_attr_e('Предыдущая страна', 'm-rent'); ?>">
							<svg class="w-[8px] h-[14px]" viewBox="0 0 8 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<polyline points="7 1 1 7 7 13" />
							</svg>
						</button>

						<div class="mrent-car-import-countries-pagination flex items-center justify-center gap-[10px] flex-1"></div>

						<button
							type="button"
							class="mrent-car-import-countries-next bg-mrent-yellow hover:bg-[#FFF831] text-mrent-black flex items-center justify-center rounded-[15px] size-[55px] shrink-0 cursor-pointer transition-colors"
							aria-label="<?php esc_attr_e('Следующая страна', 'm-rent'); ?>">
							<svg class="w-[8px] h-[14px]" viewBox="0 0 8 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<polyline points="1 1 7 7 1 13" />
							</svg>
						</button>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php /* ───── Tab 3: Вопрос-ответ ─────
		         Figma 2989:7537 desktop / 3021:8188 mobile.
		         Аккордеон на нативных <details>: клик по summary раскрывает ответ,
		         иконка-шеврон поворачивается на 180° через CSS (см. .mrent-faq-item
		         в src/main.css). JS не нужен. */ ?>
		<div data-mrent-tab-panel data-mrent-tab-id="faq" hidden>
			<div class="flex flex-col gap-[30px] xl:gap-[60px]">

				<h2 class="font-display font-[700] text-[28px] xl:text-[74px] leading-[1.2] text-mrent-white">
					<?php echo esc_html($mrent_tab3_title); ?>
				</h2>

				<div class="flex flex-col gap-[10px] xl:gap-[20px]">
					<?php foreach ($mrent_tab3_items as $mrent_qa) : ?>
						<details class="mrent-faq-item bg-[#252426] border border-[#302f31] rounded-[15px]">
							<summary class="flex items-center justify-between gap-[15px] xl:gap-[20px] p-[20px] xl:p-[40px] cursor-pointer select-none">
								<h3 class="font-[800] text-[18px] xl:text-[35px] leading-[1.2] text-mrent-white">
									<?php echo esc_html($mrent_qa['question']); ?>
								</h3>
								<span class="mrent-faq-icon shrink-0 flex items-center justify-center bg-mrent-yellow rounded-[8px] size-[25px] xl:size-[36px] text-mrent-black">
									<svg class="w-[10px] h-[10px] xl:w-[14px] xl:h-[14px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
										<path d="M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6z" />
									</svg>
								</span>
							</summary>
							<?php if ($mrent_qa['answer'] !== '') : ?>
								<div class="px-[20px] xl:px-[40px] pb-[20px] xl:pb-[40px] pt-0 text-mrent-white font-[400] text-[14px] xl:text-[24px] leading-[1.4]">
									<?php echo wp_kses_post($mrent_qa['answer']); ?>
								</div>
							<?php endif; ?>
						</details>
					<?php endforeach; ?>
				</div>

			</div>
		</div>

	</div>
</section>