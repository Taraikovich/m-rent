/**
 * Поведение шапки m-rent.
 *
 *   • Бургер открывает/закрывает мобильный оверлей `[data-mrent-mobile-menu]`.
 *   • Сам пункт меню — обычная ссылка, навигирует по href.
 *     Кнопка-шеврон `.mrent-submenu-toggle` рядом со ссылкой переключает раскрытие
 *     подменю (десктоп-топ + любые уровни внутри dropdown'а / мобайла).
 *   • Клик вне открытого десктоп-топ-подменю — закрывает его.
 *   • При переходе на десктопную ширину — оверлей принудительно закрывается.
 */

const DESKTOP_BREAKPOINT = 1280; // совпадает с Tailwind `xl:`

function initBurger() {
	const burger = document.querySelector('[data-mrent-burger]');
	const close  = document.querySelector('[data-mrent-burger-close]');
	const panel  = document.querySelector('[data-mrent-mobile-menu]');
	if (!burger || !panel) return;

	const open = () => {
		panel.classList.add('is-open');
		panel.setAttribute('aria-hidden', 'false');
		burger.setAttribute('aria-expanded', 'true');
		document.body.classList.add('overflow-hidden');
	};
	const hide = () => {
		panel.classList.remove('is-open');
		panel.setAttribute('aria-hidden', 'true');
		burger.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('overflow-hidden');
	};

	burger.addEventListener('click', () => {
		panel.classList.contains('is-open') ? hide() : open();
	});
	close?.addEventListener('click', hide);

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && panel.classList.contains('is-open')) hide();
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth >= DESKTOP_BREAKPOINT && panel.classList.contains('is-open')) {
			hide();
		}
	});
}

function initSubmenuToggles() {
	document.querySelectorAll('.mrent-submenu-toggle').forEach((toggle) => {
		toggle.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();

			const item = toggle.closest('.menu-item');
			if (!item) return;

			const willOpen        = !item.classList.contains('is-open');
			const isDesktopTop    = item.parentElement?.classList.contains('mrent-nav-desktop');

			// На десктопе на топ-уровне держим открытым только один пункт за раз.
			if (isDesktopTop && willOpen) {
				document.querySelectorAll('.mrent-nav-desktop > .menu-item.is-open').forEach((other) => {
					if (other !== item) {
						other.classList.remove('is-open');
						other.querySelector(':scope > .mrent-submenu-toggle')?.setAttribute('aria-expanded', 'false');
					}
				});
			}

			item.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
		});
	});

	// Outside-click — закрываем открытые десктоп-топ-подменю.
	document.addEventListener('click', (e) => {
		document.querySelectorAll('.mrent-nav-desktop > .menu-item.is-open').forEach((item) => {
			if (!item.contains(e.target)) {
				item.classList.remove('is-open');
				item.querySelector(':scope > .mrent-submenu-toggle')?.setAttribute('aria-expanded', 'false');
			}
		});
	});
}

document.addEventListener('DOMContentLoaded', () => {
	initBurger();
	initSubmenuToggles();
});
