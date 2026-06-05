/**
 * Плавное появление элементов при скролле (scroll-reveal).
 *
 * Любой контейнер с атрибутом `data-mrent-reveal` отдаёт свои прямые дочерние
 * элементы на анимацию. Скрытое состояние (сдвиг вниз + opacity:0) задаётся
 * сразу в CSS (см. src/main.css) — поэтому нет вспышки видимых карточек до
 * запуска JS. Здесь мы лишь добавляем класс `.is-visible`, когда элемент
 * попадает в зону видимости, и переход проигрывается.
 *
 * Каскад: элементы, попавшие в видимость в одном колбэке (например, первый
 * экран при загрузке), проявляются по очереди со сдвигом 80мс — визуально
 * «волной». Каждый элемент анимируется один раз (unobserve после показа).
 *
 * Прогрессивное улучшение: без IntersectionObserver сразу показываем всё;
 * без JS вовсе — карточки раскрываются <noscript>-стилем из header.php.
 */

const STAGGER_MS = 80;

function initReveal(container) {
  const items = Array.from(container.children);
  if (!items.length) return;

  // Нет поддержки — показываем всё сразу, без анимации.
  if (!('IntersectionObserver' in window)) {
    items.forEach((el) => el.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries
        .filter((entry) => entry.isIntersecting)
        .forEach((entry, i) => {
          entry.target.style.transitionDelay = `${i * STAGGER_MS}ms`;
          entry.target.classList.add('is-visible');
          obs.unobserve(entry.target);
        });
    },
    { threshold: 0.15, rootMargin: '0px 0px -10% 0px' }
  );

  items.forEach((el) => observer.observe(el));
}

document.querySelectorAll('[data-mrent-reveal]').forEach(initReveal);
