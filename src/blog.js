/**
 * Swiper для архива блога — только мобайл (Figma 2341:3145 / 2341:3266).
 *
 * На xl+ Swiper не нужен (десктоп использует статичную flex/grid-сетку
 * 3 колонки), поэтому инстанс уничтожается при пересечении breakpoint'а
 * 1280 — иначе остаются inline-transform'ы поверх hidden-обёртки и
 * пагинация рендерится дважды.
 *
 * Каждый swiper-slide уже содержит до 4 карточек в одной колонке (см.
 * sections/blog/grid.php), поэтому slidesPerView=1: Swiper листает страницы,
 * а не отдельные карточки.
 *
 * Структура DOM:
 *   <div data-mrent-blog-swiper class="mrent-blog-swiper swiper">
 *     <div class="swiper-wrapper">
 *       <div class="swiper-slide">…до 4 карточек stacked…</div>
 *       …
 *     </div>
 *   </div>
 *   <div>
 *     <button class="mrent-blog-prev">…</button>
 *     <div class="mrent-blog-pagination"></div>
 *     <button class="mrent-blog-next">…</button>
 *   </div>
 */

import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

const MQ_DESKTOP = window.matchMedia('(min-width: 1280px)');

function initBlogSwiper(el) {
  let instance = null;

  // Контейнер-родитель содержит и slider, и блок навигации с пагинацией.
  const container = el.parentElement;
  const prev = container.querySelector('.mrent-blog-prev');
  const next = container.querySelector('.mrent-blog-next');
  const paginationEl = container.querySelector('.mrent-blog-pagination');

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
        modules: [Navigation, Pagination],
        slidesPerView: 1,
        spaceBetween: 0,
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
    }
  };

  ensure();
  MQ_DESKTOP.addEventListener('change', ensure);
}

document.querySelectorAll('[data-mrent-blog-swiper]').forEach(initBlogSwiper);
