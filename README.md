# Uptime Monitor API

A production-ready Laravel 13 API for monitoring website uptime. The app records HTTP check history, calculates uptime percentage, transitions monitor state after consecutive failures, and sends Markdown email notifications when a site goes down or recovers.

## Requirements

- PHP 8.4+
- Composer
- MySQL or PostgreSQL
- Laravel-compatible queue/mail/cache configuration

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sitemonitor
DB_USERNAME=root
DB_PASSWORD=
```

Configure the notification recipient:

```env
UPTIME_NOTIFICATION_EMAIL=alerts@example.com
MAIL_MAILER=log
```

Run migrations:

```bash
php artisan migrate
```

Start the local API:

```bash
php artisan serve
```

## Scheduler

The monitor worker is registered as:

```bash
php artisan app:check-monitors
```

Laravel schedules it every minute. In production, add the standard Laravel scheduler cron entry:

```cron
* * * * * cd /path/to/siteMinorUpkeep && php artisan schedule:run >> /dev/null 2>&1
```

For local development, run:

```bash
php artisan schedule:work
```

## Testing

Run the test suite with:

```bash
php artisan test
```

If your local PHP binary is not on `PATH`, use the full PHP executable path, for example:

```bash
C:\laragon\bin\php\php-8.4.21-Win32-vs17-x64\php.exe artisan test
```

## API Endpoints

### Create Monitor

`POST /api/monitors`

```json
{
  "url": "https://example.com",
  "check_interval": 5,
  "threshold": 3
}
```

### List Monitors

`GET /api/monitors`

### Monitor History

`GET /api/monitors/{id}/history?page=1&per_page=15`

History is ordered by `checked_at` descending. `per_page` is capped at `100`.

## Postman

Import [postman_collection.json](postman_collection.json) into Postman and set the collection variable `environment_url` to your local or deployed base URL, such as:

```text
http://127.0.0.1:8000
```
