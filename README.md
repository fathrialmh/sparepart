# Sistem Sparepart (Laravel)

Migrasi project sparepart ke Laravel 11.

## Requirements

- PHP 8.2+
- Composer
- MySQL (MAMP)

## Cara Menjalankan Project

1. Masuk ke folder project:

```bash
cd sparepart-laravel
```

2. Install dependency:

```bash
composer install
```

3. Copy file environment:

```bash
cp .env.example .env
```

4. Generate app key:

```bash
php artisan key:generate
```

5. Pastikan konfigurasi database di `.env` seperti ini (MAMP default):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=sparepart_db
DB_USERNAME=root
DB_PASSWORD=root
```

6. Jalankan migrasi + seeder:

```bash
php artisan migrate --seed
```

7. Jalankan server Laravel:

```bash
php artisan serve
```

8. Buka di browser:

```text
http://127.0.0.1:8000
```

## Login Default

- Username: `admin`
- Password: `admin123`

## Perintah Bermanfaat

- Jalankan test:

```bash
php artisan test
```

- Reset database (hapus semua tabel lalu migrate + seed ulang):

```bash
php artisan db:wipe --force
php artisan migrate --seed
```
