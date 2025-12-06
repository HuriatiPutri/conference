Bangun fitur JOIV Registration dengan spesifikasi berikut:

1. Halaman Registrasi JOIV

Buat halaman baru dengan route: /joiv/registration
Halaman ini public, tidak memerlukan authentication.

Form registrasi berisi field:

- first_name
- last_name
- email_address
- phone_number
- institution
- country
- paper_id
- paper_title
- full_paper (upload file / full paper attachment)

Flow registrasi mengikuti alur yang sama dengan proses registrasi audience yang sudah ada:

- Public registration form
- Payment integration (PayPal, Bank Transfer)
- Payment status management
- Search and filter audiences
- Export audience data to Excel
- Download payment receipts
- View audience details and papers

Safety checking untuk email/phone jika diperlukan

UI mengikuti style existing project untuk konsisten.

2. Halaman Admin untuk JOIV

Tambahkan satu menu baru pada admin panel:

Nama menu: "Joiv Article"

Menu ini menampilkan table list seluruh user yang melakukan registrasi JOIV.

Field yang ditampilkan minimal:

- first_name
- last_name
- email_address
- phone_number
- institution
- country
- paper_id
- paper_title
- link/full_paper download
- status pembayaran
- created_at

Tambahkan pagination, search, dan filter by country / institution bila memungkinkan.
Akses menu ini hanya untuk admin role.

3. Backend Requirements

- Buat model / tabel database baru: joiv_registrations
- Field mengikuti daftar di atas.
- Simpan file full_paper (jika ada) di storage sesuai standar proyek.

4. Deliverables yang Diharapkan

- UI halaman registrasi JOIV lengkap dan berfungsi
- Route /joiv/registration
- Validasi form
- Penambahan menu admin "Joiv Article"
- Table list data JOIV di admin
- Endpoint backend + model/tables
- Dokumentasi singkat cara memakai fitur
