# Assalamualaikum Wr.Wb
Saya Muhammad Zamzam Alfadlil, dengan NIM. 23552011115, telah menyelesaikan tugas UAS Pemrograman Web 2 dengan membuat Web "Laundry OS",
Laundry OS adalah sebuah sistem informasi manajemen dan kasir laundry digital berbasis web yang dibangun menggunakan PHP Native dan MySQL. Aplikasi ini tidak memisahkan frontend dan backend secara independen, melainkan menggunakan arsitektur monolith di mana logika backend (PHP & SQL) dan tampilan frontend (HTML & CSS) terintegrasi langsung dalam satu kesatuan file.

Aplikasi ini dilengkapi dengan sistem autentikasi dua pintu (*Role-Based Access Control*):

1. Panel Admin (Owner): Diakses melalui `admin.php`, berfungsi sebagai dasbor analitik untuk memantau total pendapatan, kuantitas produksi, beban kerja antrean, serta mengelola (CRUD) daftar layanan dan harga.
2. Panel Kasir (Operator): Diakses melalui `index.php`, didesain khusus untuk operasional harian. Kasir dapat mencatat transaksi baru, mengubah status pesanan pelanggan (Proses -> Selesai -> Diambil), dan mencetak struk nota digital.

---

### Panduan Langkah-Demi-Langkah Menjalankan Aplikasi di Lokal

Berikut adalah instruksinya:

1. Buka tab baru di browser Anda.
2. Ketikkan URL berikut untuk mengakses aplikasi: `http://localhost/Website_Loundry/` (Sesuaikan bagian `Website_Loundry` dengan nama folder yang Anda taruh di `htdocs`).
3. Anda akan otomatis diarahkan ke halaman `login.php`.
4. Gunakan kredensial bawaan berikut untuk masuk ke dalam sistem:
* **Akses Admin / Owner**
* Username: `adminlaundry`
* Password: `admin123`

* **Akses Kasir / Operator**
* Username: `kasirlaundry`
* Password: `kasir123`

* Setelah itu Aplikasi dapat dijalankan..
* Selamat Mencoba
