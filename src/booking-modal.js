/**
 * Модальное окно «Оставьте заявку».
 *
 * На странице может быть несколько модалок (разные формы) — каждая со своим
 * DOM-id. Триггер открытия — любая ссылка с `href="#<modal-id>"`, где
 * `<modal-id>` совпадает с id корня модалки (`.mrent-booking-modal`). Закрытие —
 * клик по `[data-booking-modal-close]` внутри карточки/оверлея, ESC, клик по
 * оверлею. После успешной отправки CF7 (`wpcf7mailsent`) состояние модалки
 * переключается с `form` на `success` через атрибут `data-state`.
 *
 * Скролл фона блокируется классом `mrent-booking-modal-open` на `<html>`.
 * Класс ставится/снимается через `syncBodyLock()` — пересчёт по реальному
 * состоянию всех модалок, чтобы не было «зависших» локов при любых краевых
 * сценариях.
 */

const MODAL_SELECTOR = '.mrent-booking-modal';
const OPEN_CLASS = 'mrent-booking-modal-open';

function setState(modal, state) {
  if (modal) modal.dataset.state = state;
}

function syncBodyLock() {
  const anyOpen = document.querySelector(`${MODAL_SELECTOR}:not([hidden])`);
  if (anyOpen) {
    document.documentElement.classList.add(OPEN_CLASS);
  } else {
    document.documentElement.classList.remove(OPEN_CLASS);
  }
}

function openModal(modal) {
  if (!modal) return;
  setState(modal, 'form');
  modal.hidden = false;
  syncBodyLock();
}

function closeModal(modal) {
  if (modal && !modal.hidden) {
    modal.hidden = true;
    setState(modal, 'form');
    // CF7 хочет именно `<form>` элемент (`.wpcf7-form`), а не его враппер
    // `.wpcf7`. Передача не-формы в `wpcf7.reset()` бросает TypeError на
    // `new FormData(...)` и роняет дальнейший close-хендлер — лок остаётся.
    const form = modal.querySelector('form.wpcf7-form');
    if (form && typeof window.wpcf7?.reset === 'function') {
      try { window.wpcf7.reset(form); } catch (_) { /* не блокируем закрытие */ }
    }
  }
  // Снимаем лок безусловно — даже если что-то пошло не так с конкретной модалкой,
  // пересчёт по реальному DOM-состоянию гарантирует, что класс не останется
  // висеть на html, когда видимых модалок нет.
  syncBodyLock();
}

function closeAll() {
  document
    .querySelectorAll(`${MODAL_SELECTOR}:not([hidden])`)
    .forEach((modal) => closeModal(modal));
  // Доп. страховка на случай, если NodeList оказался пустым, а класс остался.
  syncBodyLock();
}

document.addEventListener('click', (event) => {
  // Сначала проверяем закрытие — close-кнопка может оказаться внутри `<a>`
  // (на success-стейте «Закрыть» — это `<a href="#">`), и opener-ветка не
  // должна перехватить клик.
  const closer = event.target.closest('[data-booking-modal-close]');
  if (closer) {
    event.preventDefault();
    const modal = closer.closest(MODAL_SELECTOR);
    if (modal) {
      closeModal(modal);
    } else {
      // Fallback: closer вне модалки (не должно случаться, но на всякий случай) —
      // закрываем все открытые.
      closeAll();
    }
    return;
  }

  const opener = event.target.closest('a[href]');
  if (!opener) return;
  const href = opener.getAttribute('href') || '';
  if (href.length < 2 || href.charAt(0) !== '#') return;
  const modal = document.getElementById(href.slice(1));
  if (!modal || !modal.classList.contains('mrent-booking-modal')) return;
  event.preventDefault();
  openModal(modal);
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') closeAll();
});

// CF7 диспатчит событие на форме при успешной отправке. Переключаем ту модалку,
// внутри которой эта форма.
document.addEventListener('wpcf7mailsent', (event) => {
  const modal = event.target.closest(MODAL_SELECTOR);
  if (modal && !modal.hidden) setState(modal, 'success');
});

// Финальная страховка: на любой переход hash/popstate сверяемся с DOM.
window.addEventListener('hashchange', syncBodyLock);
window.addEventListener('pageshow', syncBodyLock);
