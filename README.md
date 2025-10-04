Fleetify Absen – Frontend (User Guide)
Ringkas

Aplikasi ini adalah Admin Frontend berbasis Laravel + Inertia + Vue 3 yang menampilkan data dari Backend API Laravel (repo: fleetify-absen-backend). Fokus dokumentasi: cara pakai dari sisi pengguna.

Prasyarat

Backend API sudah berjalan (local atau server) dan CORS mengizinkan origin frontend.

Frontend sudah disetel baseURL API di resources/js/utils/api.js:

// ganti sesuai backend kamu
export default axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: { Accept: 'application/json' }
})


Jika backend sudah dideploy (Render dsb.), ganti ke domain HTTPS backend.

Menjalankan Frontend
# install dependencies
composer install
npm install

# jalankan dev server
php artisan serve      # http://127.0.0.1:8000 (halaman Laravel)
npm run dev            # Vite dev server

# akses aplikasi admin melalui route web kamu (mis: /)


Jika memakai Inertia + Vue: pastikan Vite berjalan (terminal menampilkan “Vite vX running…”).

Dashboard – Cara Pakai
1) Masuk ke Dashboard

Buka halaman utama aplikasi admin.

Di bagian atas ada tombol pintasan:

Go to Departments → kelola data departemen.

View Employees → kelola data karyawan.

Attendance → halaman simulasi/aksi absensi (opsional).

2) Lihat Ringkasan (Summary Cards)

Departments: jumlah departemen yang terdaftar.

Employees: jumlah karyawan yang terdaftar.

Attendance Today:

Total: jumlah log absensi pada tanggal hari ini.

On-time In: jumlah karyawan yang check-in ≤ batas maksimal departemen.

Late In: jumlah karyawan yang check-in > batas maksimal.

On-time Out: jumlah yang check-out ≥ batas maksimal.

Early Leave: jumlah yang check-out < batas maksimal.

Not Out: belum melakukan check-out.

Semua angka dihitung dari endpoint:

GET /api/departments

GET /api/employees

GET /api/attendance/logs?date_from=<today>&date_to=<today>

3) Tabel Latest Attendance (Today)

Menampilkan beberapa log absensi terbaru untuk tanggal hari ini.

Kolom:

Tanggal (work_date)

Emp ID (employee.id)

Nama (employee.name)

Departemen (employee.department.code / name)

In (check_in_at)

Status In (on_time/late/absent)

Out (check_out_at)

Status Out (on_time/early_leave/not_checked_out)

Catatan Kesesuaian Field (penting, biar tampilan benar)

Di kode template Dashboard kamu, ada beberapa nama field yang perlu disesuaikan dengan respons backend:

Ganti r.clock_in → r.check_in_at

Ganti r.clock_out → r.check_out_at

Untuk departemen, gunakan r.employee?.department?.code atau .name, bukan r.employee?.department langsung.

Contoh bagian tabel yang sudah disesuaikan:

<td>{{ r.employee?.department?.code ?? r.employee?.department?.name ?? '-' }}</td>
<td>{{ r.check_in_at ?? '-' }}</td>
<td>{{ r.status_in }}</td>
<td>{{ r.check_out_at ?? '-' }}</td>
<td>{{ r.status_out }}</td>

Alur Data di Dashboard (untuk developer)

Saat mount, Dashboard memanggil 3 endpoint sekaligus:

api.get('/departments')
api.get('/employees')
api.get('/attendance/logs', { params: { date_from: today, date_to: today }})


Data yang dipakai bisa berbentuk array atau pagination (Laravel). Karena itu kode melakukan unwrap:

const deps = depRes.data.data ?? depRes.data
const emps = empRes.data.data ?? empRes.data
const logs = logRes.data.data ?? logRes.data


Ringkasan menghitung jumlah on-time/late/not_checked_out/early_leave dari array logs.

Troubleshooting Cepat

Angka 0 semua / No data

Pastikan seeders backend sudah jalan (php artisan db:seed).

Coba manual di Postman: GET /api/attendance/logs?date_from=<today>&date_to=<today>.

CORS error di browser

Cek config/cors.php backend → allowed_origins harus memuat origin frontend (localhost:5173 atau domain Pages).

php artisan config:clear.

Axios 404

Pastikan baseURL benar.

Cek php artisan route:list --path=api di backend.

Tanggal “hari ini”

Fungsi todayStr() memakai waktu browser (UTC vs lokal bisa beda). Jika perlu tepat zona Asia/Jakarta, pertimbangkan library date atau gunakan tanggal dari server.

Endpoint Referensi (yang dipakai Dashboard)

GET /api/departments – daftar departemen (paginate).

GET /api/employees – daftar karyawan (paginate).

GET /api/attendance/logs?date_from=YYYY-MM-DD&date_to=YYYY-MM-DD – log hari ini:

Field penting: employee.{id,name,employee_code,department.{id,name,code}}, work_date, check_in_at, status_in, check_out_at, status_out.
