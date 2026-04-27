# CLAUDE.md

Заметки для будущих сессий Claude Code в этом репозитории.

## Что это

WordPress-тема **`m-rent`** + локальное dev-окружение в Docker. Корень репозитория и есть корень темы — он монтируется в `wp-content/themes/m-rent` внутри контейнера WordPress. WP-ядро живёт в named volume `wp_core` и в репо не лежит.

Стек тем: **Tailwind CSS v4** через `@tailwindcss/vite` + **Vite 5**, JS — vanilla.

## Сервисы (docker-compose.yml)

| Сервис | Порт | Назначение |
|---|---|---|
| `wordpress` | 8080 | `wordpress:6-php8.3-apache` |
| `db` | — | `mariadb:11`, healthcheck'ом ждёт `wordpress` |
| `phpmyadmin` | 8081 | UI к БД |
| `wpcli` | — | `wordpress:cli-php8.3`, `entrypoint: sleep infinity` — постоянно живой контейнер для `docker compose exec wpcli wp ...`, user `33:33` (www-data) |
| `mailhog` | 8025 | UI; SMTP `1025` внутри сети |

`.env` содержит порты и креды БД (gitignored). `.env.example` — шаблон.

## Команды

```bash
docker compose up -d                          # старт всего стека
docker compose down                            # стоп (данные сохраняются)
docker compose exec wpcli wp <команда>         # WP-CLI
docker compose exec db mariadb -uwordpress -pwordpress wordpress

npm run dev      # Vite :5173 с HMR + создаёт assets/build/hot
npm run build    # prod-сборка → assets/build/{assets/*, .vite/manifest.json}
```

Тестовое письмо в MailHog:
```bash
docker compose exec wpcli wp eval 'wp_mail("t@x.local","subj","body");'
```

## Ключевые файлы

- `docker-compose.yml` — стек.
- `docker/mu-plugins/mailhog.php` — must-use плагин: SMTP-редирект на mailhog:1025 + переопределение `wp_mail_from` (см. ниже про PHPMailer).
- `vite.config.js` — `root: 'src'`, hot-file plugin, Tailwind plugin, `base` варьируется по `command`.
- `src/main.js`, `src/main.css` — entry Vite. CSS содержит `@import "tailwindcss"` и `@source "../**/*.php"`.
- `inc/vite.php` — PHP-интеграция: `mrent_vite_is_dev()`, `mrent_vite_enqueue($entry)` (dev-инжекция / prod-manifest).
- `functions.php` — вызывает `mrent_vite_enqueue('main.js')` на `wp_enqueue_scripts`.
- Шаблоны темы: `index.php`, `header.php`, `footer.php` в корне.

## Ловушки и неочевидные решения

Эти вещи уже отлажены — менять их нужно с пониманием.

1. **Ключ Vite-манифеста — `main.js`, а не `src/main.js`.** Vite нормализует пути относительно `root: 'src'`. Поэтому в `functions.php` вызов `mrent_vite_enqueue('main.js')`. Если что-то поменяешь в `rollupOptions.input` — проверь ключи в сгенерированном `assets/build/.vite/manifest.json`.

2. **`base` в Vite разная для dev и build.** В dev — `'/'` (чтобы `http://localhost:5173/@vite/client` работал). В build — `'/wp-content/themes/m-rent/assets/build/'` (чтобы Vite складывал правильный публичный URL в манифест и asset-теги). Реализовано через `defineConfig(({ command }) => ...)`.

3. **`wp_head` callback в dev-инжекции зарегистрирован с priority 99.** `wp_enqueue_scripts` срабатывает изнутри `wp_print_head_scripts()` (это callback на `wp_head` priority 9). Регистрация `add_action('wp_head', $cb, 1)` в этот момент уже не запустится — приоритет 1 пройден. Поэтому в `mrent_vite_enqueue_dev` стоит priority 99.

4. **MailHog mu-plugin переопределяет From через `wp_mail_from` фильтр, а не только в `phpmailer_init`.** WordPress зовёт `PHPMailer::setFrom()` ДО `phpmailer_init`, и валидатор PHPMailer отбрасывает дефолтный `wordpress@localhost` (нет TLD) — `wp_mail()` возвращает `false`, хук вообще не доходит. Фильтры `wp_mail_from` / `wp_mail_from_name` подменяют адрес ещё до `setFrom`.

5. **Hot-file = маркер dev-режима.** `assets/build/hot` создаётся плагином `hotFilePlugin` в `vite.config.js` при старте dev-сервера и удаляется при exit/SIGINT/SIGTERM/SIGHUP и при `vite build`. PHP проверяет его наличие через `mrent_vite_is_dev()`. Если файл остался после краша Vite — удалить руками: `rm assets/build/hot`.

6. **`__dirname` в `vite.config.js`.** Файл ESM (`"type": "module"` в package.json) → `__dirname` undefined. Поднят через `dirname(fileURLToPath(import.meta.url))`. Не убирай этот импорт.

7. **WP-CLI запускается как user 33** — это `www-data`. Файлы, созданные через `wp ... > /var/www/html/...`, будут принадлежать www-data, что и нужно.

8. **`docker-compose.yml` и `.env` физически лежат внутри папки темы в контейнере** (`/var/www/html/wp-content/themes/m-rent/`). WordPress их не загружает (не `.php`-шаблоны), но не путайся, если будешь искать что-то через find в контейнере.

## Конвенции

- Tailwind-классы пишем в шаблонах темы (`*.php`). Кастомные стили — в `src/main.css` (Tailwind v4 синтаксис: `@theme`, `@layer components`, `@utility`).
- Новые JS-модули — в `src/`, импортятся из `main.js`. Vite сам разнесёт по чанкам.
- Новые PHP-функции темы — в `inc/*.php`, подключаются через `require_once` в `functions.php`.
- WP debug включён (`WP_DEBUG=1`, `WP_DEBUG_LOG=true`, `WP_DEBUG_DISPLAY=false`). Логи: `docker compose exec wordpress tail -f /var/www/html/wp-content/debug.log`.

## Чего НЕ делать

- **Не коммитить `assets/build/`, `node_modules/`, `.env`** — они в `.gitignore`.
- **Не запускать `apt-get` параллельно** (dpkg-lock). Если ставишь Docker и Node одновременно — последовательно.
- **Не ставить WordPress-ядро в репозиторий.** Оно в named volume `wp_core` целенаправленно.
- **Не использовать `WP_DEBUG_DISPLAY=true`** — поломает HTML-вывод и manifest-теги. Логи смотреть в `wp-content/debug.log`.

## Быстрая проверка после изменений

```bash
# контейнеры живы
docker compose ps

# фронт отвечает и тема активна
curl -s http://localhost:8080/ | grep -E 'mrent-|@vite|main-' | head

# почта ходит
docker compose exec wpcli wp eval 'echo var_export(wp_mail("t@x.local","s","b"), true);'
curl -s http://localhost:8025/api/v2/messages | head -c 200
```
