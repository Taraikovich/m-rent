/**
 * SEO-блок: плавный toggle «Подробнее / Свернуть».
 *
 * DOM (см. sections/common/seo-text.php):
 *   <div data-seo-block data-expanded="false" class="group …">
 *     <div class="relative">
 *       <div data-seo-content class="max-h-[150px] 2xl:max-h-[260px] transition-[max-height]">
 *         <p>…длинный текст…</p>
 *       </div>
 *       <div data-seo-fade class="opacity-100 group-data-[expanded=true]:opacity-0 …"></div>
 *     </div>
 *     <button data-seo-toggle data-label-more="…" data-label-less="…">…</button>
 *   </div>
 *
 * Алгоритм expand:
 *   1. Меряем `scrollHeight` контента (полная высота).
 *   2. Ставим `style.maxHeight = scrollHeight + 'px'` — CSS-transition
 *      гонит max-height от CSS-clamp до полной высоты.
 *   3. После `transitionend` снимаем inline-стиль (`'none'`), чтоб контент
 *      мог свободно расти при ресайзе.
 *
 * Алгоритм collapse:
 *   1. Контент сейчас на `maxHeight: none` — transition не сработает.
 *      Ставим explicit `scrollHeight + 'px'`, форсируем reflow,
 *      на следующий кадр снимаем inline → CSS clamp обратно включается,
 *      transition анимирует max-height вниз.
 *
 * Кнопку и оверлей прячем, если текст и так влезает в clamp-высоту.
 */

function setupBlock(block) {
	const content = block.querySelector('[data-seo-content]');
	const fade    = block.querySelector('[data-seo-fade]');
	const toggle  = block.querySelector('[data-seo-toggle]');

	if (!content || !toggle) {
		return;
	}

	const labelMore = toggle.dataset.labelMore || toggle.textContent.trim();
	const labelLess = toggle.dataset.labelLess || labelMore;

	// Если текст помещается в свёрнутую высоту — toggle не нужен.
	const collapsedPx = parseFloat(getComputedStyle(content).maxHeight);
	if (Number.isFinite(collapsedPx) && content.scrollHeight <= collapsedPx + 1) {
		toggle.hidden = true;
		if (fade) {
			fade.hidden = true;
		}
		return;
	}

	const expand = () => {
		content.style.maxHeight = content.scrollHeight + 'px';
		block.dataset.expanded = 'true';
		toggle.setAttribute('aria-expanded', 'true');
		toggle.textContent = labelLess;

		const onEnd = (event) => {
			if (event.propertyName !== 'max-height') return;
			content.removeEventListener('transitionend', onEnd);
			if (block.dataset.expanded === 'true') {
				content.style.maxHeight = 'none';
			}
		};
		content.addEventListener('transitionend', onEnd);
	};

	const collapse = () => {
		// Сначала фиксируем текущую полную высоту в px (чтоб transition имел стартовую точку),
		// форсируем reflow, затем снимаем inline-style → CSS clamp возвращает max-height.
		content.style.maxHeight = content.scrollHeight + 'px';
		// eslint-disable-next-line no-unused-expressions
		content.offsetHeight;

		requestAnimationFrame(() => {
			content.style.maxHeight = '';
			block.dataset.expanded = 'false';
			toggle.setAttribute('aria-expanded', 'false');
			toggle.textContent = labelMore;
		});
	};

	toggle.addEventListener('click', () => {
		if (block.dataset.expanded === 'true') {
			collapse();
		} else {
			expand();
		}
	});
}

document.querySelectorAll('[data-seo-block]').forEach(setupBlock);
