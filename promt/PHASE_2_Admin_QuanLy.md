# PROMPT GEMINI AGENT — PHASE 2
## Admin: Quản lý Người dùng · Thời Khóa Biểu · Đánh Giá Chất Lượng
### Dự án: Hệ thống Quản lý Giảng viên – Học viện (Laravel)

---

## 🧠 CONTEXT (nhắc lại để Gemini hiểu)

```
- Phase 1 đã hoàn thành: Auth, phân quyền 3 role, dashboard riêng từng vai trò
- Package đã có: Spatie Laravel Permission, Laravel Breeze
- Roles: admin | giang_vien | hoc_vien
- Stack: Laravel + Blade + Tailwind CSS + MySQL
- Tất cả route admin nằm trong prefix /admin, middleware 'admin', name 'admin.*'
```

---

# ══════════════════════════════════════
# MODULE A — QUẢN LÝ NGƯỜI DÙNG
# ══════════════════════════════════════

---

## BƯỚC A1 — Migration & Model bổ sung

```
Hãy tạo migration mới để bổ sung bảng hỗ trợ quản lý người dùng:

=== Bảng: giang_vien_profiles ===
Schema::create('giang_vien_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('ma_giang_vien')->unique();       // Mã GV: GV001
    $table->string('chuyen_mon')->nullable();         // Chuyên môn: Toán, Lý...
    $table->string('hoc_vi')->nullable();             // Học vị: Thạc sĩ, Tiến sĩ
    $table->string('so_cmnd')->nullable();
    $table->date('ngay_sinh')->nullable();
    $table->enum('gioi_tinh', ['nam','nu','khac'])->nullable();
    $table->string('dia_chi')->nullable();
    $table->date('ngay_vao_lam')->nullable();
    $table->enum('trang_thai', ['dang_day','nghi_phep','da_nghi'])->default('dang_day');
    $table->timestamps();
});

=== Bảng: hoc_vien_profiles ===
Schema::create('hoc_vien_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('ma_hoc_vien')->unique();          // Mã HV: HV001
    $table->date('ngay_sinh')->nullable();
    $table->enum('gioi_tinh', ['nam','nu','khac'])->nullable();
    $table->string('so_cmnd')->nullable();
    $table->string('dia_chi')->nullable();
    $table->string('truong_tot_nghiep')->nullable();
    $table->date('ngay_nhap_hoc')->nullable();
    $table->enum('trang_thai', ['dang_hoc','bao_luu','da_tot_nghiep','da_nghi'])->default('dang_hoc');
    $table->timestamps();
});

Tạo 2 Model:
- app/Models/GiangVienProfile.php (belongsTo User)
- app/Models/HocVienProfile.php (belongsTo User)

Trong User.php thêm:
- public function giangVienProfile() { return $this->hasOne(GiangVienProfile::class); }
- public function hocVienProfile()   { return $this->hasOne(HocVienProfile::class); }

Chạy: php artisan migrate
```

---

## BƯỚC A2 — Controller Quản lý Người dùng

```
Tạo 2 controller riêng biệt:

=== 1. app/Http/Controllers/Admin/GiangVienController.php ===

Các method:
- index()   : Lấy danh sách user có role 'giang_vien', hỗ trợ search (name/email/ma_giang_vien)
              và filter theo trang_thai. Paginate 15 bản ghi.
              Return view('admin.giang_vien.index', compact('giangViens', 'filters'))

- create()  : Return view('admin.giang_vien.create')

- store()   : Validate rồi tạo User + GiangVienProfile trong DB Transaction
  Validate User:
    name     : required|string|max:100
    email    : required|email|unique:users
    password : required|min:8|confirmed
    phone    : nullable|digits:10
    avatar   : nullable|image|mimes:jpg,png,jpeg|max:2048
  Validate Profile:
    ma_giang_vien : required|unique:giang_vien_profiles
    chuyen_mon    : nullable|string
    hoc_vi        : nullable|string
    ngay_sinh     : nullable|date
    gioi_tinh     : nullable|in:nam,nu,khac
    dia_chi       : nullable|string
    ngay_vao_lam  : nullable|date
  Xử lý:
    - Upload avatar nếu có → storage/app/public/avatars/
    - Tạo User, gán role 'giang_vien'
    - Tạo GiangVienProfile liên kết
    - Return redirect()->route('admin.giang_vien.index')->with('success','Thêm giảng viên thành công!')

- show($id) : Xem chi tiết giảng viên + danh sách lớp đang dạy (sẽ join sau)
              Return view('admin.giang_vien.show', compact('giangVien'))

- edit($id) : Return view('admin.giang_vien.edit', compact('giangVien'))

- update($id) : Validate và cập nhật User + GiangVienProfile (tương tự store nhưng bỏ unique email nếu không đổi)
                Không bắt buộc đổi password (nếu trống thì giữ nguyên)
                Return redirect()->route('admin.giang_vien.index')->with('success','Cập nhật thành công!')

- destroy($id) : Soft delete (đặt is_active = false) — KHÔNG xóa thật
                 Nếu GV đang có lớp đang dạy → không cho xóa, báo lỗi
                 Return redirect()->back()->with('success'/'error', message)

- toggleActive($id) : Bật/tắt trạng thái is_active
                      Return redirect()->back()->with('success', message)

- resetPassword($id) : Reset password về 'password123', gửi flash message
                       Return redirect()->back()->with('success','Đã reset mật khẩu!')

=== 2. app/Http/Controllers/Admin/HocVienController.php ===

Tương tự GiangVienController nhưng dùng HocVienProfile và role 'hoc_vien':
- Các method: index, create, store, show, edit, update, destroy, toggleActive, resetPassword
- Validate thêm: truong_tot_nghiep, ngay_nhap_hoc, trang_thai (dang_hoc/bao_luu/da_tot_nghiep/da_nghi)
- index() thêm filter theo trang_thai học viên
```

---

## BƯỚC A3 — Routes Quản lý Người dùng

```
Thêm vào routes/web.php bên trong group middleware('admin')->prefix('admin')->name('admin.'):

// Quản lý Giảng viên
Route::resource('giang-vien', Admin\GiangVienController::class)->names([
    'index'   => 'admin.giang_vien.index',
    'create'  => 'admin.giang_vien.create',
    'store'   => 'admin.giang_vien.store',
    'show'    => 'admin.giang_vien.show',
    'edit'    => 'admin.giang_vien.edit',
    'update'  => 'admin.giang_vien.update',
    'destroy' => 'admin.giang_vien.destroy',
]);
Route::patch('giang-vien/{id}/toggle-active',  [Admin\GiangVienController::class, 'toggleActive'])->name('admin.giang_vien.toggle');
Route::patch('giang-vien/{id}/reset-password', [Admin\GiangVienController::class, 'resetPassword'])->name('admin.giang_vien.reset_password');

// Quản lý Học viên (tương tự)
Route::resource('hoc-vien', Admin\HocVienController::class)->names([...]);
Route::patch('hoc-vien/{id}/toggle-active',  [..., 'toggleActive'])->name('admin.hoc_vien.toggle');
Route::patch('hoc-vien/{id}/reset-password', [..., 'resetPassword'])->name('admin.hoc_vien.reset_password');
```

---

## BƯỚC A4 — Views Quản lý Người dùng

```
Tạo layout chung cho admin trước:
resources/views/layouts/admin.blade.php
- Sidebar cố định bên trái (240px)
- Sidebar có logo + menu items với icon (dùng Heroicons SVG inline)
- Menu items: Dashboard, Giảng viên, Học viên, Thời khóa biểu, Đánh giá, Báo cáo, Cấu hình
- Active state: highlight menu item đang được chọn (dùng Route::is())
- Header top: tên user + avatar + dropdown (Hồ sơ / Đăng xuất)
- Main content: @yield('content')
- Flash message component: hiện success/error dạng toast tự động ẩn sau 3 giây

=== VIEW: admin/giang_vien/index.blade.php ===
@extends('layouts.admin')

Nội dung:
1. Breadcrumb: Admin > Quản lý Giảng viên
2. Header row: tiêu đề "Danh sách Giảng viên" + nút "+ Thêm giảng viên" (link create)
3. Bộ lọc / tìm kiếm (form GET):
   - Input search: placeholder "Tìm theo tên, email, mã GV..."
   - Select filter: Tất cả | Đang dạy | Nghỉ phép | Đã nghỉ
   - Nút Tìm kiếm + Xóa bộ lọc
4. Bảng dữ liệu:
   Cột: # | Avatar | Mã GV | Họ tên | Email | Chuyên môn | Học vị | Trạng thái | Ngày vào làm | Thao tác
   - Avatar: hiển thị ảnh tròn 40px hoặc initials nếu không có ảnh
   - Trạng thái: badge màu (xanh=đang dạy, vàng=nghỉ phép, đỏ=đã nghỉ)
   - Thao tác: icon Xem | Sửa | Bật/Tắt | Reset mật khẩu | Xóa
   - Nút Xóa dùng confirm JS trước khi submit form DELETE
5. Pagination links
6. Hiển thị tổng số: "Tìm thấy X giảng viên"

=== VIEW: admin/giang_vien/create.blade.php ===
@extends('layouts.admin')

Form 2 cột (grid-cols-2), chia section rõ ràng:

Section 1 — Thông tin tài khoản:
- Họ tên (*), Email (*), Mật khẩu (*), Xác nhận mật khẩu (*), Số điện thoại
- Upload avatar: preview ảnh ngay khi chọn (JS FileReader)

Section 2 — Thông tin giảng viên:
- Mã giảng viên (*), Chuyên môn, Học vị (select: Cử nhân/Thạc sĩ/Tiến sĩ/Phó GS/GS)
- Ngày sinh (datepicker), Giới tính (radio), CMND, Địa chỉ (textarea)
- Ngày vào làm (datepicker)

Footer: nút "Lưu giảng viên" + nút "Hủy" (back)
Validation error: hiển thị đỏ dưới từng field

=== VIEW: admin/giang_vien/edit.blade.php ===
Tương tự create nhưng điền sẵn dữ liệu, password để trống (không bắt buộc)

=== VIEW: admin/giang_vien/show.blade.php ===
Trang chi tiết:
- Avatar lớn + thông tin đầy đủ dạng card
- Tab: Thông tin cá nhân | Lịch dạy | Lớp phụ trách
- Nút: Sửa | Reset mật khẩu | Bật/Tắt tài khoản

Làm tương tự đầy đủ cho học viên:
- admin/hoc_vien/index.blade.php  (bảng thêm cột: Mã HV, Trường TN, Ngày nhập học, Tình trạng học)
- admin/hoc_vien/create.blade.php
- admin/hoc_vien/edit.blade.php
- admin/hoc_vien/show.blade.php
```

---

# ══════════════════════════════════════
# MODULE B — QUẢN LÝ THỜI KHÓA BIỂU
# ══════════════════════════════════════

---

## BƯỚC B1 — Migration Thời Khóa Biểu

```
Tạo các migration sau:

=== Bảng: khoa_hocs (Khóa học) ===
Schema::create('khoa_hocs', function (Blueprint $table) {
    $table->id();
    $table->string('ma_khoa_hoc')->unique();          // KH001
    $table->string('ten_khoa_hoc');
    $table->text('mo_ta')->nullable();
    $table->integer('so_buoi')->default(0);           // Tổng số buổi
    $table->integer('so_tiet_moi_buoi')->default(2);
    $table->decimal('hoc_phi', 12, 0)->default(0);
    $table->enum('trang_thai', ['dang_mo','da_ket_thuc','tam_dung'])->default('dang_mo');
    $table->timestamps();
});

=== Bảng: lop_hocs (Lớp học) ===
Schema::create('lop_hocs', function (Blueprint $table) {
    $table->id();
    $table->string('ma_lop')->unique();               // L001
    $table->string('ten_lop');
    $table->foreignId('khoa_hoc_id')->constrained('khoa_hocs');
    $table->foreignId('giang_vien_id')->constrained('users'); // user có role giang_vien
    $table->integer('si_so_toi_da')->default(30);
    $table->date('ngay_bat_dau');
    $table->date('ngay_ket_thuc')->nullable();
    $table->enum('trang_thai', ['dang_hoc','sap_khai_giang','da_ket_thuc'])->default('sap_khai_giang');
    $table->string('phong_hoc')->nullable();
    $table->timestamps();
});

=== Bảng: lich_hocs (Thời khóa biểu) ===
Schema::create('lich_hocs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
    $table->date('ngay_hoc');
    $table->enum('thu_trong_tuan', ['2','3','4','5','6','7','CN']);
    $table->time('gio_bat_dau');
    $table->time('gio_ket_thuc');
    $table->string('phong_hoc')->nullable();
    $table->enum('trang_thai', ['da_len_lich','hoan_thanh','huy','doi_lich'])->default('da_len_lich');
    $table->text('ghi_chu')->nullable();
    $table->timestamps();
});

=== Bảng: yeu_cau_doi_lichs ===
Schema::create('yeu_cau_doi_lichs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lich_hoc_id')->constrained('lich_hocs');
    $table->foreignId('giang_vien_id')->constrained('users');
    $table->date('ngay_muon_doi');
    $table->time('gio_bat_dau_moi');
    $table->time('gio_ket_thuc_moi');
    $table->string('phong_hoc_moi')->nullable();
    $table->text('ly_do');
    $table->enum('trang_thai', ['cho_duyet','da_duyet','tu_choi'])->default('cho_duyet');
    $table->text('ghi_chu_admin')->nullable();
    $table->timestamps();
});

=== Bảng: hoc_vien_lop_hocs (pivot) ===
Schema::create('hoc_vien_lop_hocs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hoc_vien_id')->constrained('users');
    $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
    $table->date('ngay_tham_gia');
    $table->enum('trang_thai', ['dang_hoc','da_hoan_thanh','da_nghi'])->default('dang_hoc');
    $table->unique(['hoc_vien_id', 'lop_hoc_id']);
    $table->timestamps();
});

Chạy: php artisan migrate

Tạo Models:
- KhoaHoc.php  (hasMany LopHoc)
- LopHoc.php   (belongsTo KhoaHoc, belongsTo User as giangVien, hasMany LichHoc, belongsToMany User as hocViens)
- LichHoc.php  (belongsTo LopHoc, hasMany YeuCauDoiLich)
- YeuCauDoiLich.php (belongsTo LichHoc, belongsTo User as giangVien)
```

---

## BƯỚC B2 — Controller Thời Khóa Biểu

```
=== app/Http/Controllers/Admin/KhoaHocController.php ===
Resource controller đầy đủ (index, create, store, show, edit, update, destroy)
- index(): danh sách khóa học, filter theo trang_thai, paginate 10
- store()/update(): validate đầy đủ, unique ma_khoa_hoc
- destroy(): không xóa nếu đang có lớp học

=== app/Http/Controllers/Admin/LopHocController.php ===
Resource controller đầy đủ:
- index()  : danh sách lớp, filter theo khoa_hoc_id / giang_vien_id / trang_thai
- create() : load danh sách KhoaHoc (active) và GiangVien (active) để làm select option
- store()  : validate + tạo lớp
             Auto-generate lịch học: nếu admin chọn các thứ trong tuần + giờ học,
             tự động tạo records LichHoc từ ngay_bat_dau đến ngay_ket_thuc
- show()   : chi tiết lớp + danh sách học viên đã đăng ký + lịch học
- addHocVien($lopId) : thêm học viên vào lớp (kiểm tra si_so_toi_da)
- removeHocVien($lopId, $hocVienId) : xóa học viên khỏi lớp

=== app/Http/Controllers/Admin/LichHocController.php ===
- index()  : hiển thị lịch dạng BẢNG (table view) hoặc dạng LỊCH (calendar view)
             Có thể filter theo tuần/tháng, lớp, giảng viên, phòng học
- store()  : thêm buổi học thủ công cho 1 lớp
- update() : sửa thông tin buổi học (ngày, giờ, phòng)
- destroy(): hủy buổi học (đặt trang_thai = 'huy', KHÔNG xóa)

=== app/Http/Controllers/Admin/YeuCauDoiLichController.php ===
- index()  : danh sách yêu cầu đổi lịch, filter theo trang_thai (cho_duyet/da_duyet/tu_choi)
             Hiển thị badge số lượng yêu cầu đang chờ ở sidebar
- duyet($id)   : duyệt yêu cầu → cập nhật LichHoc gốc theo thông tin mới
                 → đặt trang_thai yêu cầu = 'da_duyet'
- tuChoi($id)  : từ chối + lưu ghi_chu_admin
                 → đặt trang_thai = 'tu_choi'
```

---

## BƯỚC B3 — Views Thời Khóa Biểu

```
=== VIEW: admin/lich_hoc/index.blade.php ===
@extends('layouts.admin')

Có 2 chế độ xem (toggle button):

--- CHẾ ĐỘ BẢNG (mặc định) ---
Bộ lọc: Tuần (date range) | Lớp (select) | Giảng viên (select) | Phòng học (input)
Bảng cột: Ngày | Thứ | Lớp | Giảng viên | Giờ | Phòng | Trạng thái | Thao tác
- Badge màu: xanh=đã lên lịch, xám=hoàn thành, đỏ=đã hủy, vàng=đổi lịch
- Thao tác: Sửa | Hủy

--- CHẾ ĐỘ LỊCH (calendar) ---
Dùng thư viện FullCalendar.io (CDN)
- Hiển thị các buổi học dạng event trên lịch tháng
- Click vào event → popup modal xem chi tiết
- Màu event theo trạng thái: xanh/đỏ/vàng
- Có nút chuyển tháng trước/sau

Code JS khởi tạo FullCalendar:
- Fetch dữ liệu từ route: /admin/lich-hoc/events?start=...&end=... (trả JSON)
- Format event: { title: 'Lớp L001 - GV Nguyễn A', start: '2025-01-15T08:00', end: '...' }

Thêm route: GET /admin/lich-hoc/events → LichHocController@getEvents (trả JSON)

=== VIEW: admin/lop_hoc/create.blade.php ===
Form tạo lớp học với tính năng auto-generate lịch:

Section 1 — Thông tin lớp:
- Mã lớp (*), Tên lớp (*), Khóa học (*) [select], Giảng viên (*) [select]
- Sĩ số tối đa, Ngày bắt đầu (*), Ngày kết thúc, Phòng học

Section 2 — Cấu hình lịch học tự động:
- Checkbox chọn thứ trong tuần: □Thứ 2  □Thứ 3  □Thứ 4  □Thứ 5  □Thứ 6  □Thứ 7  □CN
- Giờ bắt đầu (timepicker), Giờ kết thúc (timepicker)
- Nút "Xem trước lịch" → AJAX preview danh sách buổi học sẽ được tạo
- Nút "Tạo lớp & Lịch học"

JS: khi click "Xem trước lịch", gọi route /admin/lop-hoc/preview-schedule (POST)
Trả về HTML table danh sách ngày học để hiển thị trong modal

=== VIEW: admin/yeu_cau_doi_lich/index.blade.php ===
- Badge đếm số yêu cầu chờ duyệt ở tiêu đề
- Tab: Chờ duyệt | Đã duyệt | Đã từ chối
- Bảng: GV | Lớp | Buổi học gốc | Ngày/Giờ muốn đổi | Phòng mới | Lý do | Thao tác
- Thao tác "Chờ duyệt": nút Duyệt (xanh) + Từ chối (đỏ)
- Nút Từ chối: mở modal nhập ghi chú lý do từ chối
```

---

# ══════════════════════════════════════
# MODULE C — QUẢN LÝ ĐÁNH GIÁ CHẤT LƯỢNG
# ══════════════════════════════════════

---

## BƯỚC C1 — Migration Đánh Giá

```
Tạo các migration:

=== Bảng: tieu_chi_danh_gias (Tiêu chí đánh giá) ===
Schema::create('tieu_chi_danh_gias', function (Blueprint $table) {
    $table->id();
    $table->string('ten_tieu_chi');                   // VD: "Kiến thức chuyên môn"
    $table->enum('loai', ['giang_vien','khoa_hoc','hoc_vien']);
    $table->integer('trong_so')->default(1);          // Trọng số (1-5)
    $table->text('mo_ta')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

=== Bảng: danh_gia_hoc_viens (GV đánh giá HV) ===
Schema::create('danh_gia_hoc_viens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hoc_vien_id')->constrained('users');
    $table->foreignId('giang_vien_id')->constrained('users');
    $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
    $table->integer('ky_hoc');                        // 1, 2, ...
    $table->integer('nam_hoc');                       // 2024, 2025
    $table->json('chi_tiet_danh_gia');                // [{tieu_chi_id, diem}]
    $table->decimal('diem_trung_binh', 4, 2);
    $table->text('nhan_xet')->nullable();
    $table->enum('xep_loai', ['xuat_sac','gioi','kha','trung_binh','yeu']);
    $table->timestamps();
    $table->unique(['hoc_vien_id','lop_hoc_id','ky_hoc','nam_hoc']);
});

=== Bảng: danh_gia_khoa_hocs (HV đánh giá Khóa học) ===
Schema::create('danh_gia_khoa_hocs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hoc_vien_id')->constrained('users');
    $table->foreignId('khoa_hoc_id')->constrained('khoa_hocs');
    $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
    $table->json('chi_tiet_danh_gia');                // [{tieu_chi_id, diem}]
    $table->decimal('diem_trung_binh', 4, 2);
    $table->integer('diem_noi_dung')->default(0);     // 1-5 sao
    $table->integer('diem_giang_vien')->default(0);   // 1-5 sao
    $table->integer('diem_co_so_vat_chat')->default(0);
    $table->text('gop_y')->nullable();
    $table->boolean('an_danh')->default(true);
    $table->timestamps();
    $table->unique(['hoc_vien_id','lop_hoc_id']);
});

=== Bảng: danh_gia_giang_viens (Admin tổng hợp đánh giá GV) ===
Schema::create('danh_gia_giang_viens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('giang_vien_id')->constrained('users');
    $table->integer('ky_hoc');
    $table->integer('nam_hoc');
    $table->decimal('diem_tb_tu_hoc_vien', 4, 2)->default(0);  // Từ danh_gia_khoa_hocs
    $table->decimal('diem_chuyen_mon', 4, 2)->default(0);       // Admin nhập
    $table->decimal('diem_chuyen_can', 4, 2)->default(0);       // Tính từ điểm danh
    $table->decimal('diem_tong', 4, 2)->default(0);
    $table->text('nhan_xet_admin')->nullable();
    $table->enum('xep_loai', ['xuat_sac','gioi','kha','trung_binh','yeu'])->nullable();
    $table->timestamps();
    $table->unique(['giang_vien_id','ky_hoc','nam_hoc']);
});

Tạo Models tương ứng với đầy đủ relationships.
Chạy: php artisan migrate
```

---

## BƯỚC C2 — Controller Đánh Giá Chất Lượng

```
=== app/Http/Controllers/Admin/DanhGiaController.php ===

Method: index()
- Dashboard tổng quan đánh giá chất lượng
- Thống kê: Điểm TB khóa học theo tháng (chart), Top 5 GV tốt nhất, Tỷ lệ xếp loại HV
- Return view('admin.danh_gia.index', compact('thongKe'))

Method: tieuChi()
- Quản lý tiêu chí đánh giá (CRUD đơn giản)
- Phân loại: tiêu chí cho GV / cho KH / cho HV
- Return view('admin.danh_gia.tieu_chi')

Method: danhGiaGiangVien()
- Danh sách đánh giá GV theo kỳ/năm (filter)
- Tính điểm tự động từ các nguồn
- Return view('admin.danh_gia.giang_vien', compact('danhGias', 'kyHoc', 'namHoc'))

Method: taoOrCapNhatDanhGiaGV(Request $request, $giangVienId)
- Admin tạo/cập nhật đánh giá cho 1 GV trong kỳ
- Tự động tính diem_tb_tu_hoc_vien từ bảng danh_gia_khoa_hocs
- Tự động tính diem_chuyen_can từ tỷ lệ điểm danh của GV (số buổi dạy / số buổi đã lên lịch)
- Admin nhập thủ công: diem_chuyen_mon, nhan_xet_admin
- Tính diem_tong = trung bình có trọng số
- Tự động xếp loại: >=9=Xuất sắc, >=8=Giỏi, >=6.5=Khá, >=5=TB, <5=Yếu
- Return redirect()->back()->with('success', 'Đã lưu đánh giá!')

Method: danhGiaKhoaHoc()
- Xem tổng hợp đánh giá của học viên cho từng khóa học
- Group by khoa_hoc_id, tính trung bình các tiêu chí
- Hiển thị biểu đồ radar (spider chart) so sánh các tiêu chí
- Filter theo kỳ/năm, khóa học
- Return view('admin.danh_gia.khoa_hoc')

Method: danhGiaHocVien()
- Xem tổng hợp đánh giá học viên từ giảng viên
- Filter theo lớp / kỳ / năm / xếp loại
- Xuất Excel/PDF
- Return view('admin.danh_gia.hoc_vien')

Method: xuatBaoCao(Request $request)
- Export PDF/Excel báo cáo đánh giá chất lượng
- Dùng package: maatwebsite/excel hoặc barryvdh/laravel-dompdf
- Filter: loại báo cáo (GV/KH/HV), kỳ, năm
```

---

## BƯỚC C3 — Views Đánh Giá Chất Lượng

```
=== VIEW: admin/danh_gia/index.blade.php ===
@extends('layouts.admin')

Dashboard Đánh giá Chất lượng — 4 section:

SECTION 1 — Thẻ tổng quan (4 card):
- Điểm TB khóa học (ví dụ: 4.2/5.0 ★)
- Số đánh giá tháng này
- GV xuất sắc kỳ này
- Tỷ lệ HV xếp loại Khá trở lên (%)

SECTION 2 — Biểu đồ điểm TB khóa học theo tháng:
- Dùng Chart.js (line chart)
- CDN: https://cdn.jsdelivr.net/npm/chart.js
- Data fetch từ route /admin/danh-gia/chart-data (trả JSON)
- Trục X: tháng 1-12, Trục Y: điểm 0-5

SECTION 3 — Top 5 Giảng viên tốt nhất kỳ này:
- Bảng: Hạng | Avatar | Tên GV | Điểm TB từ HV | Điểm chuyên môn | Điểm tổng | Xếp loại badge

SECTION 4 — Phân bổ xếp loại Học viên (doughnut chart):
- Chart.js doughnut: Xuất sắc / Giỏi / Khá / TB / Yếu

=== VIEW: admin/danh_gia/giang_vien.blade.php ===

Bộ lọc: Kỳ học (1/2) | Năm học (select) | Giảng viên (search)
Bảng đánh giá GV:
  Cột: GV | Điểm từ HV | Điểm chuyên môn | Điểm chuyên cần | Điểm tổng | Xếp loại | Nhận xét | Thao tác
  - Nút "Đánh giá": mở modal form nhập điểm chuyên môn + nhận xét
  - Modal: hiển thị sẵn điểm TB từ HV và điểm chuyên cần (auto-calculated)
  - Badge xếp loại: màu theo mức (vàng=xuất sắc, xanh=giỏi, ...)

=== VIEW: admin/danh_gia/khoa_hoc.blade.php ===

Tab: Tổng quan | Chi tiết từng khóa

--- Tab Tổng quan ---
Grid card mỗi khóa học:
- Tên khóa | Điểm trung bình (★ star rating display) | Số lượt đánh giá | Trend (↑↓)
- Màu card theo điểm: xanh >= 4, vàng >= 3, đỏ < 3

--- Tab Chi tiết ---
Select khóa học → hiển thị:
- Radar chart (Chart.js) so sánh các tiêu chí: Nội dung / Giảng viên / Cơ sở vật chất / ...
- Danh sách góp ý ẩn danh từ học viên (chỉ hiện nội dung, không hiện tên)

=== VIEW: admin/danh_gia/hoc_vien.blade.php ===

Bộ lọc: Lớp (select) | Kỳ | Năm | Xếp loại
Bảng: HV | Lớp | GV đánh giá | Điểm TB | Xếp loại | Nhận xét | Ngày đánh giá
- Row click → modal xem chi tiết từng tiêu chí
Nút "Xuất Excel" và "Xuất PDF" ở góc phải

=== VIEW: admin/danh_gia/tieu_chi.blade.php ===
2 cột:
- Trái: Form thêm tiêu chí (tên, loại, trọng số, mô tả)
- Phải: Danh sách tiêu chí theo từng loại (GV / KH / HV) với toggle active/inactive
```

---

## BƯỚC C4 — Cài đặt Package Xuất File & Cấu hình

```
Cài thêm packages:
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

Publish config:
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"

Tạo Export class:
app/Exports/DanhGiaHocVienExport.php
- Implement FromQuery, WithHeadings, WithMapping
- Headings: STT | Mã HV | Họ tên | Lớp | Điểm TB | Xếp loại | Nhận xét | Ngày đánh giá
- Filter theo request params

Tạo Export class:
app/Exports/DanhGiaGiangVienExport.php
- Headings: STT | Mã GV | Họ tên | Điểm HV | Điểm CM | Điểm CC | Điểm Tổng | Xếp loại
```

---

## BƯỚC C5 — Routes Module Đánh Giá & Hoàn thiện Routes

```
Thêm vào routes/web.php trong group admin:

// Module B — Thời khóa biểu
Route::resource('khoa-hoc', Admin\KhoaHocController::class)->names('admin.khoa_hoc');
Route::resource('lop-hoc', Admin\LopHocController::class)->names('admin.lop_hoc');
Route::post('lop-hoc/preview-schedule', [Admin\LopHocController::class, 'previewSchedule'])->name('admin.lop_hoc.preview');
Route::post('lop-hoc/{id}/add-hoc-vien', [Admin\LopHocController::class, 'addHocVien'])->name('admin.lop_hoc.add_hv');
Route::delete('lop-hoc/{lopId}/hoc-vien/{hvId}', [Admin\LopHocController::class, 'removeHocVien'])->name('admin.lop_hoc.remove_hv');
Route::resource('lich-hoc', Admin\LichHocController::class)->names('admin.lich_hoc');
Route::get('lich-hoc-events', [Admin\LichHocController::class, 'getEvents'])->name('admin.lich_hoc.events');
Route::resource('yeu-cau-doi-lich', Admin\YeuCauDoiLichController::class)->only(['index'])->names('admin.yeu_cau');
Route::patch('yeu-cau-doi-lich/{id}/duyet', [Admin\YeuCauDoiLichController::class, 'duyet'])->name('admin.yeu_cau.duyet');
Route::patch('yeu-cau-doi-lich/{id}/tu-choi', [Admin\YeuCauDoiLichController::class, 'tuChoi'])->name('admin.yeu_cau.tu_choi');

// Module C — Đánh giá chất lượng
Route::prefix('danh-gia')->name('admin.danh_gia.')->group(function () {
    Route::get('/',                                    [Admin\DanhGiaController::class, 'index'])->name('index');
    Route::get('/tieu-chi',                            [Admin\DanhGiaController::class, 'tieuChi'])->name('tieu_chi');
    Route::post('/tieu-chi',                           [Admin\DanhGiaController::class, 'storeTieuChi'])->name('tieu_chi.store');
    Route::patch('/tieu-chi/{id}',                     [Admin\DanhGiaController::class, 'updateTieuChi'])->name('tieu_chi.update');
    Route::delete('/tieu-chi/{id}',                    [Admin\DanhGiaController::class, 'destroyTieuChi'])->name('tieu_chi.destroy');
    Route::get('/giang-vien',                          [Admin\DanhGiaController::class, 'danhGiaGiangVien'])->name('giang_vien');
    Route::post('/giang-vien/{id}',                    [Admin\DanhGiaController::class, 'taoOrCapNhatDanhGiaGV'])->name('giang_vien.save');
    Route::get('/khoa-hoc',                            [Admin\DanhGiaController::class, 'danhGiaKhoaHoc'])->name('khoa_hoc');
    Route::get('/hoc-vien',                            [Admin\DanhGiaController::class, 'danhGiaHocVien'])->name('hoc_vien');
    Route::get('/xuat-bao-cao',                        [Admin\DanhGiaController::class, 'xuatBaoCao'])->name('xuat_bao_cao');
    Route::get('/chart-data',                          [Admin\DanhGiaController::class, 'chartData'])->name('chart_data');
});
```

---

## BƯỚC C6 — Seeder dữ liệu mẫu cho Phase 2

```
Tạo file database/seeders/Phase2Seeder.php với dữ liệu mẫu:

1. Tạo 5 giảng viên mẫu (user + profile):
   GV001 - Nguyễn Văn An - Toán học - Thạc sĩ
   GV002 - Trần Thị Bình - Vật lý - Tiến sĩ
   GV003 - Lê Văn Cường - Tin học - Thạc sĩ
   GV004 - Phạm Thị Dung - Tiếng Anh - Cử nhân
   GV005 - Hoàng Văn Em - Hóa học - Tiến sĩ

2. Tạo 10 học viên mẫu (user + profile):
   HV001 đến HV010 với thông tin ngẫu nhiên hợp lý

3. Tạo 3 khóa học mẫu:
   KH001 - Toán nâng cao - 30 buổi
   KH002 - Lập trình Python cơ bản - 24 buổi
   KH003 - Tiếng Anh giao tiếp - 36 buổi

4. Tạo 2 lớp học mẫu + lịch học cho 4 tuần tới:
   L001 - Lớp Toán A1 - KH001 - GV001
   L002 - Lớp Python B1 - KH002 - GV003

5. Tạo tiêu chí đánh giá mẫu:
   Cho GV: Kiến thức CM, Phương pháp giảng dạy, Tinh thần trách nhiệm, Giao tiếp
   Cho KH: Nội dung phù hợp, Tài liệu học tập, Cơ sở vật chất, Thời lượng
   Cho HV: Thái độ học tập, Mức độ tiếp thu, Kết quả kiểm tra, Chuyên cần

Chạy: php artisan db:seed --class=Phase2Seeder
```

---

## BƯỚC C7 — Kiểm thử Phase 2

```
Đăng nhập với tài khoản admin@academy.com và kiểm tra tuần tự:

=== Kiểm tra Module A — Người dùng ===
1. Vào /admin/giang-vien → danh sách hiển thị đúng
2. Thêm GV mới với đầy đủ thông tin → thành công
3. Upload avatar → ảnh lưu đúng vào storage
4. Sửa thông tin GV → cập nhật đúng
5. Bật/Tắt tài khoản → is_active thay đổi
6. Reset mật khẩu → đăng nhập được bằng password123
7. Thử xóa GV chưa có lớp → thành công
8. Kiểm tra tương tự với Học viên

=== Kiểm tra Module B — Thời khóa biểu ===
1. Tạo khóa học mới → OK
2. Tạo lớp học với auto-generate lịch (Thứ 2, 4, 6 tuần → xem preview → tạo)
3. Xem lịch dạng bảng → đủ dữ liệu
4. Xem lịch dạng calendar (FullCalendar) → events hiển thị đúng màu
5. Sửa 1 buổi học (đổi phòng) → cập nhật OK
6. Hủy 1 buổi học → trang_thai = 'huy', hiển thị badge đỏ

=== Kiểm tra Module C — Đánh giá ===
1. Vào /admin/danh-gia → dashboard hiển thị chart
2. Thêm tiêu chí đánh giá mới → danh sách cập nhật
3. Tạo đánh giá GV cho kỳ 1 năm 2025 → tính điểm tổng đúng
4. Xem đánh giá khóa học → radar chart hiển thị
5. Xuất Excel danh sách đánh giá HV → file download được

Báo cáo kết quả từng mục: ✅ OK hoặc ❌ Lỗi [mô tả lỗi]
Nếu có lỗi, phân tích và sửa ngay trong cùng lượt response.
```

---

## 📁 CẤU TRÚC FILE SAU PHASE 2

```
app/
├── Http/Controllers/Admin/
│   ├── DashboardController.php
│   ├── GiangVienController.php      ← MỚI
│   ├── HocVienController.php        ← MỚI
│   ├── KhoaHocController.php        ← MỚI
│   ├── LopHocController.php         ← MỚI
│   ├── LichHocController.php        ← MỚI
│   ├── YeuCauDoiLichController.php  ← MỚI
│   └── DanhGiaController.php        ← MỚI
├── Models/
│   ├── User.php
│   ├── GiangVienProfile.php         ← MỚI
│   ├── HocVienProfile.php           ← MỚI
│   ├── KhoaHoc.php                  ← MỚI
│   ├── LopHoc.php                   ← MỚI
│   ├── LichHoc.php                  ← MỚI
│   ├── YeuCauDoiLich.php            ← MỚI
│   ├── TieuChiDanhGia.php           ← MỚI
│   ├── DanhGiaHocVien.php           ← MỚI
│   ├── DanhGiaKhoaHoc.php           ← MỚI
│   └── DanhGiaGiangVien.php         ← MỚI
└── Exports/
    ├── DanhGiaHocVienExport.php     ← MỚI
    └── DanhGiaGiangVienExport.php   ← MỚI
database/
├── migrations/
│   ├── xxxx_create_giang_vien_profiles_table.php
│   ├── xxxx_create_hoc_vien_profiles_table.php
│   ├── xxxx_create_khoa_hocs_table.php
│   ├── xxxx_create_lop_hocs_table.php
│   ├── xxxx_create_lich_hocs_table.php
│   ├── xxxx_create_yeu_cau_doi_lichs_table.php
│   ├── xxxx_create_hoc_vien_lop_hocs_table.php
│   ├── xxxx_create_tieu_chi_danh_gias_table.php
│   ├── xxxx_create_danh_gia_hoc_viens_table.php
│   ├── xxxx_create_danh_gia_khoa_hocs_table.php
│   └── xxxx_create_danh_gia_giang_viens_table.php
└── seeders/
    └── Phase2Seeder.php
resources/views/
├── layouts/
│   └── admin.blade.php              ← MỚI (layout chung)
├── admin/
│   ├── giang_vien/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── hoc_vien/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── khoa_hoc/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── lop_hoc/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── lich_hoc/
│   │   └── index.blade.php          (bảng + FullCalendar)
│   ├── yeu_cau_doi_lich/
│   │   └── index.blade.php
│   └── danh_gia/
│       ├── index.blade.php           (dashboard + charts)
│       ├── giang_vien.blade.php
│       ├── khoa_hoc.blade.php
│       ├── hoc_vien.blade.php
│       └── tieu_chi.blade.php
```

---

## ✅ TIÊU CHÍ HOÀN THÀNH PHASE 2

- [ ] CRUD Giảng viên + upload avatar hoạt động
- [ ] CRUD Học viên hoạt động
- [ ] Tạo khóa học / lớp học / lịch học auto-generate
- [ ] Lịch hiển thị dạng calendar (FullCalendar)
- [ ] Duyệt / từ chối yêu cầu đổi lịch
- [ ] Dashboard đánh giá với Chart.js
- [ ] Đánh giá GV (nhập + tính điểm tự động)
- [ ] Tổng hợp đánh giá khóa học (radar chart)
- [ ] Xuất Excel/PDF hoạt động
- [ ] Seeder Phase 2 chạy được

---

## 🔜 PHASE TIẾP THEO

- **Phase 3**: Chức năng Giảng viên (điểm danh, quản lý điểm, đổi lịch)
- **Phase 4**: Chức năng Học viên (xem lịch, xem điểm, đánh giá)
- **Phase 5**: Thông báo real-time + Báo cáo tổng hợp
