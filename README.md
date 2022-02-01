[![Laravel](https://github.com/StevyMarlino/nextwaveApi/actions/workflows/laravel.yml/badge.svg)](https://github.com/StevyMarlino/nextwaveApi/actions/workflows/laravel.yml)

## Step to Deploy

### copy .env.example to .env
 
```bash
cp .env.example .env
```

### update package or install 

```composer
composer install
```
OR
```composer
composer update
```
### generate key for the application

```php
php artisan key:generate
```

### migration table and seed

```php
php artisan migrate:fresh --seed
```
### generate documentation

```php
php artisan scribe:generate
```
### open the document
```php
php artisan serve
```
and open the browser on the default url 127.0.0.1:8000





