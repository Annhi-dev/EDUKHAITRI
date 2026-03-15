# PROMPT GEMINI AGENT — PHASE 1
## Authentication · Phân Quyền · Trang Chủ
### Dự án: Hệ thống Quản lý Giảng viên – Học viện (Laravel)

---

## 🧠 CONTEXT DỰ ÁN

```
Framework  : Laravel (project đã khởi tạo, chưa có code nghiệp vụ)
Database   : MySQL
Auth       : Laravel Breeze hoặc Fortify + Spatie Laravel Permission
Vai trò    : admin | giang_vien | hoc_vien
Giao diện  : Blade + Tailwind CSS (hoặc Bootstrap 5 — chọn 1)
```

---

## 📦 PHASE 1 — MỤC TIÊU

Xây dựng hoàn chỉnh:
1. Hệ thống đăng nhập / đăng xuất
2. Phân quyền 3 vai trò: Admin, Giảng viên, Học viên
3. Dashboard trang chủ riêng cho từng vai trò
4. Middleware bảo vệ route theo vai trò
5. Seeder dữ liệu mẫu

---

## 📋 PROMPT CHI TIẾT GỬI GEMINI AGENT

---

### BƯỚC 1 — Cài đặt package & cấu hình ban đầu

```
Tôi có project Laravel mới (chưa làm gì). Hãy thực hiện tuần tự:

1. Cài đặt Spatie Laravel Permission:
   composer require spatie/laravel-permission

2. Publish migration của Spatie:
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

3. Cài đặt Laravel Breeze (Blade stack):
   composer require laravel/breeze --dev
   php artisan breeze:install blade

4. Chạy migration:
   php artisan migrate

5. Cài frontend:
   npm install && npm run build

Sau khi xong, xác nhận các file đã tạo và kiểm tra lỗi nếu có.
```

---

### BƯỚC 2 — Cấu hình Model User & HasRoles

```
Hãy chỉnh sửa file app/Models/User.php như sau:

- Thêm trait: use Spatie\Permission\Traits\HasRoles;
- Thêm HasRoles vào class User
- Thêm các trường vào $fillable: 'name', 'email', 'password', 'role', 'phone', 'avatar', 'is_active'
- Thêm cột vào migration users table (tạo migration mới):
  - phone: string, nullable
  - avatar: string, nullable  
  - is_active: boolean, default true
  - role: enum('admin','giang_vien','hoc_vien'), default 'hoc_vien'

Tạo migration thêm các cột trên vào bảng users, sau đó chạy migrate.

Cho tôi xem nội dung file User.php sau khi chỉnh sửa.
```

---

### BƯỚC 3 — Tạo Roles, Permissions & Seeder

```
Hãy tạo file database/seeders/RolePermissionSeeder.php với nội dung sau:

Tạo các ROLES:
- admin
- giang_vien  
- hoc_vien

Tạo các PERMISSIONS theo nhóm:

Nhóm user_management (chỉ admin):
- manage_users
- create_user
- edit_user
- delete_user
- view_users

Nhóm schedule (admin + giang_vien):
- manage_schedule       → admin
- view_schedule         → admin, giang_vien, hoc_vien
- request_change_schedule → giang_vien

Nhóm class_management (admin + giang_vien):
- manage_classes        → admin
- view_classes          → admin, giang_vien
- manage_attendance     → giang_vien
- view_attendance       → giang_vien, hoc_vien

Nhóm grade_management:
- manage_grades         → giang_vien
- view_grades           → giang_vien, hoc_vien

Nhóm evaluation:
- manage_evaluation     → admin
- evaluate_student      → giang_vien
- evaluate_course       → hoc_vien

Sau đó gán permissions vào đúng role.

Tạo thêm DatabaseSeeder gọi RolePermissionSeeder.

Tạo thêm UserSeeder tạo 3 tài khoản mẫu:
- admin@academy.com / password123 → role: admin
- gv@academy.com   / password123 → role: giang_vien  
- hv@academy.com   / password123 → role: hoc_vien

Chạy: php artisan db:seed
```

---

### BƯỚC 4 — Tạo Middleware Phân Quyền

```
Hãy tạo 3 middleware trong app/Http/Middleware/:

1. File: CheckAdmin.php
   - Kiểm tra user đã login VÀ có role 'admin'
   - Nếu không: redirect về route('home') kèm flash message 'Bạn không có quyền truy cập.'

2. File: CheckGiangVien.php
   - Kiểm tra user đã login VÀ có role 'giang_vien' HOẶC 'admin'
   - Nếu không: redirect về route('home')

3. File: CheckHocVien.php
   - Kiểm tra user đã login VÀ có role 'hoc_vien'
   - Nếu không: redirect về route('home')

Đăng ký cả 3 middleware trong bootstrap/app.php (Laravel 11) hoặc app/Http/Kernel.php (Laravel 10) với alias:
- 'admin'      => CheckAdmin::class
- 'giang_vien' => CheckGiangVien::class
- 'hoc_vien'   => CheckHocVien::class

Cho tôi xem code từng file middleware.
```

---

### BƯỚC 5 — Cấu hình Routes theo vai trò

```
Hãy tạo/chỉnh sửa file routes/web.php với cấu trúc sau:

// Route công khai
Route::get('/', fn() => redirect()->route('login'));

// Route Auth (do Breeze tạo sẵn — giữ nguyên)
require __DIR__.'/auth.php';

// Sau khi đăng nhập → điều hướng về đúng dashboard theo role
Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ===== ADMIN ROUTES =====
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    });

    // ===== GIẢNG VIÊN ROUTES =====
    Route::middleware('giang_vien')->prefix('giang-vien')->name('gv.')->group(function () {
        Route::get('/dashboard', [GiangVien\DashboardController::class, 'index'])->name('dashboard');
    });

    // ===== HỌC VIÊN ROUTES =====
    Route::middleware('hoc_vien')->prefix('hoc-vien')->name('hv.')->group(function () {
        Route::get('/dashboard', [HocVien\DashboardController::class, 'index'])->name('dashboard');
    });
});

Tạo file HomeController.php: kiểm tra role của user đã đăng nhập và redirect về đúng dashboard.

Logic redirect trong HomeController@index:
- role = admin      → redirect route('admin.dashboard')
- role = giang_vien → redirect route('gv.dashboard')
- role = hoc_vien   → redirect route('hv.dashboard')
- không có role     → redirect login với message lỗi

Cấu hình RouteServiceProvider (hoặc trong Breeze) để sau login redirect về route('home').
```

---

### BƯỚC 6 — Tạo Controllers & Views Dashboard

```
Hãy tạo 3 controller và view dashboard tương ứng:

=== 1. ADMIN DASHBOARD ===

Tạo: app/Http/Controllers/Admin/DashboardController.php
- Method index(): trả về view 'admin.dashboard'
- Truyền vào view các biến thống kê:
  $totalGiangVien = User::role('giang_vien')->count()
  $totalHocVien   = User::role('hoc_vien')->count()
  $totalAdmin     = User::role('admin')->count()

Tạo: resources/views/admin/dashboard.blade.php
Layout: sidebar bên trái + content bên phải
Header: "Xin chào, {{ Auth::user()->name }}" + nút Đăng xuất
Sidebar menu:
  - Dashboard (active)
  - Quản lý người dùng
  - Thời khóa biểu
  - Đánh giá chất lượng
  - Khóa học & Lớp
  - Báo cáo
  - Cấu hình hệ thống
Content: 3 card thống kê (Giảng viên / Học viên / Admin)
Style: Tailwind CSS, màu chủ đạo tím (purple-600 / purple-700)

=== 2. GIẢNG VIÊN DASHBOARD ===

Tạo: app/Http/Controllers/GiangVien/DashboardController.php
- Method index(): trả về view 'giang_vien.dashboard'
- Truyền vào: $user = Auth::user()

Tạo: resources/views/giang_vien/dashboard.blade.php
Layout: sidebar + content
Header: "Xin chào Thầy/Cô {{ Auth::user()->name }}"
Sidebar menu:
  - Dashboard (active)
  - Lịch dạy
  - Quản lý lớp
  - Điểm danh
  - Quản lý điểm
  - Đánh giá học viên
  - Hồ sơ cá nhân
Content: card thông tin nhanh (lịch hôm nay, số lớp phụ trách, số học viên)
Style: Tailwind CSS, màu chủ đạo xanh lá (green-600 / emerald-700)

=== 3. HỌC VIÊN DASHBOARD ===

Tạo: app/Http/Controllers/HocVien/DashboardController.php
- Method index(): trả về view 'hoc_vien.dashboard'

Tạo: resources/views/hoc_vien/dashboard.blade.php
Layout: sidebar + content
Header: "Xin chào {{ Auth::user()->name }}"
Sidebar menu:
  - Dashboard (active)
  - Lịch học
  - Kết quả học tập
  - Điểm danh
  - Đánh giá khóa học
  - Hồ sơ cá nhân
Content: card (lịch hôm nay, điểm trung bình, số khóa đang học)
Style: Tailwind CSS, màu chủ đạo xanh dương (blue-600 / blue-700)
```

---

### BƯỚC 7 — Trang Login tùy chỉnh

```
Hãy tùy chỉnh trang login mặc định của Breeze (resources/views/auth/login.blade.php):

1. Thêm logo / tên học viện ở trên form: "HỌC VIỆN ABC"
2. Thêm subtitle: "Hệ thống Quản lý Đào tạo"
3. Form giữ nguyên email + password + remember me
4. Thêm phần thông tin đăng nhập demo nhỏ phía dưới form (chỉ khi APP_ENV=local):
   - Admin: admin@academy.com
   - Giảng viên: gv@academy.com
   - Học viên: hv@academy.com
5. Style đẹp với Tailwind, nền gradient nhẹ, card trắng bo góc, shadow

Đồng thời chỉnh sửa LoginController hoặc AuthenticatedSessionController để sau khi login:
- Gọi redirect()->route('home') thay vì RouteServiceProvider::HOME
```

---

### BƯỚC 8 — Trang Hồ sơ cá nhân (dùng chung 3 vai trò)

```
Tạo ProfileController chung cho cả 3 vai trò tại:
app/Http/Controllers/ProfileController.php (Breeze đã tạo sẵn — mở rộng thêm)

Thêm method updateProfile():
- Validate: name (required), phone (nullable, digits:10), avatar (nullable, image, max:2048)
- Nếu có upload avatar: lưu vào storage/app/public/avatars/, cập nhật cột avatar
- Cập nhật name, phone
- Return back()->with('success', 'Cập nhật thành công!')

Tạo view resources/views/profile/edit.blade.php:
- Form cập nhật thông tin: Họ tên, Email (readonly), Số điện thoại, Avatar (upload preview)
- Form đổi mật khẩu: Mật khẩu hiện tại, Mật khẩu mới, Xác nhận mật khẩu
- Hiển thị flash message thành công/lỗi
- Layout kế thừa từ layout của từng vai trò (dùng @extends hoặc component)

Thêm route trong web.php (trong group auth):
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
```

---

### BƯỚC 9 — Kiểm thử & Xác nhận

```
Hãy thực hiện các bước kiểm tra sau và báo cáo kết quả:

1. Chạy: php artisan route:list | grep -E "admin|giang-vien|hoc-vien"
   → Liệt kê tất cả routes đã đăng ký

2. Thử đăng nhập với từng tài khoản:
   - admin@academy.com    → phải vào /admin/dashboard
   - gv@academy.com       → phải vào /giang-vien/dashboard
   - hv@academy.com       → phải vào /hoc-vien/dashboard

3. Thử truy cập chéo (học viên vào /admin/dashboard)
   → Phải bị redirect về /home với message lỗi

4. Kiểm tra: php artisan permission:cache-reset

5. Liệt kê toàn bộ file đã tạo trong phase này theo cấu trúc thư mục.

Nếu có lỗi, phân tích nguyên nhân và sửa ngay.
```

---

## 📁 CẤU TRÚC FILE SAU PHASE 1

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── DashboardController.php
│   │   ├── GiangVien/
│   │   │   └── DashboardController.php
│   │   ├── HocVien/
│   │   │   └── DashboardController.php
│   │   ├── HomeController.php
│   │   └── ProfileController.php (mở rộng)
│   └── Middleware/
│       ├── CheckAdmin.php
│       ├── CheckGiangVien.php
│       └── CheckHocVien.php
├── Models/
│   └── User.php (đã thêm HasRoles)
database/
├── migrations/
│   └── xxxx_add_fields_to_users_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── RolePermissionSeeder.php
    └── UserSeeder.php
resources/views/
├── auth/
│   └── login.blade.php (tùy chỉnh)
├── admin/
│   └── dashboard.blade.php
├── giang_vien/
│   └── dashboard.blade.php
├── hoc_vien/
│   └── dashboard.blade.php
└── profile/
    └── edit.blade.php
routes/
└── web.php (đã cấu hình đầy đủ)
```

---

## ✅ TIÊU CHÍ HOÀN THÀNH PHASE 1

- [ ] Login / Logout hoạt động
- [ ] 3 vai trò được tạo và gán permissions
- [ ] Redirect đúng dashboard theo role sau login
- [ ] Middleware chặn truy cập chéo giữa các vai trò
- [ ] Dashboard mỗi vai trò hiển thị đúng menu
- [ ] Trang hồ sơ cá nhân hoạt động (cập nhật thông tin + đổi mật khẩu)
- [ ] Seeder tạo được 3 tài khoản test

---

## 🔜 PHASE TIẾP THEO (SAU KHI PHASE 1 XONG)

- **Phase 2**: Quản lý người dùng (Admin CRUD Giảng viên & Học viên)
- **Phase 3**: Thời khóa biểu & Lịch dạy/học
- **Phase 4**: Điểm danh & Quản lý lớp
- **Phase 5**: Quản lý điểm & Đánh giá
- **Phase 6**: Báo cáo & Thống kê
