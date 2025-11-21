# 🧩 ITS SkillShare

ITS SkillShare adalah aplikasi web sederhana untuk mempertemukan **penyedia jasa (seller)** dan **pencari jasa (customer)**.  
Aplikasi memiliki dua peran utama: **Admin** dan **Customer**. Admin dapat mengelola pengguna, jasa, serta pesanan; sedangkan customer dapat membuat dan memantau pesanan.

> Tampilan awal dashboard admin:
>
<img width="2555" height="1423" alt="image" src="https://github.com/user-attachments/assets/24270c7e-86a9-4661-aeec-0d8ed61b3fc4" />


---

## ✨ Fitur Utama

- 🔐 **Autentikasi dua peran**: Admin & Customer (demo credential di bawah)
- 🧑‍💼 **Manajemen Pengguna (Admin)**: tambah, edit, hapus user
- 🎁 **Manajemen Jasa (Admin)**: tambah, edit, arsip/hapus jasa
- 🧾 **Manajemen Pesanan (Admin)**: lihat semua pesanan, update status (baru, diproses, selesai), edit, atau hapus
- 🚫 **Penegakan Ketentuan (Admin)**: hapus nama/pengguna **pelanggar ketentuan**
- 🛍️ **Pesan Jasa (Customer)**: buat pesanan, unggah detail kebutuhan, pantau status
- 🔎 **Pencarian & Filter**: cari pengguna/jasa/pesanan dengan cepat
- 📊 **Ringkasan Dashboard**: kartu ringkas untuk total pengguna, jasa, dan pesanan
- 💾 **Penyimpanan lokal** (default): data disimpan sementara (mis. `localStorage`) untuk keperluan demo—mudah diganti ke API/DB di kemudian hari
- 💡 **UI clean & responsif** dengan micro-interactions

---

## 🧠 Peran & Hak Akses

### Admin
- Melihat ringkasan data di **Dashboard**
- **Kelola Pengguna**: tambah, edit, hapus
- **Kelola Jasa**: tambah, edit, hapus/arsip
- **Kelola Pesanan**: ubah status, edit detail, hapus
- **Penegakan ketentuan**: menghapus nama/user yang melakukan pelanggaran
- Logout

### Customer
- Melihat daftar jasa
- Membuat pesanan baru
- Melihat riwayat & status pesanan
- Edit/Hapus pesanan milik sendiri
- Logout

---
