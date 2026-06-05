/**
 * Tap-to-flip фото карточки авто (sections/cars/card.php).
 *
 * На десктопе альтернативное фото показывается по :hover (group-hover в разметке).
 * На тач-устройствах hover недоступен, поэтому:
 *   • первый тап по фото показывает hover-изображение (переход отменяется);
 *   • повторный тап по уже «перевёрнутой» карточке — переходит на страницу авто.
 * Тап по другой карточке или мимо — снимает flip с предыдущей.
 * Кнопки «Забронировать»/«Подробнее» под фото работают как обычные ссылки.
 *
 * Состояние пишется в data-flipped="true" на самой ссылке-обёртке, его ловят
 * group-data-[flipped=true]-варианты на обоих <img>.
 */

const MQ_TOUCH = window.matchMedia('(hover: none)');

function initCardFlip() {
  const links = document.querySelectorAll('[data-mrent-card-flip]');
  if (!links.length) return;

  let current = null;

  const reset = () => {
    if (current) {
      current.removeAttribute('data-flipped');
      current = null;
    }
  };

  links.forEach((link) => {
    link.addEventListener('click', (e) => {
      // Десктоп (есть hover) — обычный hover + переход по ссылке.
      if (!MQ_TOUCH.matches) return;
      // Уже перевёрнута — пропускаем тап дальше, ссылка срабатывает.
      if (link.dataset.flipped === 'true') return;
      e.preventDefault();
      reset();
      link.dataset.flipped = 'true';
      current = link;
    });
  });

  // Тап вне текущей карточки — снять flip.
  document.addEventListener('click', (e) => {
    if (current && !current.contains(e.target)) reset();
  });
}

initCardFlip();
