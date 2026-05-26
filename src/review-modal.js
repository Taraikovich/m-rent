/**
 * Модальное окно «Оставьте отзыв».
 *
 * Триггеры открытия — любые ссылки/кнопки с `href="#reviews-form"`. Закрытие —
 * клик по `[data-review-modal-close]`, ESC, клик по оверлею. После успешной
 * отправки CF7 (`wpcf7mailsent`) состояние переключается с `form` на `success`.
 *
 * Структурно повторяет `booking-modal.js` — отдельный модал, чтобы заявка
 * и отзыв не делили DOM и не путали состояния друг друга.
 */

const MODAL_ID = 'review-modal';
const OPEN_CLASS = 'mrent-booking-modal-open';

function getModal() {
  return document.getElementById(MODAL_ID);
}

function setState(modal, state) {
  modal.dataset.state = state;
}

function openModal() {
  const modal = getModal();
  if (!modal) return;
  setState(modal, 'form');
  modal.hidden = false;
  document.documentElement.classList.add(OPEN_CLASS);
}

function closeModal() {
  const modal = getModal();
  if (!modal || modal.hidden) return;
  modal.hidden = true;
  document.documentElement.classList.remove(OPEN_CLASS);
  setState(modal, 'form');
  // CF7 ждёт сам `<form>` (`.wpcf7-form`), а не враппер `.wpcf7` —
  // иначе `new FormData(...)` бросает TypeError и роняет close-хендлер.
  const form = modal.querySelector('form.wpcf7-form');
  if (form && typeof window.wpcf7?.reset === 'function') {
    try { window.wpcf7.reset(form); } catch (_) { /* не блокируем закрытие */ }
  }
}

document.addEventListener('click', (event) => {
  const opener = event.target.closest('a[href="#reviews-form"], button[data-review-modal-open]');
  if (opener) {
    event.preventDefault();
    openModal();
    return;
  }
  const closer = event.target.closest('[data-review-modal-close]');
  if (closer) {
    event.preventDefault();
    closeModal();
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  const modal = getModal();
  if (modal && !modal.hidden) closeModal();
});

document.addEventListener('wpcf7mailsent', (event) => {
  const modal = getModal();
  if (!modal || modal.hidden) return;
  if (!modal.contains(event.target)) return;
  setState(modal, 'success');
});
