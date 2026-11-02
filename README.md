# Lumen Service App

REST API sederhana (Lumen 10) untuk lima resource: **categories, products, customers, orders, users**.
Mendukung content negotiation (JSON/XML), pagination, validasi, dan error handling JSON.

## Menjalankan

```bash
composer install
cp .env.example .env      # lalu isi DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan migrate
php -S localhost:8000 -t public
```

Untuk error handling JSON, set `APP_DEBUG=false` di `.env` lalu restart server.

## Endpoint

Setiap resource (`categories`, `products`, `customers`, `orders`, `users`) memakai pola yang sama:

| Method | URL | Header wajib |
|---|---|---|
| GET | `/{resource}` | `Accept` |
| GET | `/{resource}/{id}` | `Accept` |
| POST | `/{resource}` | `Accept`, `Content-Type: application/json` |
| PUT | `/{resource}/{id}` | `Accept`, `Content-Type: application/json` |
| DELETE | `/{resource}/{id}` | `Accept` |

`Accept` hanya menerima `application/json` atau `application/xml`. Format respons mengikuti nilai `Accept`.

## Kode respons

| Kode | Arti |
|---|---|
| 200 / 201 | Berhasil / berhasil dibuat |
| 400 | Validasi gagal (daftar error per field) |
| 404 | Data atau route tidak ditemukan |
| 405 | Method tidak didukung |
| 406 | `Accept` tidak valid |
| 415 | `Content-Type` bukan `application/json` |

## Pagination

Semua `GET /{resource}` dipaginasi (`?page=2`). Jumlah data per halaman diatur oleh `PER_PAGE` di `app/Http/Controllers/Controller.php`.

```json
{
  "message": "Daftar produk",
  "total_count": 6,
  "limit": 2,
  "pagination": { "next_page": "http://localhost:8000/products?page=2", "current_page": 1 },
  "data": []
}
```

## Aturan validasi

| Resource | Aturan |
|---|---|
| categories | `name` wajib, min 3 |
| products | `sku` wajib, maks 30, unik; `name` wajib, min 5; `category` salah satu dari `Makanan, Minuman, Elektronik, Pakaian`; `description` opsional, min 10; `price` dan `stock` integer >= 0; `weight_gram` integer >= 1; `is_available` boolean |
| customers | `name` wajib, min 3; `phone` opsional, maks 20 |
| orders | `customer_name`, `product_name` wajib, min 3; `quantity` integer >= 1; `total_price` integer >= 0; `order_date` tanggal; `status` salah satu dari `pending, paid, shipped, cancelled` |
| users | `name` wajib, min 3; `email` valid dan unik; `password` min 6 (opsional saat update) |

`PUT` memakai aturan yang sama dengan `POST`, jadi field wajib harus dikirim lengkap.

## Catatan tabel products

Kolom `sku`, `description`, `weight_gram`, dan `is_available` ditambahkan lewat migration
`2026_11_02_000001_add_detail_columns_to_products_table`. Data lama tetap aman: `sku` diisi otomatis
(`SKU-001`, `SKU-002`, dst.), `weight_gram` bernilai 1, dan `is_available` bernilai true.
Karena `PUT` mewajibkan field lengkap, data lama perlu dilengkapi lewat `PUT /products/{id}`.

## Struktur penting

- `app/Http/Controllers/Controller.php`: helper bersama (negosiasi format, respons JSON/XML, pagination, validasi, 404)
- `app/Exceptions/Handler.php`: error handling JSON (`success`, `status`, `message`) saat `APP_DEBUG=false`
