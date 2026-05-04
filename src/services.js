/**
 * Swiper для секций услуг с >7 карточками.
 *
 * Каждая такая секция — `[data-mrent-services-swiper]` — внутри неё лежат
 * слайды по 4 карточки. На мобайле (<xl) Swiper листает их с пагинацией
 * полосками (Figma 2535:5717) и кнопками prev/next, расположенными рядом
 * с пагинацией в одном flex-row под слайдером.
 *
 * На xl+ Swiper не нужен (десктоп использует свою flex-сетку), поэтому
 * инстанс уничтожается при пересечении breakpoint'а 1280 — иначе остаются
 * inline-transform'ы поверх hidden-обёртки.
 *
 * Структура DOM (см. sections/services/grid.php):
 *   <div data-mrent-services-swiper class="mrent-services-swiper swiper">
 *     <div class="swiper-wrapper">
 *       <div class="swiper-slide">…4 cards…</div>
 *       …
 *     </div>
 *   </div>
 *   <div>
 *     <button class="mrent-services-prev">…</button>
 *     <div class="mrent-services-pagination"></div>
 *     <button class="mrent-services-next">…</button>
 *   </div>
 *
 * Кнопки и пагинация ищутся в общем родителе (closest-обёртке-секции),
 * а не внутри swiper-блока — Swiper их сам никуда не двигает, мы прокидываем
 * элементы через опции `navigation`/`pagination`.
 */

import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

const MQ_DESKTOP = window.matchMedia('(min-width: 1280px)');

function initServicesSwiper(el) {
  let instance = null;

  // Контейнер-родитель содержит и slider, и блок навигации с пагинацией.
  const container = el.parentElement;
  const prev = container.querySelector('.mrent-services-prev');
  const next = container.querySelector('.mrent-services-next');
  const paginationEl = container.querySelector('.mrent-services-pagination');

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

document.querySelectorAll('[data-mrent-services-swiper]').forEach(initServicesSwiper);

/**
 * Карусель фото во второй секции single-страницы услуги
 * (Figma 2142:1629). Активна на всех брейкпоинтах. По одному слайду в
 * видимой области, прев/некст — по бокам картинки, loop включён,
 * чтобы при последнем слайде клик «вперёд» возвращал к началу.
 */
function initFeatureGallery(el) {
  const container = el.parentElement;
  const prev = container.querySelector('.mrent-feature-gallery-prev');
  const next = container.querySelector('.mrent-feature-gallery-next');

  new Swiper(el, {
    modules: [Navigation],
    slidesPerView: 1,
    spaceBetween: 0,
    // loop не включаем: при <=2 слайдах Swiper отключает его сам и клонирует
    // слайды с непредсказуемым результатом (в т.ч. невидимыми оригиналами).
    navigation: prev && next ? { prevEl: prev, nextEl: next } : false,
  });
}

document.querySelectorAll('[data-mrent-feature-gallery]').forEach(initFeatureGallery);

/**
 * Свайпер услуг-виджета на главной (Figma 199:109).
 *   • Mobile (<xl) → 1 карточка в видимой области, gap 10px.
 *   • Desktop (xl+) → 2 карточки в ряд, gap 20px.
 * Пагинация полосками (mrent-bullet-bar) — отдельный <div> рядом со свайпером,
 * подключаем через breakpoints чтобы число bullet'ов было корректным.
 */
function initHomeServices(el) {
  const paginationEl = el.parentElement.querySelector('.mrent-home-services-pagination');

  new Swiper(el, {
    modules: [Pagination],
    slidesPerView: 1,
    spaceBetween: 10,
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
        slidesPerView: 2,
        spaceBetween: 20,
      },
    },
  });
}

document.querySelectorAll('[data-mrent-home-services]').forEach(initHomeServices);
