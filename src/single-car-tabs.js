/**
 * Single car: переключение табов «Доп. инфо» + моб. спойлер «Подробнее»
 * в табе «Характеристики».
 *
 * DOM (см. sections/cars/single-tabs.php):
 *   <section data-car-tabs>
 *     <details data-car-tabs-mobile>      ← моб. дропдаун: <summary> + <ul>
 *       <summary>… <span data-mobile-active-label>Активный таб</span> …</summary>
 *       <ul><li><button data-tab="…">…</button></li>…</ul>
 *     </details>
 *     <div>… desktop pills: <button data-tab="…" aria-selected> …</div>
 *     <div data-tab-panel="services">…</div>
 *     <div data-tab-panel="specifications" hidden>
 *       …
 *       <div data-spec-extra hidden>…</div>
 *       <div data-spec-extra hidden>…</div>
 *       <button data-spec-toggle data-label-more=… data-label-less=…>Подробнее</button>
 *     </div>
 *   </section>
 *
 * Активное состояние:
 *   • Пилюли (desktop + mobile list) — отличаются классами bg/text, `aria-selected`.
 *   • Панели — атрибут `hidden` снят у активной, выставлен у остальных.
 *   • При клике на пункт моб. дропдауна — `<details>` закрываем вручную.
 */

document.querySelectorAll('[data-car-tabs]').forEach((root) => {
	const tabButtons = root.querySelectorAll('[data-tab]');
	const panels     = root.querySelectorAll('[data-tab-panel]');
	const mobile     = root.querySelector('[data-car-tabs-mobile]');
	const mobLabel   = root.querySelector('[data-mobile-active-label]');

	const setActive = (key) => {
		tabButtons.forEach((btn) => {
			const active = btn.dataset.tab === key;
			btn.setAttribute('aria-selected', active ? 'true' : 'false');
			// На desktop активный — белый. Мобильные пункты в дропдауне
			// без визуального состояния «выбран» (после клика список схлопывается).
			if (btn.closest('[data-car-tabs-mobile]')) return;
			btn.classList.toggle('bg-mrent-white', active);
			btn.classList.toggle('text-mrent-black', active);
			btn.classList.toggle('bg-[#252426]', !active);
			btn.classList.toggle('text-mrent-white', !active);
		});
		panels.forEach((p) => {
			if (p.dataset.tabPanel === key) p.removeAttribute('hidden');
			else                            p.setAttribute('hidden', '');
		});
		if (mobLabel) {
			const btn = root.querySelector(`[data-car-tabs-mobile] [data-tab="${key}"]`);
			if (btn) mobLabel.textContent = btn.textContent.trim();
		}
		if (mobile && mobile.open) mobile.open = false;
	};

	tabButtons.forEach((btn) => {
		btn.addEventListener('click', () => setActive(btn.dataset.tab));
	});

	// «Подробнее» в табе «Характеристики» (mobile).
	root.querySelectorAll('[data-spec-toggle]').forEach((toggle) => {
		const panel  = toggle.closest('[data-tab-panel]');
		const extras = panel ? panel.querySelectorAll('[data-spec-extra]') : [];
		toggle.addEventListener('click', () => {
			const expanded = toggle.getAttribute('aria-expanded') === 'true';
			const next     = !expanded;
			toggle.setAttribute('aria-expanded', next ? 'true' : 'false');
			// extras всегда display:flex; `max-xl:hidden` прячет их только ниже xl.
			// Снимаем/возвращаем именно его — на десктопе класс не действует и
			// колонки видны независимо от состояния тоггла.
			extras.forEach((el) => el.classList.toggle('max-xl:hidden', !next));
			toggle.textContent = next
				? (toggle.dataset.labelLess || 'Свернуть')
				: (toggle.dataset.labelMore || 'Подробнее');
		});
	});
});
