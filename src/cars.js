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
 */

import Swiper from 'swiper';
import { Navigation, FreeMode } from 'swiper/modules';
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

initFilters();
initGalleries();
