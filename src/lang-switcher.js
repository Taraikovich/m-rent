/**
 * Переключатель языков в шапке, завязанный на плагин GTranslate (free).
 *
 * GTranslate переводит страницу через Google Translate и хранит выбор в cookie
 * `googtrans` формата `/{src}/{dst}` (например `/ru/en`). Нам достаточно выставить
 * cookie и перезагрузить страницу — скрипт плагина (js/float.js) на следующей
 * загрузке прочитает cookie и переведёт страницу. См. inc/gtranslate.php.
 *
 * Дропдаун рендерит PHP (mrent_lang_switcher); здесь — открытие/закрытие и клик
 * по языку.
 */

const COOKIE_EXPIRED = 'Thu, 01 Jan 1970 00:00:00 GMT';

/**
 * Домены-кандидаты для cookie googtrans от самого специфичного к корневому:
 * для `sub.example.com` → ['sub.example.com', 'example.com'] (TLD исключаем —
 * на него браузер cookie не пускает). Для localhost / IP — пусто (host-only).
 */
function googTransDomains() {
	const host = location.hostname;
	if (host.indexOf('.') === -1 || /^\d+(\.\d+){3}$/.test(host)) return [];

	const parts = host.split('.');
	const domains = [];
	for (let i = 0; i < parts.length - 1; i++) {
		domains.push(parts.slice(i).join('.'));
	}
	return domains;
}

/**
 * Ставим cookie googtrans ОДИН раз — на registrable-домен (`.example.com`), как
 * это делает и сам Google Translate. Так наша кука и Google-кука совпадают, без
 * дублей в разных scope'ах (из-за которых язык не сбрасывался на поддомене).
 */
function setGoogTransCookie(value) {
	const domains = googTransDomains();
	if (!domains.length) {
		// localhost / IP — только host-only cookie.
		document.cookie = 'googtrans=' + value + ';path=/';
		return;
	}
	const registrable = domains[domains.length - 1];
	document.cookie = 'googtrans=' + value + ';path=/;domain=.' + registrable;
}

/**
 * Сбрасываем cookie googtrans во ВСЕХ scope'ах: host-only + каждый родительский
 * домен (с точкой и без). Иначе уцелевшая кука на родительском домене (например
 * `.example.com`, поставленная Google) не даёт вернуться к языку-источнику.
 */
function clearGoogTransCookie() {
	const base = 'googtrans=;expires=' + COOKIE_EXPIRED + ';path=/';
	document.cookie = base; // host-only
	googTransDomains().forEach((domain) => {
		document.cookie = base + ';domain=' + domain;
		document.cookie = base + ';domain=.' + domain;
	});
}

function switchLanguage(lang, def) {
	if (lang === def) {
		clearGoogTransCookie();
	} else {
		setGoogTransCookie('/' + def + '/' + lang);
	}
	location.reload();
}

function initLangSwitcher(root) {
	const toggle = root.querySelector('[data-mrent-lang-toggle]');
	const menu = root.querySelector('[data-mrent-lang-menu]');
	const def = root.getAttribute('data-default') || 'ru';
	if (!toggle || !menu) return;

	const open = () => {
		menu.hidden = false;
		root.classList.add('is-open');
		toggle.setAttribute('aria-expanded', 'true');
	};
	const close = () => {
		menu.hidden = true;
		root.classList.remove('is-open');
		toggle.setAttribute('aria-expanded', 'false');
	};

	toggle.addEventListener('click', (e) => {
		e.stopPropagation();
		menu.hidden ? open() : close();
	});

	menu.querySelectorAll('[data-lang]').forEach((opt) => {
		opt.addEventListener('click', () => switchLanguage(opt.getAttribute('data-lang'), def));
	});

	// Клик вне переключателя / Escape — закрываем.
	document.addEventListener('click', (e) => {
		if (!root.contains(e.target)) close();
	});
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') close();
	});
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('[data-mrent-lang]').forEach(initLangSwitcher);
});
