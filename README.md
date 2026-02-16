# Backend Test - REST API

REST API berbasis Laravel 8 dengan autentikasi menggunakan Laravel Sanctum, CRUD User, pencarian data eksternal, dan dokumentasi Swagger UI.

## Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 8 |
| Database | PostgreSQL |
| Autentikasi | Laravel Sanctum (Bearer Token) |
| HTTP Client | Guzzle |
| API Docs | L5-Swagger (OpenAPI 3.0) |
| Testing | PHPUnit |

## Fitur

- **Autentikasi** — Register, Login, Logout menggunakan Laravel Sanctum (Bearer Token)
- **CRUD User** — Create, Read, Update, Delete user (dilindungi autentikasi)
- **Pencarian Data Eksternal** — Cari data dari sumber eksternal berdasarkan NAMA, NIM, atau YMD
- **Swagger UI** — Dokumentasi API interaktif via browser
- **Error Handling** — Response JSON yang konsisten untuk semua error (validasi, autentikasi, database, dll.)
- **Postman Collection** — File koleksi Postman siap import (`postman_collection.json`)

## Prasyarat

- PHP >= 7.3 / 8.0+
- Composer
- PostgreSQL
- Git

## Instalasi

```bash
# 1. Clone repository
git clone <url-repo>
cd test_be

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate
```

## Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=test_be
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Pastikan database `test_be` sudah dibuat di PostgreSQL:

```bash
# Masuk ke psql
psql -U postgres

# Buat database
CREATE DATABASE test_be;
\q
```

Jalankan migrasi untuk membuat tabel:

```bash
php artisan migrate
```

## Membuat User Awal

Gunakan tinker untuk membuat user pertama:

```bash
php artisan tinker
```

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('secret123')
]);
```

Atau bisa menggunakan endpoint `POST /api/register`.

## Menjalankan Server

```bash
php artisan serve
```

Server berjalan di `http://localhost:8000`.

## Swagger UI (Dokumentasi API)

### Generate Dokumentasi

```bash
php artisan l5-swagger:generate
```

### Akses Swagger UI

Buka di browser:

```
http://localhost:8000/api/documentation
```

### Konfigurasi Swagger

Pastikan variabel berikut ada di `.env`:

```dotenv
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

Setelah mengubah kode controller atau annotation Swagger, jalankan ulang:

```bash
php artisan l5-swagger:generate
```

### Cara Menggunakan Swagger UI

1. Buka `http://localhost:8000/api/documentation`
2. Gunakan endpoint `POST /api/login` atau `POST /api/register` untuk mendapatkan token
3. Klik tombol **Authorize** (ikon gembok) di kanan atas
4. Masukkan token dengan format: `Bearer <token>`
5. Klik **Authorize** → sekarang semua endpoint yang memerlukan autentikasi bisa diakses

---

## Dokumentasi API

**Base URL:** `http://localhost:8000/api`

Semua endpoint memerlukan header `Authorization: Bearer <token>` kecuali Register dan Login.

### A. Autentikasi

| Method | Endpoint         | Keterangan                      | Auth |
|--------|------------------|---------------------------------|------|
| POST   | `/api/register`  | Register user baru              | Tidak |
| POST   | `/api/login`     | Login dan mendapatkan token     | Tidak |
| POST   | `/api/logout`    | Logout (hapus token)            | Ya   |

**Request body Register:**
```json
{
  "name": "Admin",
  "email": "admin@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

**Request body Login:**
```json
{
  "email": "admin@example.com",
  "password": "secret123"
}
```

**Response Login/Register (201/200):**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token_type": "Bearer",
    "token": "1|abc123...",
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com"
    }
  }
}
```

### B. CRUD User

| Method | Endpoint          | Keterangan       |
|--------|-------------------|------------------|
| GET    | `/api/users`      | Daftar semua user |
| POST   | `/api/users`      | Buat user baru    |
| GET    | `/api/users/{id}` | Detail user       |
| PUT    | `/api/users/{id}` | Update user       |
| DELETE | `/api/users/{id}` | Hapus user        |

**Request body Buat/Update User:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "User berhasil dibuat.",
  "data": {
    "id": 2,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-02-16T10:00:00.000000Z",
    "updated_at": "2026-02-16T10:00:00.000000Z"
  }
}
```

### C. Pencarian Data Eksternal

Data diambil secara **real-time** dari sumber data eksternal.

#### Pencarian via Path Parameter

| Method | Endpoint                     | Keterangan             |
|--------|------------------------------|------------------------|
| GET    | `/api/external/name/{name}`  | Cari berdasarkan NAMA  |
| GET    | `/api/external/nim/{nim}`    | Cari berdasarkan NIM   |
| GET    | `/api/external/ymd/{ymd}`    | Cari berdasarkan YMD   |

**Contoh:**
- `GET /api/external/name/Turner%20Mia`
- `GET /api/external/nim/9352078461`
- `GET /api/external/ymd/20230405`

#### Pencarian via Query Parameter (Best Practice — Search Dinamis)

Endpoint `/api/external/search` adalah **search dinamis** yang menggabungkan ketiga pencarian (NAMA, NIM, YMD) dalam satu endpoint fleksibel menggunakan query parameter.

| Method | Endpoint                          | Keterangan             |
|--------|-----------------------------------|------------------------|
| GET    | `/api/external/search?name=...`   | Cari berdasarkan NAMA  |
| GET    | `/api/external/search?nim=...`    | Cari berdasarkan NIM   |
| GET    | `/api/external/search?ymd=...`    | Cari berdasarkan YMD   |

**Cara kerja:**

1. Client mengirim **tepat satu** query parameter (`name`, `nim`, atau `ymd`)
2. Server secara otomatis mendeteksi parameter mana yang dikirim melalui `SearchExternalRequest`
3. Parameter di-mapping ke field data eksternal (`name` → `NAMA`, `nim` → `NIM`, `ymd` → `YMD`)
4. Data diambil real-time dari sumber eksternal, lalu difilter berdasarkan field dan value yang diminta
5. Jika tidak ada parameter atau lebih dari satu parameter dikirim, akan mengembalikan error validasi (422)

**Validasi:**
- Harus mengisi **tepat satu** parameter per request
- Tidak boleh kosong (tanpa parameter) dan tidak boleh lebih dari satu

**Contoh request:**
```
GET /api/external/search?name=Turner%20Mia
GET /api/external/search?nim=9352078461
GET /api/external/search?ymd=20230405
```

**Response sukses (200):**
```json
{
  "success": true,
  "message": "Pencarian berhasil.",
  "data": {
    "field": "NAMA",
    "value": "Turner Mia",
    "count": 1,
    "data": [
      {
        "NAMA": "Turner Mia",
        "YMD": "20220713",
        "NIM": "9352078461"
      }
    ]
  }
}
```

**Response error validasi (422) — lebih dari satu parameter:**
```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "filter": ["Harus mengisi tepat satu parameter: name, nim, atau ymd."]
  }
}
```

**Keuntungan Search Dinamis:**
- Satu endpoint untuk semua jenis pencarian — lebih mudah di-maintain
- Validasi otomatis di level Form Request
- Mapping field otomatis (query param → field data eksternal)
- Mudah di-extend jika ada field baru di masa depan

---

## Struktur Project

```
app/
├── Console/            # Artisan commands
├── Exceptions/         # Exception handlers (termasuk DB error handling)
├── Http/
│   ├── Controllers/    # AuthController, UserController, ExternalDataController
│   ├── Middleware/      # Middleware aplikasi
│   └── Requests/       # Form request validation
├── Models/             # Eloquent models (User)
├── Providers/          # Service providers
├── Services/           # Business logic (AuthService, UserService, ExternalDataService)
├── Swagger/            # Swagger/OpenAPI schema definitions
└── Traits/             # Reusable traits (ApiResponse)
```

## Error Handling

API mengembalikan response JSON yang konsisten untuk semua jenis error:

| HTTP Code | Keterangan |
|-----------|------------|
| 401       | Unauthenticated — token tidak valid atau tidak ada |
| 404       | Data tidak ditemukan |
| 422       | Validasi gagal |
| 502       | Gagal mengambil data dari sumber eksternal |
| 503       | Database tidak dapat dihubungi |

**Contoh response error:**
```json
{
  "success": false,
  "message": "Tidak dapat terhubung ke database. Pastikan database sudah berjalan dan konfigurasi sudah benar."
}
```

## Testing

```bash
php artisan test
```

## Postman Collection

Import file `postman_collection.json` ke Postman untuk mencoba semua endpoint.

## Database Backup

File `database_backup.sql` tersedia untuk restore data awal:

```bash
psql -U postgres -d test_be < database_backup.sql
```
