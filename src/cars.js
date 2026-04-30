/**
 * Swiper-инициализация для раздела «Автопарк»:
 *
 *   • .mrent-cars-filter — горизонтальная карусель фильтров (мобайл/планшет).
 *     На xl+ Swiper не инициализируется вообще — раскладку держит CSS
 *     (xl:!flex xl:!gap-[20px] в filters.php). При ресайзе создаём/уничтожаем
 *     инстанс, чтобы переходы между mobile и desktop были чистыми (без
 *     остаточных inline-transform'ов).
 *
 *   • .mrent-car-gallery — карусель фото на странице модели. Активна всегда.
 *
 *   • .mrent-popular — секция «Популярные модели» на главной. На мобайле — 1
 *     карточка + полоски пагинации, на xl+ — 4 карточки в ряд (без пагинации).
 */

import Swiper from 'swiper';
import { Navigation, FreeMode, Pagination } from 'swiper/modules';
import 'swiper/css';

const MQ_DESKTOP = window.matchMedia('(min-width: 1280px)');

function initFilters() {
  document.querySelectorAll('[data-mrent-cars-filter]').forEach((el) => {
    let instance = null;

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
          modules: [FreeMode],
          slidesPerView: 'auto',
          spaceBetween: 10,
          freeMode: true,
          slidesOffsetBefore: 15,
          slidesOffsetAfter: 30, // edge-bleed справа: последняя карточка визуально заезжает за край
        });
      }
    };

    ensure();
    MQ_DESKTOP.addEventListener('change', ensure);
  });
}

function initGalleries() {
  document.querySelectorAll('[data-mrent-car-gallery]').forEach((el) => {
    const prev = el.querySelector('.mrent-car-gallery-prev');
    const next = el.querySelector('.mrent-car-gallery-next');
    new Swiper(el, {
      modules: [Navigation],
      slidesPerView: 1,
      spaceBetween: 0,
      loop: true,
      navigation: { prevEl: prev, nextEl: next },
    });
  });
}

function initPopular() {
  document.querySelectorAll('[data-mrent-popular]').forEach((el) => {
    const paginationEl = el.parentElement.querySelector('.mrent-popular-pagination');
    new Swiper(el, {
      modules: [Pagination],
      slidesPerView: 1,
      spaceBetween: 15,
      pagination: paginationEl
        ? {
            el: paginationEl,
            clickable: true,
            bulletClass: 'mrent-bullet-bar',
            bulletActiveClass: 'mrent-bullet-bar-active',
          }
        : false,
      breakpoints: {
        // xl: 4 карточки в ряд, gap 29px (по дизайну 1720 = 4×408 + 3×29).
        1280: {
          slidesPerView: 4,
          spaceBetween: 29,
        },
      },
    });
  });
}

/**
 * «Почему выбирают нас»: на мобайле — Swiper из N слайдов (по 3 карточки в
 * каждом) с пагинацией-полосками; на xl+ — статическая 3-кол сетка через
 * `xl:!contents` на промежуточных обёртках, поэтому Swiper не нужен и при
 * desktop-брейкпоинте уничтожается, чтобы не накладывать transform/инлайны
 * поверх grid-раскладки.
 */
function initWhyUs() {
  document.querySelectorAll('[data-mrent-why-us]').forEach((el) => {
    const paginationEl = el.parentElement.querySelector('.mrent-why-us-pagination');
    let instance = null;

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
          modules: [Pagination],
          slidesPerView: 1,
          spaceBetween: 0,
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
  });
}

initFilters();
initGalleries();
initPopular();
initWhyUs();
