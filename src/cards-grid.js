/**
 * Swiper для универсальной секции «cards-grid» (sections/cards-grid.php) —
 * используется секциями certificates/audience и certificates/benefits.
 *
 * Один список карточек: на десктопе Swiper уничтожается, и список
 * раскладывается в CSS-grid (xl:grid xl:grid-cols-N в разметке). На мобайле
 * Swiper-grid модуль группирует карточки по 4 на слайд (rows=4, fill=row).
 */

import Swiper from 'swiper';
import { Pagination, Grid } from 'swiper/modules';
import 'swiper/css/grid';

const MQ_DESKTOP = window.matchMedia('(min-width: 1280px)');

function initCardsGridSwiper(el) {
  let instance = null;
  const container = el.parentElement;
  const paginationEl = container.querySelector('.mrent-cards-grid-pagination');

  const ensure = () => {
    if (MQ_DESKTOP.matches) {
      if (instance) {
        instance.destroy(true, true);
        instance = null;
      }
      return;
    }
    if (!instance) {
      instance = new Swiper(el, {
        modules: [Pagination, Grid],
        slidesPerView: 1,
        spaceBetween: 10,
        grid: { rows: 4, fill: 'row' },
        pagination: paginationEl
          ? {
              el: paginationEl,
              clickable: true,
              bulletClass: 'mrent-bullet-bar',
              bulletActiveClass: 'mrent-bullet-bar-active',
            }
          : false,
      });
    }
  };

  ensure();
  MQ_DESKTOP.addEventListener('change', ensure);
}

document.querySelectorAll('[data-mrent-cards-grid-swiper]').forEach(initCardsGridSwiper);
