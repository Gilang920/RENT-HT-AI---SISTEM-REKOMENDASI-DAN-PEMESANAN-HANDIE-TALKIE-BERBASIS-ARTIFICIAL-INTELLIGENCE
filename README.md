# 📻 RENT-HT AI — Sistem Rekomendasi & Pemesanan Handie Talkie Berbasis AI

> Platform pemesanan dan sistem rekomendasi Handie Talkie (HT) cerdas berbasis *Artificial Intelligence* (AI) yang membantu pengelola event, panitia, dan operasional memilih unit HT sesuai jangkauan, frekuensi, serta kebutuhan medan secara otomatis dan cepat.

---

## 🚀 Fitur Utama

### 🧠 Engine Rekomendasi AI
- **Analisis Kebutuhan Event:** Algoritma AI menganalisis parameter input (jumlah personil, jarak jangkauan, area indoor/outdoor, serta hambatan geografis) untuk merekomendasikan tipe HT terbaik.
- **Pencocokan Spesifikasi Otomatis:** Menyarankan tipe frekuensi (VHF/UHF) dan kapasitas baterai yang tepat sesuai durasi acara.

### 👥 Portal Penyewa (User)
- **Rekomendasi Cerdas Interaktif:** Membantu penyewa yang awam menentukan unit HT tanpa bingung spesifikasi teknis.
- **Katalog Unit HT:** Informasi lengkap spesifikasi unit, jangkauan daya, dan tarif sewa per hari.
- **Pemesanan & Jadwal Sewa:** Pemilihan tanggal sewa, durasi, dan jumlah unit yang diinginkan secara real-time.
- **Upload Bukti Pembayaran:** Modul konfirmasi transfer pembayaran sewa.

### 👑 Panel Pengelola (Admin)
- **Dashboard Ringkasan Operasional:** Memantau ketersediaan stok unit HT, total transaksi sewa, dan pendapatan.
- **Kelola Unit & Biaya Sewa:** Manajemen data inventaris HT, spesifikasi teknis, serta opsi paket aksesoris (earpiece, ekstra baterai).
- **Verifikasi Pembayaran & Sewa:** Konfirmasi bukti transfer masuk dan pengelolaan status pengembalian unit.

---

## 🛠️ Teknologi yang Digunakan

- **Bahasa Pemrograman:** PHP, JavaScript, HTML5, CSS3
- **Fitur Kecerdasan Buatan:** AI Recommendation Engine Logic / Rule-Based Algorithm
- **Database:** MySQL / MariaDB
- **Frontend Framework:** Bootstrap 5 & Bootstrap Icons
- **Environment:** Local Server (XAMPP / Laragon)
- **IDE:** Visual Studio Code

---

## 📦 Struktur Direktori Proyek

```text
rent-ht-ai/
│
├── index.php                  # Halaman utama / katalog unit
├── rekomendasi_ai.php         # Modul sistem rekomendasi berbasis AI
├── member_dashboard.php       # Portal penyewa / member
├── admin_dashboard.php        # Panel utama pengelola rental
├── koneksi.php                # Konfigurasi koneksi database MySQL
├── sewa.php                   # Form pemesanan unit HT
├── bayar.php                  # Upload bukti pembayaran sewa
├── kelola_ht.php              # Manajemen stok dan spesifikasi HT (Admin)
├── verifikasi_pembayaran.php  # Verifikasi transaksi masuk (Admin)
└── uploads/                   # Direktori penyimpanan bukti transfer
