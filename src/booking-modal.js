/**
 * Модальное окно «Оставьте заявку».
 *
 * Триггеры открытия — любые ссылки/кнопки с `href="#booking-form"` (делегированный
 * обработчик на document). Закрытие — клик по `[data-booking-modal-close]`, ESC,
 * клик по оверлею. После успешной отправки CF7 (`wpcf7mailsent`) состояние
 * модалки переключается с `form` на `success` через атрибут `data-state`.
 *
 * Чтобы фон страницы не скроллился под модалкой — на `<html>` ставится класс
 * `mrent-booking-modal-open` (стили блокировки скролла в main.css).
 */

const MODAL_ID = 'booking-modal';
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
  // Сбрасываем форму, чтобы при повторном открытии не висел success-стейт.
  setState(modal, 'form');
  const form = modal.querySelector('.wpcf7');
  if (form && typeof window.wpcf7?.reset === 'function') {
    window.wpcf7.reset(form);
  }
}

document.addEventListener('click', (event) => {
  const opener = event.target.closest('a[href="#booking-form"], button[data-booking-modal-open]');
  if (opener) {
    event.preventDefault();
    openModal();
    return;
  }
  const closer = event.target.closest('[data-booking-modal-close]');
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

// CF7 диспатчит событие на форме при успешной отправке.
document.addEventListener('wpcf7mailsent', (event) => {
  const modal = getModal();
  if (!modal || modal.hidden) return;
  if (!modal.contains(event.target)) return;
  setState(modal, 'success');
});
