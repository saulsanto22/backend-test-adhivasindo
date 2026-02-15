# Backend Test - REST API

REST API berbasis Laravel 8 dengan autentikasi menggunakan Laravel Sanctum.

## Teknologi

- **Framework:** Laravel 8
- **Database:** MySQL
- **Autentikasi:** Laravel Sanctum (Bearer Token)

## Instalasi

```bash
# Clone repository
git clone <url-repo>
cd test_be

# Install dependensi
composer install

# Salin file environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di file .env
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migrasi
php artisan migrate

# Buat user awal (melalui tinker)
php artisan tinker
# User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>bcrypt('secret123')]);

# Jalankan server
php artisan serve
```

## Dokumentasi API

Base URL: `http://localhost:8000/api`

Semua endpoint di bawah memerlukan header `Authorization: Bearer <token>` kecuali `/login`.

### A. Autentikasi (Login)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/api/login` | Login dan mendapatkan token |
| POST | `/api/logout` | Logout (hapus token) |

**Contoh body login:**
```json
{
  "email": "admin@example.com",
  "password": "secret123"
}
```

**Contoh response login:**
```json
{
  "token_type": "Bearer",
  "token": "1|abc123...",
  "user": { "id": 1, "name": "Admin", "email": "admin@example.com" }
}
```

### B. CRUD User

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/users` | Daftar semua user |
| POST | `/api/users` | Buat user baru |
| GET | `/api/users/{id}` | Detail user |
| PUT | `/api/users/{id}` | Update user |
| DELETE | `/api/users/{id}` | Hapus user |

**Contoh body buat user:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret123"
}
```

### C. Cari Data Berdasarkan NAMA

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/external/name/{name}` | Cari berdasarkan nama |

**Contoh:** `GET /api/external/name/Turner%20Mia`

### D. Cari Data Berdasarkan NIM

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/external/nim/{nim}` | Cari berdasarkan NIM |

**Contoh:** `GET /api/external/nim/9352078461`

### E. Cari Data Berdasarkan YMD

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/external/ymd/{ymd}` | Cari berdasarkan YMD |

**Contoh:** `GET /api/external/ymd/20230405`

### Pencarian Umum (Best Practice)

Endpoint ini mencakup ketiga pencarian di atas (C, D, E) dalam satu endpoint menggunakan query parameter.

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/external/search?name=...` | Cari berdasarkan NAMA |
| GET | `/api/external/search?nim=...` | Cari berdasarkan NIM |
| GET | `/api/external/search?ymd=...` | Cari berdasarkan YMD |

Hanya **satu** parameter pencarian yang diperbolehkan per request.

**Contoh:**
- `GET /api/external/search?name=Turner%20Mia` → sama dengan endpoint C
- `GET /api/external/search?nim=9352078461` → sama dengan endpoint D
- `GET /api/external/search?ymd=20230405` → sama dengan endpoint E

**Contoh response pencarian:**
```json
{
  "field": "NAMA",
  "value": "Turner Mia",
  "count": 1,
  "data": [
    { "NAMA": "Turner Mia", "YMD": "20220713", "NIM": "9352078461" }
  ]
}
```

### Catatan

- Data pencarian (C, D, E) diambil secara **real-time** dari sumber eksternal.
- Semua endpoint kecuali login memerlukan **autentikasi** (Bearer Token dari endpoint login).
- File Postman Collection tersedia di `postman_collection.json`.

# backend-test-adhivasindo
