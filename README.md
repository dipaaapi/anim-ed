# Anim-Ed 🎬📖

A modern, minimalist web platform for discovering and tracking anime, manga, manhwa and manhua. **Anim-Ed** is a portfolio and educational project that serves as an interactive catalog hub, powered by live data from the public [AniList](https://anilist.co) API.

---

## 🚀 Features

* **Welcome page:** an introduction to the project, with live trending posters, before visitors enter the catalog.
* **Live, auto-updating catalog:** data is refreshed from AniList every 10 minutes. If AniList is slow or down, the last good copy (kept for up to 7 days) is shown instead of empty pages.
* **Current trend lists:** Weekly (trending now), Monthly (started in the last 6 months) and Yearly (started in the last 12 months).
* **Advanced filters:** genre, ~400 tags/themes (Isekai, Shounen, Reincarnation…), format, country of origin, status, year, season, source material, age rating and 8 sort orders.
* **Manga / Manhwa / Manhua:** dedicated tabs and navigation links filtered by country of origin (Japan, Korea, China).
* **Accounts:** email and password registration and login, stored in MySQL. Google and Facebook login are prepared but disabled for now.
* **Admin panel:** a hidden login URL, a dashboard and ad management.
* **Safe defaults:** adult titles are hidden unless explicitly selected.

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 (PHP 8.5) |
| Frontend | Blade templates, Tailwind CSS 4, HTMX, Vite |
| Database | MySQL 8.4 (Docker) |
| Data source | [AniList GraphQL API](https://docs.anilist.co) (no API key required) |
| Runtime | Docker Compose (Apache + PHP, MySQL) |

---

## ⚡ Getting Started (Docker, recommended)

### Prerequisites
* [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### 1. Configure the environment
```bash
cp .env.example .env
```
Then edit `.env`:
* Set `DB_PASSWORD`, `DB_ROOT_PASSWORD` and `ADMIN_SEED_PASSWORD` to strong values.
* Set `ADMIN_LOGIN_PATH` to a hard-to-guess path.
* Leave `APP_KEY` empty to have one generated, or run `php artisan key:generate`.

### 2. Start the app
```bash
docker compose up -d --build
```
On first start the container waits for MySQL, runs the migrations and seeds the default accounts.

| Service | Address |
|---|---|
| Website | http://localhost:8080 |
| Admin login | http://localhost:8080/`<ADMIN_LOGIN_PATH>` |
| MySQL (from your machine) | `127.0.0.1:3307` (port 3307 avoids clashing with XAMPP's MySQL on 3306) |

### Seeded accounts
| Role | Email | Password |
|---|---|---|
| Admin | `admin@anim-ed.io` | value of `ADMIN_SEED_PASSWORD` in `.env` |
| Demo viewer | `demo@anim-ed.io` | `demo1234` |

### Useful commands
```bash
docker compose up -d --build              # start, or rebuild after code changes
docker compose down                       # stop (data is kept)
docker compose down -v                    # stop AND delete the database
docker compose logs -f app                # follow application logs
docker compose exec app php artisan ...   # run artisan inside the container
docker compose exec db mysql -u anim_ed -p anim_ed   # open a MySQL shell
```

---

## 💻 Local Development (without Docker)

Requires PHP 8.4+, Composer and Node.js 20+.

```bash
composer install
npm install
docker compose up -d db        # or point DB_* in .env at your own MySQL
php artisan migrate --seed
composer run dev               # Laravel server + Vite dev server
```

### Running tests
Tests use an in-memory SQLite database and do not touch MySQL:
```bash
php artisan test
```

---

## 🔑 Enabling Google / Facebook Login (later)

1. Create OAuth apps in Google Cloud Console and Meta for Developers.
2. Fill in `GOOGLE_*` and `FACEBOOK_*` in `.env`.
3. Uncomment the social login routes in [routes/web.php](routes/web.php).
4. Uncomment the buttons in [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php).

---

## 📋 Open Source & Data Compliance

This project is built for portfolio and educational purposes:

1. **Non-commercial:** it uses AniList's free public API in line with its [terms of use](https://docs.anilist.co/guide/terms-of-use).
2. **Rate-limit friendly:** API responses are cached server-side, so repeat visits do not hit AniList.
3. **Catalog only:** Anim-Ed shows metadata and official trailers. It does not host or stream copyrighted episodes or chapters.
4. **Environment security:** secrets live in `.env`, which is git-ignored and never committed.
