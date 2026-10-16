# Cargo & Collie Logistics Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/PostgreSQL-16+-336791?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License">
</p>

---

## 📌 Tentang Sistem (Overview)

**Cargo & Collie Logistics Management System** adalah aplikasi berbasis web modern yang dirancang untuk mengelola dan memonitor operasional logistik, pergudangan, pencatatan muatan koli (**Collie**), **Surat Muat (SM)**, **SPPB (Surat Perintah Pengeluaran Barang)**, serta pembuatan dokumen cetak resmi **Packing List**.

Aplikasi ini merupakan hasil modernisasi dari sistem desktop legacy (*Visual Basic 6*) ke arsitektur web modern berbasis **Laravel 12 (PHP 8.2)** dan **PostgreSQL RDBMS**, memberikan aksesibilitas terpusat, keamanan terjamin, dan pemrosesan data pengiriman yang cepat serta akurat.

---

## 🚀 Fitur Utama (Key Features)

- **Manajemen Collie & Muatan (Cargo Management)**
  - Pengelolaan nomor kemasan / koli tunggal maupun rentang (*range*, contoh: `1-62`, `335-666`).
  - Kalkulasi otomatis jumlah box (`QTY1 = JUCOL - AD + 1`).
  - Kalkulasi total fisik barang (`QTY = QTY1 × QTY2`).
  - Kalkulasi dan rekapitulasi berat kotor / neto barang (`QTYR`).

- **SPPB & Surat Muat (SM) Dispatch**
  - Pencatatan nomor SPPB, tanggal pengeluaran, ekspedisi/kurir, dan nomor polisi armada / nama pengemudi.
  - Verifikasi dan monitoring status pengiriman barang dari gudang ke cabang atau distributor.

- **Cetak Dokumen Resmi Packing List**
  - Tampilan siap cetak (*Print-Ready*) dengan format standar industri (sesuai formulir mutu perusahaan).
  - Ringkasan total koli, total berat (KG), rincian nomor collie, nama barang, dan tanda tangan otorisasi.

- **Master Data & Referensi**
  - Master Distributor / Customer.
  - Master Ekspedisi & Jasa Pengiriman.
  - Master Stok & Katalog Produk / Barang.
  - Master Pengguna (Manajemen user, role, dan profil).

- **Dashboard & Analitik**
  - Ringkasan transaksi harian dan bulanan.
  - Status pengiriman, volume muatan koli, dan rekapitulasi pengiriman per cabang/wilayah.

- **Import & Export Data**
  - Fitur import data lama (CSV/Excel) langsung via CLI Artisan Command (`import:collie-csv`).
  - Ekspor laporan data muatan ke format spreadsheet.

---

## 🛠️ Tech Stack & Requirements

- **Bahasa & Framework**: PHP >= 8.2, Laravel 12.x
- **Basis Data**: PostgreSQL (atau MySQL 8.0+)
- **Frontend**: Blade Templating, Vanilla CSS / Tailwind, JavaScript, Vite
- **Web Server**: Apache / Nginx / Docker Engine

---

## ⚙️ Panduan Instalasi (Installation Guide)

Ikuti langkah-langkah berikut untuk menjalankan project di lingkungan lokal:

### 1. Clone Repository
```bash
git clone https://github.com/baccend12dev/cargo-collie-logistics-system.git
cd cargo-collie-logistics-system
```

### 2. Install Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi environment dan sesuaikan koneksi database Anda:
```bash
cp .env.example .env
```
Sesuaikan pengaturan database pada `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cargo_collie
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Seeder Database
```bash
php artisan migrate --seed
```

### 6. Build Aset Frontend & Jalankan Server
Buka dua terminal terpisah atau jalankan secara concurrent:

**Terminal 1 (Vite Dev):**
```bash
npm run dev
```

**Terminal 2 (Laravel Dev Server):**
```bash
php artisan serve
```

Aplikasi siap diakses melalui browser di: `http://localhost:8000`

---

## 📐 Logika & Rumus Bisnis Utama

| Variabel | Keterangan | Rumus / Ketentuan |
| :--- | :--- | :--- |
| `AD` | Nomor Urut Collie Awal | Integer (dimulai dari `1`) |
| `JUCOL` | Nomor Urut Collie Akhir | Integer (`JUCOL >= AD`) |
| `NO_COL` | Display Nomor Collie | `AD == JUCOL` $\rightarrow$ `"AD"`<br>`AD != JUCOL` $\rightarrow$ `"AD - JUCOL"` |
| `QTY1` | Jumlah Kemasan / Box | $\text{JUCOL} - \text{AD} + 1$ |
| `QTY2` | Kapasitas / Isi per Box | Nilai default dari master item / input |
| `QTY` | Total Satuan Produk | $\text{QTY1} \times \text{QTY2}$ |
| `QTYR` | Total Berat (KG) | Berat item $\times$ QTY |

---

## 📂 Struktur Modul Utama

```
app/
├── Console/Commands/       # Command import batch CSV/data legacy
├── Http/Controllers/       # Controller Collie, SPPB, Report, Dashboard, Auth
├── Models/                 # CollieSppb, Distributor, OttoMasterStock, SmOracle, User
database/
├── migrations/             # Migrasi schema tabel collie, sppb, ekspedisi, distributor
├── seeders/                # Seeder database awal
resources/views/
├── auth/                   # Tampilan login & autentikasi
├── collie/                 # Form & daftar data Collie
├── sppb/                   # Manajemen SPPB & Dokumen Cetak Packing List
├── dashboard/              # Ringkasan analitik & metrik
├── report/                 # Laporan logistik
└── layouts/                # Layout master & komponen navigasi
```

---

## 📄 Lisensi (License)

Proyek ini dilisensikan di bawah lisensi open-source [MIT License](LICENSE).
