[![codecov](https://codecov.io/gh/IronSinew/recipebox/graph/badge.svg?token=IJC5HGMP0W)](https://codecov.io/gh/IronSinew/recipebox)
# Revtools

## Overview
Revtools is a [Laravel Jetstream](https://jetstream.laravel.com)-based site using Vue and InertiaJS. It's also built with:
- Docker
- Caddy
- PostgreSQL
- Laravel Scout

## Installation Prerequisites
- Docker Desktop
- [Bun](https://bun.sh)
- **If using Windows:**
    - You will need to set up [WSL2](https://docs.microsoft.com/en-us/windows/wsl/install) (Ubuntu is known to work) and configure Docker Desktop to use it as a [backend](https://docs.docker.com/desktop/windows/wsl/).
    - All of the following command line commands should be run inside your WSL2 / shell.

## Installation
`cp provision/caddy/Caddyfile.example provision/caddy/Caddyfile`

It is highly likely that `.env` will be present, but rather, `.env.example` instead. To fix this, run:  
`cp .env.example .env`  

`docker compose up -d`  
`docker compose exec php composer install` 

`docker compose exec php php artisan key:generate`  
`bun install`

Confirm the `laravel` schema was created during PostgreSQL container creation, otherwise, create the schema.  
`docker compose exec php php artisan migrate:fresh --seed` 

If migration fails 
`docker compose exec pgsql createdb -U root laravel`  

Also, create the testing schema
`docker compose exec pgsql createdb -U root laravel_test`

Configure folder permissions.  
`sudo chmod -R 777 storage/` 

See if it works!

`bun run dev`

You should now be able to view the site at <http://revtools.localhost>.

---

# Contribution Guide
Please run `make prepush` to validate that all automated tests and styling has been satisfied before submitting a PR.

---

## Testing emails
Access mailhog locally at <http://localhost:1025>

---

## Stop Application
`docker compose down`

## Start Application (after install)
`docker compose up -d`  
`bun run dev`
- It may not be an awful idea to check migration / npm / composer dependencies in case other installs are needed as well.

---

## Extras

### Start application:
`docker compose up -d`

### Stop application:
`docker compose down`

### Xdebug

Xdebug is available in the PHP container but is turned off by default. To turn it on, set `XDEBUG_MODE` (`XDEBUG_MODE=develop` as Xdebug's default, other options are [documented](https://xdebug.org/docs/all_settings#mode)) in `.env` and restart PHP via docker compose:

```
docker compose up -d php
```

# Roadmap
### Item section
- [x] Tooltips
- [x] Global search
- [x] Filtering
### Mobs
- [ ] TODO
### Quests
- [ ] TODO
### Map
- [ ] TODO
### Guides
- [ ] TODO
