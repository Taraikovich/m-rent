/**
 * Плавающая кнопка мессенджеров (мобайл).
 *
 * Тоггл состояния data-state="open|closed" на корневом элементе,
 * клик по основной кнопке — переключает, клик вне — закрывает.
 */

const ROOT_SELECTOR = '[data-mrent-msg-fab]';
const TOGGLE_SELECTOR = '[data-mrent-msg-fab-toggle]';

function setState(root, state) {
  root.dataset.state = state;
  const btn = root.querySelector(TOGGLE_SELECTOR);
  if (btn) btn.setAttribute('aria-expanded', state === 'open' ? 'true' : 'false');
}

const SCROLL_TOP_SELECTOR = '[data-mrent-scroll-top]';

function initScrollTop() {
  const btn = document.querySelector(SCROLL_TOP_SELECTOR);
  if (!btn) return;

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

function init() {
  initScrollTop();

  const root = document.querySelector(ROOT_SELECTOR);
  if (!root) return;

  const toggle = root.querySelector(TOGGLE_SELECTOR);
  if (!toggle) return;

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    setState(root, root.dataset.state === 'open' ? 'closed' : 'open');
  });

  document.addEventListener('click', (e) => {
    if (root.dataset.state !== 'open') return;
    if (root.contains(e.target)) return;
    setState(root, 'closed');
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && root.dataset.state === 'open') {
      setState(root, 'closed');
    }
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
