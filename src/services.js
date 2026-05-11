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
import { Navigation, Pagination, Grid } from 'swiper/modules';
// CSS модуля Grid — без него Swiper не выставляет высоту/позицию рядов в slidesPerView<auto.
import 'swiper/css/grid';

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
 * Карусель тарифов на single-странице услуги (Figma 2960:7082).
 *   • Mobile (<xl) → Swiper по 1 карточке + полосочная пагинация под слайдером.
 *   • Desktop (xl+) → Swiper уничтожается, раскладку держит flex
 *     (.swiper-wrapper остаётся display:flex, на слайдах включены
 *     `xl:flex-1!`/`xl:w-auto!`).
 */
function initPackagesSwiper(el) {
  let instance = null;

  // Кнопки лежат в общем `.relative`-родителе (parentElement), а пагинация —
  // ниже как сиблинг этого родителя, поэтому ищем её в ближайшей <section>.
  const container = el.parentElement;
  const section = el.closest('section') || container;
  const prev = container.querySelector('.mrent-packages-prev');
  const next = container.querySelector('.mrent-packages-next');
  const paginationEl = section.querySelector('.mrent-packages-pagination');

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
        spaceBetween: 10,
        autoHeight: true,
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

document.querySelectorAll('[data-mrent-packages-swiper]').forEach(initPackagesSwiper);

/**
 * Карусель доп. опций (вторая вкладка секции «Тарифы и пакеты»,
 * Figma 2120:1493). Поведение идентично пакетному свайперу.
 */
function initOptionsSwiper(el) {
  let instance = null;

  const container = el.parentElement;
  const prev = container.querySelector('.mrent-options-prev');
  const next = container.querySelector('.mrent-options-next');
  const paginationEl = container.querySelector('.mrent-options-pagination');

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
        spaceBetween: 10,
        autoHeight: true,
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

document.querySelectorAll('[data-mrent-options-swiper]').forEach(initOptionsSwiper);

/**
 * Карусель «Преимущества» на single-странице услуги (Figma 2135:1436 / 2535:6110).
 *   • Mobile (<xl) → Swiper, 1 колонка × 3 ряда на видимую страницу
 *     (Grid module, fill:row), горизонтальный свайп, полосочная пагинация
 *     + жёлтые prev/next 55×55 под слайдером.
 *   • Desktop (xl+) → Swiper уничтожается, `.swiper-wrapper` становится
 *     3-кол CSS-grid'ом через `xl:!grid` в разметке.
 */
function initBenefitsSwiper(el) {
  let instance = null;

  const container = el.parentElement;
  const prev = container.querySelector('.mrent-benefits-prev');
  const next = container.querySelector('.mrent-benefits-next');
  const paginationEl = container.querySelector('.mrent-benefits-pagination');

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
        modules: [Navigation, Pagination, Grid],
        slidesPerView: 1,
        spaceBetween: 15,
        grid: { rows: 3, fill: 'row' },
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

document.querySelectorAll('[data-mrent-benefits-swiper]').forEach(initBenefitsSwiper);

/**
 * Переключатель табов «Тарифы и пакеты» / «Дополнительные опции»
 * на single-странице услуги (Figma 2960:7082, 2120:1493).
 *
 * UI: два набора пилюль (по одному на панель, на xl+) + один общий
 * native <select> на мобайле. Клик/change → активируется панель,
 * остальные `hidden`. После показа дёргаем swiper.update() — слайды,
 * инициализированные поверх hidden-панели, иначе считают ширину 0.
 */
function initSinglePackagesTabs(root) {
  const panels = root.querySelectorAll('[data-mrent-pkg-panel]');
  if (panels.length < 2) return;

  const triggers = root.querySelectorAll('[data-mrent-pkg-trigger]');
  const select = root.querySelector('[data-mrent-pkg-select]');

  function activate(id) {
    panels.forEach((panel) => {
      const isActive = panel.dataset.mrentPkgId === id;
      if (isActive) panel.removeAttribute('hidden');
      else panel.setAttribute('hidden', '');

      if (isActive) {
        panel.querySelectorAll('.swiper').forEach((swiperEl) => {
          if (swiperEl.swiper) swiperEl.swiper.update();
        });
      }
    });
    triggers.forEach((trigger) => {
      const isActive = trigger.dataset.mrentPkgId === id;
      if (isActive) trigger.setAttribute('aria-current', 'true');
      else trigger.removeAttribute('aria-current');
    });
    if (select && select.value !== id) select.value = id;
  }

  triggers.forEach((trigger) => {
    trigger.addEventListener('click', () => activate(trigger.dataset.mrentPkgId));
  });
  if (select) {
    select.addEventListener('change', () => activate(select.value));
  }
}

document.querySelectorAll('[data-mrent-single-packages]').forEach(initSinglePackagesTabs);

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
    modules: [Pagination, Grid],
    // Mobile: 1 колонка × 2 ряда — 2 карточки на видимую страницу.
    slidesPerView: 1,
    spaceBetween: 10,
    grid: { rows: 2, fill: 'row' },
    pagination: paginationEl
      ? {
          el: paginationEl,
          clickable: true,
          bulletClass: 'mrent-bullet-bar',
          bulletActiveClass: 'mrent-bullet-bar-active',
        }
      : false,
    breakpoints: {
      // Desktop: 2 карточки в ряд, без grid.
      1280: {
        slidesPerView: 2,
        spaceBetween: 20,
        grid: { rows: 1 },
      },
    },
  });
}

document.querySelectorAll('[data-mrent-home-services]').forEach(initHomeServices);

/**
 * Виджет «Услуги по категориям с табами» (Figma 2169:2145 / 2345:3873).
 *
 * DOM (см. sections/common/services-tabbed.php):
 *   <div data-mrent-services-tabbed>
 *     <button data-mrent-tab-trigger data-mrent-tab-id="..."> ... </button> ×N (desktop pills)
 *     <select data-mrent-tab-select> ... </select>            (mobile)
 *     <div data-mrent-tab-panel data-mrent-tab-id="..." [hidden]>
 *       <div class="mrent-services-tabbed-swiper swiper">...</div>
 *       <div data-mrent-tab-pagination></div>
 *     </div> ×N
 *   </div>
 *
 * На каждый tab-панель — отдельный Swiper с собственной полосочной пагинацией
 * (1 карточка <xl, 2 ≥xl, gap 10/20). При переключении таба активный panel
 * раскрывается, остальные `hidden`. Swiper.update() после показа — нужен,
 * чтобы Swiper пересчитал slidesPerView/ширины слайдов после display:none.
 *
 * Селект на мобайле и пилюли на десктопе — два UI к одному и тому же
 * стейту: всегда оба синхронизированы через `activate(id)`.
 */
function initServicesTabbed(root) {
  const triggers = root.querySelectorAll('[data-mrent-tab-trigger]');
  const select = root.querySelector('[data-mrent-tab-select]');
  const panels = root.querySelectorAll('[data-mrent-tab-panel]');

  const swipers = new Map();
  panels.forEach((panel) => {
    const id = panel.dataset.mrentTabId;
    const swiperEl = panel.querySelector('.mrent-services-tabbed-swiper');
    const paginationEl = panel.querySelector('[data-mrent-tab-pagination]');
    if (!swiperEl) return;

    const instance = new Swiper(swiperEl, {
      modules: [Pagination, Grid],
      // Mobile: 1 колонка × 2 ряда — 2 карточки на видимую страницу,
      // свайп горизонтальный, gap 10px между рядами.
      slidesPerView: 1,
      spaceBetween: 10,
      grid: { rows: 2, fill: 'row' },
      pagination: paginationEl
        ? {
            el: paginationEl,
            clickable: true,
            bulletClass: 'mrent-bullet-bar',
            bulletActiveClass: 'mrent-bullet-bar-active',
          }
        : false,
      breakpoints: {
        // Desktop: 2 карточки в ряд, без grid (1 ряд).
        1280: {
          slidesPerView: 2,
          spaceBetween: 20,
          grid: { rows: 1 },
        },
      },
    });
    swipers.set(id, instance);
  });

  function activate(id) {
    panels.forEach((panel) => {
      const isActive = panel.dataset.mrentTabId === id;
      if (isActive) panel.removeAttribute('hidden');
      else panel.setAttribute('hidden', '');
    });
    triggers.forEach((trigger) => {
      const isActive = trigger.dataset.mrentTabId === id;
      if (isActive) trigger.setAttribute('aria-current', 'true');
      else trigger.removeAttribute('aria-current');
    });
    if (select && select.value !== id) select.value = id;
    const swiper = swipers.get(id);
    if (swiper) swiper.update();

    // Тоже триггерим update на любом стороннем Swiper'е внутри активного panel
    // (например, свайпер «Страны поставки» в car-import). Swiper хранит инстанс
    // в `el.swiper` после init — без этого свайпер, инициализированный поверх
    // hidden-панели, не пересчитает ширины слайдов при первом показе.
    panels.forEach((panel) => {
      if (panel.dataset.mrentTabId !== id) return;
      panel.querySelectorAll('.swiper').forEach((swiperEl) => {
        if (swiperEl.swiper && swiperEl.swiper !== swiper) {
          swiperEl.swiper.update();
        }
      });
    });
  }

  triggers.forEach((trigger) => {
    trigger.addEventListener('click', () => activate(trigger.dataset.mrentTabId));
  });
  if (select) {
    select.addEventListener('change', () => activate(select.value));
  }

  // Активируем первый таб (по DOM-порядку), чтобы изначально проставить
  // aria-current на пилюле и сделать первый panel видимым.
  if (panels[0]) activate(panels[0].dataset.mrentTabId);
}

document.querySelectorAll('[data-mrent-services-tabbed]').forEach(initServicesTabbed);

/**
 * «Похожие услуги» на single-странице услуги (Figma 2120:1425 / 2535:6239).
 *   • Mobile (<xl) → 1 карточка в видимой области, gap 10.
 *   • Desktop (xl+) → 2 карточки в ряд, gap 20.
 * Полосочная пагинация (mrent-bullet-bar) лежит сиблингом свайпера в той же
 * flex-col-обёртке секции; находим её через parentElement.
 */
function initRelatedServices(el) {
  const paginationEl = el.parentElement.querySelector('.mrent-related-services-pagination');

  new Swiper(el, {
    modules: [Pagination, Grid],
    // Mobile: 1 колонка × 2 ряда (2 карточки на видимой странице).
    slidesPerView: 1,
    spaceBetween: 10,
    grid: { rows: 2, fill: 'row' },
    pagination: paginationEl
      ? {
          el: paginationEl,
          clickable: true,
          bulletClass: 'mrent-bullet-bar',
          bulletActiveClass: 'mrent-bullet-bar-active',
        }
      : false,
    breakpoints: {
      // Desktop: 2 карточки в ряд, без grid.
      1280: {
        slidesPerView: 2,
        spaceBetween: 20,
        grid: { rows: 1 },
      },
    },
  });
}

document.querySelectorAll('[data-mrent-related-services]').forEach(initRelatedServices);
