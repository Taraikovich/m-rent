/**
 * Свайпер отзывов на странице «Отзывы» (Figma 2218:3282 desktop / 2483:4159 nav).
 *
 *   • Mobile (<xl) → 1 карточка в видимой области, gap 15px, под слайдером —
 *     prev / полосочная пагинация / next.
 *   • Desktop (xl+) → 4 карточки в ряд, 3 ряда (Grid-модуль, fill:'row'),
 *     gap 29px. Под свайдером та же навигация, активная «полоска» бьётся по
 *     странице (12 карточек = 1 страница).
 *
 * Контейнер навигации лежит рядом со swiper'ом в общем flex-col родителе —
 * ищем кнопки/пагинацию через `closest('.mrent-reviews').parentElement` нельзя,
 * Swiper-классы перетирает; берём родителя самого swiper-элемента.
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Grid } from 'swiper/modules';
import 'swiper/css/grid';

const MQ_DESKTOP = window.matchMedia('(min-width: 1280px)');

function initReviewsSwiper(el) {
  const container = el.parentElement;
  const prev = container.querySelector('.mrent-reviews-prev');
  const next = container.querySelector('.mrent-reviews-next');
  const paginationEl = container.querySelector('.mrent-reviews-pagination');

  let instance = null;

  const build = () => {
    const desktop = MQ_DESKTOP.matches;
    instance = new Swiper(el, {
      modules: [Navigation, Pagination, Grid],
      slidesPerView: desktop ? 4 : 1,
      spaceBetween: desktop ? 29 : 15,
      grid: desktop ? { rows: 3, fill: 'row' } : { rows: 4, fill: 'row' },
      navigation: prev && next ? { prevEl: prev, nextEl: next } : false,
      pagination: paginationEl
        ? {
            el: paginationEl,
            clickable: true,
            bulletClass: 'mrent-bullet-bar',
            bulletActiveClass: 'mrent-bullet-bar-active',
          }
        : false,
    });
  };

  const rebuild = () => {
    if (instance) {
      instance.destroy(true, true);
      instance = null;
    }
    build();
  };

  build();
  // Swiper при смене layout (rows / slidesPerView) не пересчитывает Grid
  // корректно — пересоздаём инстанс на переходе mobile↔desktop.
  MQ_DESKTOP.addEventListener('change', rebuild);
}

document.querySelectorAll('[data-mrent-reviews]').forEach(initReviewsSwiper);
