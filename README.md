# m-rent

Тема WordPress на **Tailwind CSS v4** + **Vite**, поднимается локально в Docker.

## Что внутри

| Сервис | Адрес | Что это |
|---|---|---|
| WordPress | http://localhost:8080 | Сам сайт |
| phpMyAdmin | http://localhost:8081 | Веб-интерфейс к БД |
| MailHog | http://localhost:8025 | Ловушка исходящих писем |
| Vite dev | http://localhost:5173 | Дев-сервер ассетов (только при `npm run dev`) |

Учётка WP по умолчанию: **admin / admin** (`admin@example.test`).

## Требования

- Docker Engine + Compose plugin
- Node.js >= 20

При свежей установке Ubuntu всё это уже стоит после первичного запуска проекта.

## Первый запуск

```bash
cp .env.example .env       # при необходимости поправь пароли/порты
docker compose up -d       # поднимет WP + MariaDB + phpMyAdmin + MailHog + WP-CLI
npm install                # ставит Vite и Tailwind v4
```

Если БД пустая — установить WP и активировать тему:

```bash
docker compose exec wpcli wp core install \
  --url=http://localhost:8080 \
  --title="M-Rent Dev" \
  --admin_user=admin --admin_password=admin \
  --admin_email=admin@example.test --skip-email
docker compose exec wpcli wp theme activate m-rent
```

## Ежедневная разработка

```bash
npm run dev                # Vite на :5173 с HMR
```

Открыть http://localhost:8080 — стили и скрипты подгружаются с Vite, любые изменения в `*.php`, `src/**/*.css`, `src/**/*.js` подхватываются мгновенно (без F5).

Внутри `src/main.css` строкой `@source "../**/*.php"` указан путь, по которому Tailwind сканирует классы из шаблонов темы.

Тема активна как обычно через `Внешний вид → Темы` или через `wp theme activate m-rent`.

## Production-сборка

```bash
npm run build              # пишет хешированные ассеты в assets/build/ + manifest.json
```

После `npm run build` (и при выключенном `npm run dev`) тема подключает уже статические `assets/build/assets/main-<hash>.{js,css}` через `inc/vite.php`, который читает `manifest.json`.

Файл-маркер `assets/build/hot` создаётся автоматически при `npm run dev` и удаляется при `npm run build` / выходе. По нему PHP отличает dev-режим от prod.

## Структура

```
m-rent/
├── docker-compose.yml         # WP, MariaDB, phpMyAdmin, WP-CLI, MailHog
├── .env / .env.example        # пароли, порты
├── docker/mu-plugins/
│   └── mailhog.php            # SMTP-редирект писем на MailHog
├── package.json
├── vite.config.js             # base, hot-file plugin, Tailwind plugin
├── src/
│   ├── main.js                # entry для Vite
│   └── main.css               # @import "tailwindcss"; + @source
├── assets/build/              # выход Vite (gitignored)
├── inc/vite.php               # PHP-хелпер enqueue манифеста / dev-клиента
├── style.css                  # метаданные темы
├── index.php / header.php / footer.php
└── functions.php              # бутстрап + регистрация enqueue
```

## Полезные команды

### Управление стеком

```bash
docker compose up -d           # старт
docker compose down            # стоп (данные сохранятся в volume)
docker compose down -v         # стоп + удалить volumes (БД и ядро WP уйдут)
docker compose logs -f wordpress
docker compose restart wordpress
```

### WP-CLI

WP-CLI работает в постоянно поднятом контейнере `wpcli`:

```bash
docker compose exec wpcli wp plugin list
docker compose exec wpcli wp plugin install woocommerce --activate
docker compose exec wpcli wp user list
docker compose exec wpcli wp option update blogname "My Site"
docker compose exec wpcli wp db export -            # дамп БД
docker compose exec wpcli wp db import /var/www/html/wp-content/themes/m-rent/dump.sql
docker compose exec wpcli wp eval 'wp_mail("t@x.local","subj","body");'   # тест письма
```

### База данных напрямую

```bash
docker compose exec db mariadb -uwordpress -pwordpress wordpress
```

Или через phpMyAdmin: http://localhost:8081 (логин/пароль из `.env`).

### MailHog

Все письма WP (регистрация, восстановление пароля, формы) ловятся в http://localhost:8025. Реальная почта **не уходит** — это безопасно.

## Где править тему

- **Стили:** Tailwind-классы прямо в `*.php`. Кастомные слои — в `src/main.css` (можно добавлять `@layer components`, `@theme`, и пр. — это синтаксис Tailwind v4).
- **JS:** `src/main.js`. Можно подключать любые npm-пакеты — Vite их соберёт.
- **PHP:** обычные шаблоны WordPress (`index.php`, `single.php`, `page.php`, `archive.php`, и т.д.). Подключение ассетов уже настроено в `functions.php` через `mrent_vite_enqueue('main.js')`.

## Траблшутинг

- **«Тема не появилась в админке»** — проверь, что `style.css` лежит в корне темы и содержит шапку `Theme Name`.
- **«Стили не подгружаются в dev»** — проверь, что `npm run dev` запущен и в HTML страницы видны теги `http://localhost:5173/@vite/client` и `http://localhost:5173/main.js`.
- **«Стили не подгружаются в prod»** — нужно один раз выполнить `npm run build`, чтобы появились `assets/build/.vite/manifest.json` и хешированные файлы.
- **Изменил `vite.config.js`** — перезапусти `npm run dev`.
- **Сбросить всё с нуля** — `docker compose down -v && rm -rf assets/build node_modules` и пройти первый запуск заново.
