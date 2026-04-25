# 🧺 Laundry App — Manual Book

> Aplikasi manajemen laundry berbasis web menggunakan **Laravel** dengan autentikasi **JWT API** dan tampilan **Blade**.

---

## 📋 Daftar Isi

- [Deskripsi Aplikasi](#-deskripsi-aplikasi)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Struktur Database](#-struktur-database)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Fitur Aplikasi](#-fitur-aplikasi)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [API Endpoint](#-api-endpoint)
- [Struktur Folder](#-struktur-folder)
- [Akun Default](#-akun-default)

---

## 📖 Deskripsi Aplikasi

Laundry App adalah aplikasi web untuk mengelola pesanan laundry. Pengguna dapat melakukan registrasi, login, membuat order laundry, dan melakukan pembayaran. Sistem menggunakan dua mekanisme autentikasi:

- **API JWT** → untuk login & register via API (cocok untuk mobile/Postman)
- **Session Blade** → untuk tampilan web (orders & payments)

---

## 🛠 Teknologi yang Digunakan

| Teknologi | Versi |
|-----------|-------|
| PHP | 8.1+ |
| Laravel | 10.x |
| MySQL | 8.0+ |
| JWT Auth | php-open-source-saver/jwt-auth |
| Bootstrap | 5.3.2 |
| Axios | Latest |
| Laragon | Windows (rekomendasi) |

---

## 🗄 Struktur Database

### Tabel `app_users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| name | varchar | Nama pengguna |
| email | varchar | Email unik |
| password | varchar | Password terenkripsi |
| role | enum | admin / user |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel `laundry_services`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| name | varchar | Nama layanan |
| price_per_kg | decimal | Harga per kg |
| duration_days | int | Estimasi hari selesai |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel `laundry_orders`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key ke app_users |
| service_id | bigint | Foreign key ke laundry_services |
| weight_kg | decimal | Berat cucian |
| total_price | decimal | Total harga |
| status | enum | pending / processing / completed / cancelled |
| notes | text | Catatan order |
| pickup_date | timestamp | Tanggal pickup |
| delivery_date | timestamp | Tanggal selesai |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel `payments`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| order_id | bigint | Foreign key ke laundry_orders |
| amount | decimal | Jumlah pembayaran |
| payment_method | enum | cash / transfer / ewallet |
| payment_status | enum | paid / unpaid |
| payment_date | timestamp | Tanggal bayar |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## ⚙️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/luthfiartdean/ujk-luthf1.git
cd ujk-luthf1
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Copy File Environment

```bash
cp .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Generate JWT Secret

```bash
php artisan jwt:secret
```

---

## 🔧 Konfigurasi

Buka file `.env` dan sesuaikan konfigurasi database:

```env
APP_NAME="Laundry App"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundry_app
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=isi_otomatis_setelah_jwt:secret
JWT_TTL=60
```

---

## 🚀 Menjalankan Aplikasi

### 1. Buat Database

Buka phpMyAdmin atau MySQL, buat database baru:

```sql
CREATE DATABASE laundry_app;
```

### 2. Jalankan Migration

```bash
php artisan migrate
```

### 3. Isi Data Awal (Seeder)

```bash
php artisan db:seed
```

Atau isi data service manual via tinker:

```bash
php artisan tinker
```

```php
App\Models\LaundryService::insert([
    ['name' => 'Cuci Reguler',   'price_per_kg' => 6000,  'duration_days' => 3, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Cuci Express',   'price_per_kg' => 10000, 'duration_days' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Cuci + Setrika', 'price_per_kg' => 9000,  'duration_days' => 2, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Dry Cleaning',   'price_per_kg' => 25000, 'duration_days' => 4, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Cuci Selimut',   'price_per_kg' => 15000, 'duration_days' => 2, 'created_at' => now(), 'updated_at' => now()],
]);
```

### 4. Jalankan Server

```bash
php artisan serve
```

Buka browser: **http://127.0.0.1:8000**

---

## ✨ Fitur Aplikasi

| Fitur | Keterangan |
|-------|------------|
| ✅ Register | Daftar akun baru |
| ✅ Login | Masuk via web (session) atau API (JWT) |
| ✅ Logout | Keluar dari aplikasi |
| ✅ Buat Order | Pilih service, input berat & tanggal pickup |
| ✅ Lihat Order | Daftar semua order milik user |
| ✅ Update Order | Ubah status dan catatan order |
| ✅ Hapus Order | Hapus order berstatus pending |
| ✅ Pembayaran | Bayar order via cash, transfer, atau e-wallet |
| ✅ Riwayat Bayar | Lihat semua riwayat pembayaran |

---

## 📱 Panduan Penggunaan

### Register Akun Baru

1. Buka **http://127.0.0.1:8000/register**
2. Isi **Nama**, **Email**, **Password**, dan **Konfirmasi Password**
3. Klik tombol **Daftar**
4. Otomatis diarahkan ke halaman Orders

### Login

1. Buka **http://127.0.0.1:8000/login**
2. Isi **Email** dan **Password**
3. Klik tombol **Login**
4. Otomatis diarahkan ke halaman Orders

### Membuat Order

1. Di halaman **Orders**, isi form sebelah kiri:
   - Pilih **Service** (jenis layanan)
   - Isi **Berat (kg)**
   - Pilih **Pickup Date** (opsional)
   - Isi **Notes** (opsional)
2. Klik **+ Buat Order**
3. Order muncul di daftar sebelah kanan

### Melakukan Pembayaran

1. Di daftar order, cari order dengan status **Belum Bayar**
2. Klik tombol **💳 Bayar**
3. Halaman pembayaran menampilkan detail order
4. Pilih metode pembayaran: **Cash**, **Transfer Bank**, atau **E-Wallet**
5. Klik **Bayar Sekarang**
6. Status order berubah menjadi **✓ Lunas**

### Update Status Order

1. Di daftar order, pilih status baru dari dropdown
2. Edit notes jika perlu
3. Klik **Update**

### Logout

1. Klik tombol **Logout** di pojok kanan atas

---

## 🔌 API Endpoint

Base URL: `http://127.0.0.1:8000/api`

### Auth (Publik)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/api/register` | Daftar akun baru |
| POST | `/api/login` | Login, mendapat JWT token |

### Auth (Butuh Token)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/profile` | Data user yang login |
| POST | `/api/logout` | Logout, invalidate token |
| POST | `/api/refresh` | Refresh JWT token |

### Services (Butuh Token)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/services` | Daftar semua layanan |

### Orders (Butuh Token)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/orders` | Daftar order milik user |
| POST | `/api/orders` | Buat order baru |
| PUT | `/api/orders/{id}` | Update order |
| DELETE | `/api/orders/{id}` | Hapus order |

### Contoh Request Login (Postman)

```json
POST /api/login
Content-Type: application/json

{
    "email": "user@laundry.com",
    "password": "password123"
}
```

### Contoh Response Login

```json
{
    "status": true,
    "message": "Login berhasil.",
    "data": {
        "user": {
            "id": 1,
            "name": "Pelanggan",
            "email": "user@laundry.com"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "type": "bearer"
    }
}
```

### Contoh Request Buat Order (dengan Token)

```json
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "service_id": 1,
    "weight_kg": 3.5,
    "pickup_date": "2026-04-25T10:00",
    "notes": "Cuci terpisah warna putih"
}
```

---

## 📁 Struktur Folder

```
laundry-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          ← JWT API login/register
│   │   │   ├── LaundryOrderController.php  ← CRUD order (blade + API)
│   │   │   └── PaymentController.php       ← CRUD payment (blade)
│   │   └── Middleware/
│   │       └── Authenticate.php            ← Redirect ke /login
│   └── Models/
│       ├── User.php
│       ├── LaundryService.php
│       ├── LaundryOrder.php
│       └── Payment.php
├── database/
│   ├── migrations/                         ← Struktur tabel
│   └── seeders/                            ← Data awal
├── public/
│   └── js/
│       └── app.js                          ← Helper JWT frontend
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php               ← Layout utama
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── orders/
│       │   └── index.blade.php
│       └── payments/
│           ├── index.blade.php
│           └── create.blade.php
└── routes/
    ├── web.php                             ← Route blade (session)
    └── api.php                             ← Route API (JWT)
```

---

## 👤 Akun Default

Setelah menjalankan seeder:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@laundry.com | password123 |
| User | user@laundry.com | password123 |

---

## ⚠️ Troubleshooting

### Error: Undefined variable $services
Pastikan method `index()` di `LaundryOrderController` mengirim `$services`:
```php
$services = LaundryService::all();
return view('orders.index', compact('orders', 'services'));
```

### Redirect ke /home setelah login
Ubah di `app/Http/Middleware/Authenticate.php`:
```php
return $request->expectsJson() ? null : '/login';
```
Dan di `app/Providers/RouteServiceProvider.php`:
```php
public const HOME = '/orders';
```

### Dropdown service kosong
Pastikan tabel `laundry_services` sudah terisi datanya:
```bash
php artisan tinker
App\Models\LaundryService::count();
```

### Token JWT expired
```bash
POST /api/refresh
Authorization: Bearer {token_lama}
```

---

## 📝 Catatan

- JWT token berlaku selama **60 menit** (dapat diubah di `.env` → `JWT_TTL`)
- Order hanya bisa dihapus jika status masih **pending**
- Pembayaran otomatis mengambil total dari `total_price` order

---

> Dibuat untuk keperluan UJK — Luthfi | 2026
