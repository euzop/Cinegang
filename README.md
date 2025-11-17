# Cinegang


Cinegang is a lightweight PHP/MySQL web application for browsing and tracking films and series. This repository contains the front-end PHP pages, styles, assets, and a SQL dump (`cinegang.sql`) to create the database schema and seed data.

![Image Alt](https://github.com/euzop/Cinegang/blob/b4042459a40e6349b52565caf3309cb559bc8134/cinegang-screenshot.png)

## Features

- User registration and login
- Browse movies, series, cartoons, and anime
- Play film pages and view descriptions
- Add items to a watchlist
- Simple user profile page
- Admin area for management

## Requirements

- PHP 7.4+ (or compatible PHP 8)
- MySQL / MariaDB
- A web server (Apache/Nginx) or the PHP built-in server for local testing
- Browser with JavaScript enabled

## Quick Setup (local)

1. Copy the project files to your web server document root, or open the project directory.

2. Create the database and import the schema/data from `cinegang.sql`.

- Using the MySQL command line:

```powershell
mysql -u <db_user> -p < <path_to_project>\cinegang.sql
```

Replace `<db_user>` and `<path_to_project>` as appropriate.

- Or import via phpMyAdmin by creating a database (e.g. `cinegang`) and importing the `cinegang.sql` file.

3. Configure database credentials in `db.php`.

- Open `db.php` and update host, username, password, and database name to match your environment.

4. Run the app locally using PHP's built-in server (for quick testing):

```powershell
cd <path_to_project>
php -S localhost:8000 -t .
```

Then open `http://localhost:8000` in your browser.

## Important Files and Structure

- `index.php` — main entry / homepage
- `homepage.php` — homepage content
- `description.php` — film/episode description pages
- `play.php` — page used to play selected media
- `login.php`, `register.php`, `logoutHandler.php` — authentication
- `db.php` — database connection (update with credentials)
- `watchlist.php`, `watchlistHandler.php` — watchlist UI & actions
- `profile.php`, `account.php` — user profile/account pages
- `p_movie.php`, `p_series.php`, `p_anime.php`, `p_cartoon.php` — category pages
- `p_review.php` / `filmrate.php` — review and rating related pages
- `handlers/` — server-side form/action handlers (e.g., `loginHandler.php`, `registerHandler.php`)
- `css/` — stylesheets
- `media/` — images and media assets
- `scripts/` — JavaScript files
- `cinegang.sql` — SQL dump to create database and seed sample data
- `backup/` — older/backed-up pages and styles

## Security & Development Notes

- The app currently uses plain PHP files and may use raw SQL queries. For production, ensure:
	- Use prepared statements / parameterized queries to prevent SQL injection.
	- Validate and sanitize all user inputs.
	- Use secure password hashing (e.g., `password_hash()` / `password_verify()`).
	- Configure proper session handling and HTTPS in production.

## Contributing

- Open an issue to describe bugs or feature requests.
- Fork, make changes, and submit a pull request.
- If you plan to restructure or add modern tooling (Composer, routing, MVC), please open an issue first to align on direction.

## Troubleshooting

- If pages fail to connect to the database, verify `db.php` credentials and that the MySQL server is running.
- If CSS/JS don't load, check that the `css/` and `scripts/` folders are accessible and referenced correctly.
