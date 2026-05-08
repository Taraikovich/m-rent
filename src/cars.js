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

/**
 * Карусель фото на странице «Авто под заказ» (Figma 2983:7088 / 3004:9332).
 * Один слайдер на странице; стрелки 50/65px без фона. Loop включаем только при
 * 2+ слайдах — на одиночном слайде Swiper иначе ругается и дублирует слайд.
 */
function initCarImport() {
  document.querySelectorAll('[data-mrent-car-import-gallery]').forEach((el) => {
    const prev = el.querySelector('.mrent-car-import-prev');
    const next = el.querySelector('.mrent-car-import-next');
    const slidesCount = el.querySelectorAll('.swiper-slide').length;
    new Swiper(el, {
      modules: [Navigation],
      slidesPerView: 1,
      spaceBetween: 0,
      loop: slidesCount > 1,
      navigation: { prevEl: prev, nextEl: next },
    });
  });
}

/**
 * Свайпер таба «Страны поставки» на странице авто под заказ
 * (Figma 2989:7357 desktop / 3004:9366 mobile). Один слайд = одна страна
 * (3-колоночный блок на десктопе, одна колонка на мобайле). Кнопки prev/next
 * и контейнер пагинации лежат рядом со swiper'ом — внутри корневого
 * `[data-mrent-car-import-countries]`. autoHeight, чтобы контейнер
 * подстраивался под высоту текущего слайда (тексты сильно различаются).
 *
 * Свайпер инициализируется при загрузке, но таб 2 изначально hidden.
 * Корректные размеры рассчитаются после первого `swiper.update()` —
 * это вызывает `activate()` в src/services.js при переключении на этот таб.
 */
function initCarImportCountries() {
  document.querySelectorAll('[data-mrent-car-import-countries]').forEach((el) => {
    const swiperEl = el.querySelector('.mrent-car-import-countries-swiper');
    if (!swiperEl) return;
    const prev = el.querySelector('.mrent-car-import-countries-prev');
    const next = el.querySelector('.mrent-car-import-countries-next');
    const paginationEl = el.querySelector('.mrent-car-import-countries-pagination');
    const slidesCount = swiperEl.querySelectorAll('.swiper-slide').length;

    new Swiper(swiperEl, {
      modules: [Navigation, Pagination],
      slidesPerView: 1,
      spaceBetween: 0,
      autoHeight: true,
      loop: slidesCount > 1,
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

/**
 * «Спецпредложения»: простой свайпер картинок (Figma 171:389).
 *   • Mobile (<xl) → 2 карточки в видимой области, gap 10px, пагинация-полоски.
 *   • Desktop (xl+) → 4 карточки в ряд, gap 30px, кнопки prev/next в шапке секции.
 * Кнопки prev/next и контейнер пагинации лежат в общем родителе свайпера —
 * по тому же шаблону, что и `[data-mrent-services-swiper]`.
 */
function initSpecials() {
  document.querySelectorAll('[data-mrent-specials]').forEach((el) => {
    const container = el.parentElement;
    const prev = container.querySelector('.mrent-specials-prev');
    const next = container.querySelector('.mrent-specials-next');
    const paginationEl = container.querySelector('.mrent-specials-pagination');

    new Swiper(el, {
      modules: [Navigation, Pagination],
      slidesPerView: 2,
      spaceBetween: 10,
      navigation: prev && next ? { prevEl: prev, nextEl: next } : false,
      pagination: paginationEl
        ? {
            el: paginationEl,
            clickable: true,
            bulletClass: 'mrent-bullet-bar',
            bulletActiveClass: 'mrent-bullet-bar-active',
          }
        : false,
      breakpoints: {
        1280: {
          slidesPerView: 4,
          spaceBetween: 30,
        },
      },
    });
  });
}

/**
 * «Наши преимущества» на странице авто под заказ (Figma 2989:7617 / 3020:7716).
 * Mobile (<xl) → Swiper из 2 слайдов по 3 карточки + полосочная пагинация.
 * Desktop (xl+) → Swiper уничтожается, раскладку держит xl:!grid через xl:!contents.
 */
function initCarImportBenefits() {
  document.querySelectorAll('[data-mrent-car-import-benefits]').forEach((el) => {
    const paginationEl = el.parentElement.querySelector('.mrent-car-import-benefits-pagination');
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
initCarImport();
initCarImportCountries();
initCarImportBenefits();
initPopular();
initWhyUs();
initSpecials();
