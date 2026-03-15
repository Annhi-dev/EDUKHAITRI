# PROMPT GEMINI AGENT — PHASE 3
## Giảng Viên: Lịch Dạy · Quản Lý Lớp · Điểm Danh · Quản Lý Điểm · Đánh Giá · Hồ Sơ
### Dự án: Hệ thống Quản lý Giảng viên – Học viện (Laravel)

---

## 🧠 CONTEXT (nhắc lại để Gemini hiểu)

```
- Phase 1 ✅: Auth, phân quyền 3 role, dashboard riêng từng vai trò
- Phase 2 ✅: Admin CRUD người dùng, thời khóa biểu, đánh giá chất lượng
- Bảng đã có: users, giang_vien_profiles, hoc_vien_profiles, khoa_hocs,
              lop_hocs, lich_hocs, yeu_cau_doi_lichs, hoc_vien_lop_hocs,
              tieu_chi_danh_gias, danh_gia_hoc_viens, danh_gia_khoa_hocs
- Route GV: prefix /giang-vien, middleware 'giang_vien', name 'gv.*'
- Stack: Laravel + Blade + Tailwind CSS + MySQL
- User đang login là giảng viên → lấy bằng Auth::user()
```

---

# ══════════════════════════════════════
# MODULE A — LỊCH DẠY & YÊU CẦU ĐỔI LỊCH
# ══════════════════════════════════════

---

## BƯỚC A1 — Controller Lịch Dạy

```
Tạo file: app/Http/Controllers/GiangVien/LichDayController.php

=== Method: index() ===
- Lấy Auth::user()->id làm $giangVienId
- Query LichHoc join LopHoc WHERE lop_hocs.giang_vien_id = $giangVienId
- Hỗ trợ filter:
    + theo tuần: $request->tuan (mặc định tuần hiện tại)
    + theo tháng: $request->thang / $request->nam
    + theo trang_thai: da_len_lich / hoan_thanh / huy
- Tính thêm:
    + $lichHomNay   = lịch hôm nay của GV (date = today)
    + $lichTuanNay  = lịch trong 7 ngày tới
    + $tongBuoiThang = tổng số buổi trong tháng hiện tại
- Return view('giang_vien.lich_day.index', compact('lichHocs','lichHomNay','lichTuanNay','tongBuoiThang','filters'))

=== Method: getEvents() ===
- Trả JSON cho FullCalendar AJAX
- Query lich_hocs của GV hiện tại, filter theo ?start= và ?end=
- Format trả về:
  [
    {
      "id": 1,
      "title": "Lớp L001 - Phòng A101",
      "start": "2025-06-10T08:00:00",
      "end":   "2025-06-10T10:00:00",
      "color": "#16a34a",           // xanh=da_len_lich, xám=hoan_thanh, đỏ=huy
      "extendedProps": {
        "lop": "Lớp Toán A1",
        "phong": "A101",
        "trang_thai": "da_len_lich",
        "so_hoc_vien": 15
      }
    }
  ]

=== Method: show($lichHocId) ===
- Lấy chi tiết 1 buổi học (kiểm tra buổi đó phải thuộc lớp của GV hiện tại)
- Load thêm: danh sách học viên của lớp (để điểm danh)
- Return view('giang_vien.lich_day.show', compact('lichHoc', 'hocViens'))
```

---

## BƯỚC A2 — Controller Yêu Cầu Đổi Lịch

```
Tạo file: app/Http/Controllers/GiangVien/YeuCauDoiLichController.php

=== Method: index() ===
- Lấy tất cả yêu cầu đổi lịch của GV hiện tại
- Eager load: lichHoc.lopHoc
- Filter theo trang_thai: cho_duyet / da_duyet / tu_choi
- Paginate 10
- Return view('giang_vien.yeu_cau_doi_lich.index', compact('yeuCaus'))

=== Method: create($lichHocId) ===
- Load thông tin buổi học gốc (kiểm tra quyền sở hữu)
- Kiểm tra: buổi học phải có trang_thai = 'da_len_lich' mới cho đổi
- Kiểm tra: chưa có yêu cầu đang chờ duyệt cho buổi này
- Return view('giang_vien.yeu_cau_doi_lich.create', compact('lichHoc'))

=== Method: store(Request $request) ===
Validate:
  lich_hoc_id    : required|exists:lich_hocs,id
  ngay_muon_doi  : required|date|after:today
  gio_bat_dau_moi: required|date_format:H:i
  gio_ket_thuc_moi: required|date_format:H:i|after:gio_bat_dau_moi
  phong_hoc_moi  : nullable|string|max:50
  ly_do          : required|string|min:10|max:500

Xử lý:
- Tạo YeuCauDoiLich với giang_vien_id = Auth::id(), trang_thai = 'cho_duyet'
- Cập nhật lich_hoc.trang_thai = 'doi_lich'
- Return redirect()->route('gv.yeu_cau.index')->with('success','Đã gửi yêu cầu đổi lịch. Chờ admin duyệt!')

=== Method: destroy($id) ===
- Chỉ hủy được khi trang_thai = 'cho_duyet'
- Đặt trang_thai = 'tu_choi' (tự hủy), khôi phục lich_hoc.trang_thai = 'da_len_lich'
- Return redirect()->back()->with('success','Đã hủy yêu cầu đổi lịch!')
```

---

## BƯỚC A3 — Views Lịch Dạy

```
=== Tạo layout chung cho Giảng viên ===
File: resources/views/layouts/giang_vien.blade.php

Cấu trúc:
- Sidebar trái 240px cố định, màu chủ đạo emerald/green
- Logo học viện + tên "Cổng Giảng viên"
- Menu items với Heroicons SVG:
    🏠 Dashboard          → route('gv.dashboard')
    📅 Lịch dạy           → route('gv.lich_day.index')
    🏫 Quản lý lớp        → route('gv.lop_hoc.index')
    ✅ Điểm danh          → route('gv.diem_danh.index')
    📝 Quản lý điểm       → route('gv.diem.index')
    ⭐ Đánh giá học viên  → route('gv.danh_gia.index')
    🔄 Yêu cầu đổi lịch  → route('gv.yeu_cau.index') + badge số đang chờ
    👤 Hồ sơ cá nhân     → route('profile.edit')
- Active state: dùng Route::is() để highlight
- Header top: Avatar + Tên GV + Chuyên môn + Dropdown đăng xuất
- Flash message toast (success/error) tự ẩn sau 3 giây
- @yield('content') | @yield('scripts')

=== VIEW: giang_vien/lich_day/index.blade.php ===
@extends('layouts.giang_vien')

PHẦN 1 — Banner tóm tắt (3 card ngang):
  Card 1: "Hôm nay" — hiển thị số buổi dạy hôm nay + giờ bắt đầu sớm nhất
  Card 2: "Tuần này" — tổng số buổi 7 ngày tới
  Card 3: "Tháng này" — tổng số buổi trong tháng

PHẦN 2 — Toggle view: [📋 Danh sách] [📅 Lịch tháng]

--- CHẾ ĐỘ DANH SÁCH ---
Bộ lọc: Tuần (prev/next arrow) | Lớp (select) | Trạng thái
Nhóm theo ngày (group by ngay_hoc):
  Mỗi nhóm ngày có header: "Thứ 3, 10/06/2025" (highlight nếu là hôm nay)
  Dưới mỗi ngày: card các buổi học:
    - Màu trái theo trạng thái (xanh/xám/đỏ/vàng)
    - Icon đồng hồ + Giờ bắt đầu - Giờ kết thúc
    - Tên lớp + Khóa học
    - Icon phòng + Phòng học
    - Icon người + Số học viên
    - Nút [Xem chi tiết] → gv.lich_day.show
    - Nút [Điểm danh] → gv.diem_danh.create?lich_hoc_id=X (chỉ hiện khi là hôm nay)
    - Nút [Đổi lịch] → gv.yeu_cau.create?lich_hoc_id=X (chỉ hiện khi trang_thai=da_len_lich)

--- CHẾ ĐỘ LỊCH THÁNG (FullCalendar) ---
- Import FullCalendar từ CDN: https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js
- Khởi tạo calendar với locale: 'vi' (tiếng Việt)
- Fetch events từ: /giang-vien/lich-day/events?start=...&end=...
- Click vào event → mở modal popup:
    + Thông tin buổi học: Lớp, Giờ, Phòng, Số HV, Trạng thái
    + Nút [Điểm danh] và [Đổi lịch] tùy trạng thái
- Hiển thị badge đếm số buổi trên từng ô ngày

=== VIEW: giang_vien/lich_day/show.blade.php ===
@extends('layouts.giang_vien')

Chi tiết buổi học:
- Header: tên lớp + khóa học + badge trạng thái
- Grid 2 cột thông tin: Ngày học | Thứ | Giờ | Phòng | Số HV
- Nút hành động: [Điểm danh ngay] (nếu hôm nay) | [Đổi lịch] | [Quay lại]

=== VIEW: giang_vien/yeu_cau_doi_lich/index.blade.php ===
Tab 3 cột: [Chờ duyệt (N)] [Đã duyệt] [Đã từ chối]
Mỗi tab là list card:
  Card: Icon lịch | Thông tin buổi gốc → Thông tin muốn đổi | Lý do | Ngày gửi
  Badge trạng thái + nút [Hủy yêu cầu] nếu đang chờ duyệt

=== VIEW: giang_vien/yeu_cau_doi_lich/create.blade.php ===
Hiển thị thông tin buổi học gốc (readonly):
  Lớp | Ngày học gốc | Giờ gốc | Phòng gốc

Form yêu cầu đổi:
  Ngày muốn đổi (datepicker, chỉ chọn từ ngày mai trở đi)
  Giờ bắt đầu mới (timepicker)
  Giờ kết thúc mới (timepicker)
  Phòng học mới (input, optional)
  Lý do đổi lịch (textarea, bắt buộc, min 10 ký tự - hiện counter)

Nút: [Gửi yêu cầu] | [Hủy]
```

---

# ══════════════════════════════════════
# MODULE B — QUẢN LÝ LỚP HỌC
# ══════════════════════════════════════

---

## BƯỚC B1 — Controller Quản Lý Lớp

```
Tạo file: app/Http/Controllers/GiangVien/LopHocController.php

=== Method: index() ===
- Lấy danh sách lớp mà GV hiện tại phụ trách
  LopHoc::where('giang_vien_id', Auth::id())->with(['khoaHoc','hocViens'])->paginate(10)
- Thêm thống kê mỗi lớp:
    + so_hoc_vien: count học viên đang học (trang_thai = dang_hoc)
    + so_buoi_da_day: count lich_hocs trang_thai = hoan_thanh
    + so_buoi_con_lai: count lich_hocs trang_thai = da_len_lich
    + ti_le_hoan_thanh: phần trăm buổi đã dạy
- Return view('giang_vien.lop_hoc.index', compact('lopHocs'))

=== Method: show($id) ===
- Kiểm tra lớp thuộc GV hiện tại (abort 403 nếu không)
- Load:
    $lopHoc với khoaHoc, giangVien
    $hocViens = danh sách học viên trong lớp kèm profile + điểm TB
    $lichHocs = lịch học sắp tới + đã qua (5 gần nhất mỗi loại)
    $diemDanhs = thống kê điểm danh theo từng học viên
    $thongKe = [so_hv_dang_hoc, ti_le_diem_danh, so_buoi_hoan_thanh]
- Return view('giang_vien.lop_hoc.show', compact(...))

=== Method: danhSachHocVien($lopId) ===
- Danh sách học viên của 1 lớp kèm:
    + Thông tin cá nhân (tên, mã HV, điện thoại)
    + Số buổi đã điểm danh / tổng số buổi đã dạy
    + Tỷ lệ chuyên cần (%)
    + Điểm trung bình (nếu đã có)
    + Trạng thái (đang học/đã nghỉ)
- Hỗ trợ search theo tên/mã HV
- Return view('giang_vien.lop_hoc.danh_sach_hv', compact('lopHoc','hocViens'))

=== Method: xuatDanhSach($lopId) ===
- Xuất Excel danh sách học viên của lớp
- Dùng maatwebsite/excel
- File name: "DS_HV_{ma_lop}_{date}.xlsx"
- Cột: STT | Mã HV | Họ tên | Ngày sinh | Điện thoại | Email | Chuyên cần% | Điểm TB
```

---

## BƯỚC B2 — Views Quản Lý Lớp

```
=== VIEW: giang_vien/lop_hoc/index.blade.php ===
@extends('layouts.giang_vien')

Header: "Lớp học của tôi" + badge tổng số lớp

Grid 2 cột, mỗi lớp là 1 card:
  - Header card: Tên lớp + badge trạng thái (xanh=đang học, vàng=sắp khai giảng)
  - Khóa học: tag pill
  - Progress bar: % buổi đã dạy / tổng (ví dụ: 8/24 buổi = 33%)
  - Thống kê nhỏ:
      👥 15 học viên   📅 8/24 buổi   ✅ 78% chuyên cần
  - Thời gian: Khai giảng 01/01/2025 - Kết thúc 30/06/2025
  - Nút: [Xem chi tiết] [Danh sách HV] [Điểm danh]

=== VIEW: giang_vien/lop_hoc/show.blade.php ===
@extends('layouts.giang_vien')

PHẦN 1 — Header thông tin lớp:
  Tên lớp | Khóa học | Phòng học | Badge trạng thái
  4 card thống kê: Học viên | Buổi đã dạy | Buổi còn lại | Tỷ lệ chuyên cần TB

PHẦN 2 — Tab navigation:
  [👥 Học viên] [📅 Lịch học] [📝 Điểm số] [✅ Điểm danh tổng hợp]

--- Tab Học viên ---
Bảng: Avatar | Mã HV | Họ tên | Chuyên cần | Điểm TB | Trạng thái | Thao tác
- Chuyên cần: progress bar nhỏ (xanh >=80%, vàng >=60%, đỏ <60%)
- Thao tác: [👁 Chi tiết] [📝 Nhập điểm]

--- Tab Lịch học ---
Bảng: STT | Ngày | Thứ | Giờ | Phòng | Trạng thái | Sĩ số điểm danh
- Badge màu theo trạng thái

--- Tab Điểm số ---
Bảng điểm tổng hợp:
  Cột: Mã HV | Họ tên | Điểm CC | Điểm GK | Điểm CK | Điểm TB | Xếp loại
  - Click vào hàng → mở inline edit
  - Nút [Lưu tất cả] cuối bảng

--- Tab Điểm danh tổng hợp ---
Bảng pivot: Hàng = học viên, Cột = từng buổi học
  Ô: ✅ Có mặt / ❌ Vắng / 📝 Có phép / ➖ Chưa điểm danh
  Cuối hàng: Tổng có mặt / Tổng vắng / %

=== VIEW: giang_vien/lop_hoc/danh_sach_hv.blade.php ===
@extends('layouts.giang_vien')

Breadcrumb: Lớp học > {tên lớp} > Danh sách học viên
Search input + Nút [Xuất Excel]

Bảng:
  STT | Avatar | Mã HV | Họ tên | Ngày sinh | Điện thoại | Chuyên cần | Điểm TB | Thao tác
  - Cột chuyên cần: hiện % + màu
  - Thao tác: [👁 Hồ sơ HV] [📝 Nhập điểm]
```

---

# ══════════════════════════════════════
# MODULE C — ĐIỂM DANH
# ══════════════════════════════════════

---

## BƯỚC C1 — Migration & Model Điểm Danh

```
Tạo migration:

=== Bảng: diem_danhs ===
Schema::create('diem_danhs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lich_hoc_id')->constrained('lich_hocs')->onDelete('cascade');
    $table->foreignId('hoc_vien_id')->constrained('users');
    $table->foreignId('giang_vien_id')->constrained('users');  // GV thực hiện điểm danh
    $table->enum('trang_thai', ['co_mat','vang_co_phep','vang_khong_phep','di_muon','ve_som'])
           ->default('co_mat');
    $table->time('gio_den')->nullable();          // Giờ học viên đến thực tế
    $table->text('ghi_chu')->nullable();
    $table->timestamp('thoi_gian_diem_danh')->nullable();
    $table->timestamps();
    $table->unique(['lich_hoc_id', 'hoc_vien_id']);
});

Tạo Model: app/Models/DiemDanh.php
  - belongsTo LichHoc
  - belongsTo User as hocVien (FK: hoc_vien_id)
  - belongsTo User as giangVien (FK: giang_vien_id)

Thêm vào LichHoc.php:
  public function diemDanhs() { return $this->hasMany(DiemDanh::class); }
  public function daDiemDanh() { return $this->diemDanhs()->exists(); }

Chạy: php artisan migrate
```

---

## BƯỚC C2 — Controller Điểm Danh

```
Tạo file: app/Http/Controllers/GiangVien/DiemDanhController.php

=== Method: index() ===
- Lịch sử điểm danh của GV hiện tại
- Lấy tất cả lich_hocs thuộc lớp của GV, group by lop_hoc
- Với mỗi buổi: đếm có_mặt / vắng / tổng HV
- Filter: theo lớp, theo tháng, theo trạng thái điểm danh (đã/chưa)
- Return view('giang_vien.diem_danh.index', compact('lichHocs', 'filters'))

=== Method: create(Request $request) ===
- $lichHocId = $request->lich_hoc_id
- Kiểm tra lich_hoc thuộc lớp của GV hiện tại
- Load danh sách học viên của lớp đó (hoc_vien_lop_hocs join users)
- Load điểm danh đã có (nếu đã điểm danh trước) để hiển thị lại
- Kiểm tra lich_hoc.ngay_hoc (chỉ điểm danh trong ngày ± 1 ngày)
- Return view('giang_vien.diem_danh.create', compact('lichHoc','hocViens','diemDanhCu'))

=== Method: store(Request $request) ===
Validate:
  lich_hoc_id : required|exists:lich_hocs,id
  diem_danhs  : required|array
  diem_danhs.*.hoc_vien_id : required|exists:users,id
  diem_danhs.*.trang_thai  : required|in:co_mat,vang_co_phep,vang_khong_phep,di_muon,ve_som
  diem_danhs.*.gio_den     : nullable|date_format:H:i
  diem_danhs.*.ghi_chu     : nullable|string|max:200

Xử lý (DB Transaction):
- Với mỗi học viên trong diem_danhs[]:
    DiemDanh::updateOrCreate(
        ['lich_hoc_id' => $lichHocId, 'hoc_vien_id' => $hvId],
        ['trang_thai' => ..., 'gio_den' => ..., 'ghi_chu' => ...,
         'giang_vien_id' => Auth::id(),
         'thoi_gian_diem_danh' => now()]
    )
- Cập nhật lich_hoc.trang_thai = 'hoan_thanh'
- Return redirect()->route('gv.diem_danh.index')->with('success','Điểm danh thành công! Đã lưu '.count($diemDanhs).' học viên.')

=== Method: show($lichHocId) ===
- Xem kết quả điểm danh của 1 buổi
- Load đầy đủ: lich_hoc, lop_hoc, danh sách diem_danh kèm hoc_vien
- Thống kê: Có mặt / Vắng có phép / Vắng KP / Đi muộn / Về sớm
- Return view('giang_vien.diem_danh.show', compact('lichHoc','diemDanhs','thongKe'))

=== Method: edit($lichHocId) ===
- Cho phép chỉnh sửa điểm danh đã làm (trong vòng 24h)
- Load lại dữ liệu cũ vào form
- Return view('giang_vien.diem_danh.create', compact('lichHoc','hocViens','diemDanhCu','isEdit'))

=== Method: thongKe() ===
- Thống kê điểm danh tổng hợp theo GV
- Filter: theo lớp, theo tháng/kỳ
- Trả về:
    + Bảng: mỗi hàng = 1 HV, mỗi cột = 1 buổi
    + Chart: tỷ lệ chuyên cần từng HV
- Return view('giang_vien.diem_danh.thong_ke', compact(...))
```

---

## BƯỚC C3 — Views Điểm Danh

```
=== VIEW: giang_vien/diem_danh/index.blade.php ===
@extends('layouts.giang_vien')

Header + bộ lọc: Lớp (select) | Tháng | Trạng thái điểm danh

List buổi học, mỗi item:
  - Ngày | Thứ | Lớp | Giờ | Phòng
  - Badge: "Đã điểm danh (15/15)" hoặc "Chưa điểm danh" (màu đỏ nhấp nháy)
  - Nút: [Điểm danh] (nếu chưa) hoặc [Xem] [Sửa] (nếu đã điểm danh)
  - Với buổi chưa điểm danh của ngày hôm nay: hiển thị nổi bật với viền xanh nhấp nháy

=== VIEW: giang_vien/diem_danh/create.blade.php ===
@extends('layouts.giang_vien')

HEADER:
  Thông tin buổi học: Lớp | Ngày | Giờ | Phòng | Tổng học viên

CÔNG CỤ NHANH (action bar):
  [✅ Tất cả có mặt] — JS: set tất cả select = co_mat
  [🔄 Reset] — JS: reset về mặc định
  Filter: Tìm theo tên HV (JS filter realtime)

BẢNG ĐIỂM DANH:
  Mỗi hàng = 1 học viên:
  | # | Avatar | Mã HV | Họ tên | Trạng thái | Giờ đến | Ghi chú |

  Cột Trạng thái: Radio button group hoặc Select đẹp với màu:
    🟢 Có mặt (mặc định, màu xanh)
    🟡 Vắng có phép (màu vàng)
    🔴 Vắng không phép (màu đỏ)
    🟠 Đi muộn (màu cam)
    🔵 Về sớm (màu xanh nhạt)

  Cột Giờ đến: chỉ hiện input khi chọn "Đi muộn"
  Cột Ghi chú: input text nhỏ

  JS UX:
    - Khi chọn "Vắng không phép" → highlight đỏ cả row
    - Khi chọn "Có mặt" → highlight xanh nhạt
    - Đếm realtime: "Có mặt: 13 | Vắng: 2 | Đi muộn: 0" (sticky bottom)

FOOTER STICKY:
  Thanh tổng kết dính dưới cùng:
  [Có mặt: 13] [Vắng CP: 1] [Vắng KP: 1] [Đi muộn: 0]   [💾 Lưu điểm danh]

=== VIEW: giang_vien/diem_danh/show.blade.php ===
@extends('layouts.giang_vien')

PHẦN 1 — Tóm tắt (5 card):
  Tổng HV | Có mặt | Vắng CP | Vắng KP | Đi muộn

PHẦN 2 — Bảng kết quả:
  STT | Avatar | Mã HV | Họ tên | Trạng thái (badge) | Giờ đến | Ghi chú | TG điểm danh

PHẦN 3 — Biểu đồ tròn (Chart.js Doughnut): Tỷ lệ các trạng thái

Nút hành động: [✏️ Chỉnh sửa] (nếu trong 24h) | [⬅ Quay lại]

=== VIEW: giang_vien/diem_danh/thong_ke.blade.php ===
@extends('layouts.giang_vien')

Bộ lọc: Lớp | Kỳ học | Tháng

PHẦN 1 — Bảng pivot điểm danh:
  Hàng = học viên, Cột = từng buổi học (hiện ngày tháng)
  Ô: ✅ / ❌ / 📝 / ⚠️ / ➖
  Cuối hàng: Tổng có mặt | Tổng buổi | Tỷ lệ % (màu theo mức)
  Cuối bảng: Nút [Xuất Excel bảng này]

PHẦN 2 — Chart.js Bar chart: Tỷ lệ chuyên cần từng học viên
  Trục X: tên HV, Trục Y: %, đường ngang 80% làm mốc ngưỡng
  Cột <60%: đỏ, 60-80%: vàng, >80%: xanh
```

---

# ══════════════════════════════════════
# MODULE D — QUẢN LÝ ĐIỂM
# ══════════════════════════════════════

---

## BƯỚC D1 — Migration Quản Lý Điểm

```
Tạo migration:

=== Bảng: bang_diems ===
Schema::create('bang_diems', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hoc_vien_id')->constrained('users');
    $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
    $table->foreignId('giang_vien_id')->constrained('users');
    $table->decimal('diem_chuyen_can', 4, 2)->nullable();    // 10% - tính từ điểm danh
    $table->decimal('diem_kiem_tra_1', 4, 2)->nullable();    // 15%
    $table->decimal('diem_kiem_tra_2', 4, 2)->nullable();    // 15%
    $table->decimal('diem_giua_ky',    4, 2)->nullable();    // 20%
    $table->decimal('diem_cuoi_ky',    4, 2)->nullable();    // 40%
    $table->decimal('diem_trung_binh', 4, 2)->nullable();    // tự tính
    $table->enum('xep_loai', ['xuat_sac','gioi','kha','trung_binh','yeu','chua_xep_loai'])
           ->default('chua_xep_loai');
    $table->boolean('da_khoa')->default(false);              // Khóa điểm sau khi nộp
    $table->text('ghi_chu')->nullable();
    $table->timestamps();
    $table->unique(['hoc_vien_id', 'lop_hoc_id']);
});

Tạo Model: app/Models/BangDiem.php
  - belongsTo User as hocVien (FK: hoc_vien_id)
  - belongsTo LopHoc
  - belongsTo User as giangVien (FK: giang_vien_id)

Thêm accessor tính điểm tự động:
  public function getDiemTrungBinhAttribute() {
    // Công thức: CC*10% + KT1*15% + KT2*15% + GK*20% + CK*40%
    ...
  }

Thêm accessor xếp loại tự động:
  >= 9.0 → Xuất sắc | >= 8.0 → Giỏi | >= 6.5 → Khá | >= 5.0 → TB | < 5.0 → Yếu

Chạy: php artisan migrate
```

---

## BƯỚC D2 — Controller Quản Lý Điểm

```
Tạo file: app/Http/Controllers/GiangVien/DiemController.php

=== Method: index() ===
- Danh sách lớp của GV + trạng thái nhập điểm
- Với mỗi lớp: đếm số HV đã có điểm TB / tổng HV
- Return view('giang_vien.diem.index', compact('lopHocs'))

=== Method: bangDiem($lopId) ===
- Kiểm tra lớp thuộc GV hiện tại
- Load danh sách HV của lớp với điểm đã nhập (nếu có)
- Nếu HV chưa có bảng điểm → khởi tạo rỗng
- Tính sẵn diem_chuyen_can từ tỷ lệ điểm danh (số buổi có mặt / tổng buổi * 10)
- Return view('giang_vien.diem.bang_diem', compact('lopHoc','bangDiems'))

=== Method: nhapDiem(Request $request, $lopId) ===
Validate:
  diem_data : required|array
  diem_data.*.hoc_vien_id   : required|exists:users,id
  diem_data.*.diem_kiem_tra_1: nullable|numeric|min:0|max:10
  diem_data.*.diem_kiem_tra_2: nullable|numeric|min:0|max:10
  diem_data.*.diem_giua_ky  : nullable|numeric|min:0|max:10
  diem_data.*.diem_cuoi_ky  : nullable|numeric|min:0|max:10
  diem_data.*.ghi_chu       : nullable|string|max:200

Xử lý (DB Transaction):
- Kiểm tra lớp chưa bị khóa điểm
- Với mỗi HV:
    + Tính diem_chuyen_can tự động từ điểm danh
    + Tính diem_trung_binh theo công thức trọng số
    + Tự động xếp loại
    + BangDiem::updateOrCreate(...)
- Return redirect()->back()->with('success','Đã lưu bảng điểm!')

=== Method: khoaDiem($lopId) ===
- Khóa toàn bộ bảng điểm của lớp (da_khoa = true)
- Chỉ khóa được khi TẤT CẢ HV đã có điểm cuối kỳ
- Nếu chưa đủ: return back với error 'Còn X học viên chưa có điểm cuối kỳ!'
- Return redirect()->back()->with('success','Đã khóa bảng điểm lớp '.$lopHoc->ten_lop)

=== Method: moKhoaDiem($lopId) ===
- Mở khóa điểm (chỉ trong 7 ngày sau khi khóa)
- Return redirect()->back()->with('success','Đã mở khóa bảng điểm!')

=== Method: xuatBangDiem($lopId) ===
- Xuất Excel bảng điểm của lớp
- File: "BangDiem_{ma_lop}_{date}.xlsx"
- Cột: STT | Mã HV | Họ tên | CC | KT1 | KT2 | GK | CK | Điểm TB | Xếp loại

=== Method: nhapDiemAjax(Request $request) ===
- Nhận: hoc_vien_id, lop_hoc_id, loai_diem, gia_tri
- Validate loai_diem in: diem_kiem_tra_1/2, diem_giua_ky, diem_cuoi_ky
- Cập nhật 1 ô điểm, tính lại diem_trung_binh + xep_loai
- Return JSON: { diem_trung_binh, xep_loai, color }
- (Dùng cho inline edit realtime không cần submit form)
```

---

## BƯỚC D3 — Views Quản Lý Điểm

```
=== VIEW: giang_vien/diem/index.blade.php ===
@extends('layouts.giang_vien')

Grid lớp học của GV — mỗi card:
  - Tên lớp + Khóa học
  - Progress nhập điểm: "Đã nhập: 12/15 học viên" + progress bar
  - Badge: "Đang nhập" / "Đã khóa điểm" (lock icon)
  - Nút [📝 Nhập điểm] hoặc [🔒 Xem điểm] tùy trạng thái

=== VIEW: giang_vien/diem/bang_diem.blade.php ===
@extends('layouts.giang_vien')

HEADER:
  Tên lớp | Khóa học | Số HV | Badge trạng thái (Đang nhập / Đã khóa)
  Nút: [📊 Xuất Excel] [🔒 Khóa điểm] (hoặc [🔓 Mở khóa])

BẢNG ĐIỂM INLINE EDIT:
  Cột: # | Mã HV | Họ tên | CC (auto) | KT1 | KT2 | GK | CK | Điểm TB | Xếp loại | Ghi chú

  - Cột CC: hiện số % chuyên cần, không cho sửa (auto từ điểm danh), có tooltip
  - Cột KT1, KT2, GK, CK: input number (0-10, step 0.1)
      + Nếu lớp đã khóa → disabled, chỉ hiển thị
      + Thay đổi → AJAX gọi nhapDiemAjax → cập nhật cột Điểm TB + Xếp loại realtime
  - Cột Điểm TB: tự cập nhật khi nhập, màu theo xếp loại
  - Cột Xếp loại: badge màu (vàng=Xuất sắc, xanh=Giỏi, xanh nhạt=Khá, xám=TB, đỏ=Yếu)
  - Cột Ghi chú: input text ngắn

  FOOTER ROW (dòng tổng):
    TB cả lớp | | | -- | -- | -- | -- | [TB] | [Phân bố xếp loại]

NÚT DƯỚI BẢNG:
  [💾 Lưu tất cả] (submit form toàn bảng — backup khi AJAX fail)
  [🔄 Tính lại điểm CC từ điểm danh]

CHART NHỎ BÊN PHẢI (hoặc dưới):
  Doughnut chart phân bố xếp loại: Xuất sắc/Giỏi/Khá/TB/Yếu
  Cập nhật realtime khi nhập điểm
```

---

# ══════════════════════════════════════
# MODULE E — ĐÁNH GIÁ HỌC VIÊN
# ══════════════════════════════════════

---

## BƯỚC E1 — Controller Đánh Giá Học Viên

```
Tạo file: app/Http/Controllers/GiangVien/DanhGiaHocVienController.php

=== Method: index() ===
- Danh sách đánh giá đã thực hiện của GV hiện tại
- Group by lop_hoc, filter theo kỳ/năm
- Với mỗi lớp: đếm đã đánh giá / tổng HV
- Return view('giang_vien.danh_gia.index', compact('lopHocs','danhGias'))

=== Method: create($lopId) ===
- Load lớp + danh sách HV chưa được đánh giá trong kỳ hiện tại
- Load tiêu chí đánh giá (loai = 'hoc_vien') đang active
- Return view('giang_vien.danh_gia.create', compact('lopHoc','hocViens','tieuChis'))

=== Method: store(Request $request) ===
Validate:
  lop_hoc_id   : required|exists:lop_hocs,id
  ky_hoc       : required|integer|in:1,2
  nam_hoc      : required|integer|min:2020
  danh_gias    : required|array
  danh_gias.*.hoc_vien_id        : required
  danh_gias.*.chi_tiet_danh_gia  : required|array (mảng [{tieu_chi_id, diem}])
  danh_gias.*.nhan_xet           : nullable|string|max:500

Xử lý:
- Với mỗi HV:
    + Tính diem_trung_binh từ chi_tiet_danh_gia (có trọng số)
    + Xếp loại tự động
    + DanhGiaHocVien::updateOrCreate(...)
- Return redirect()->route('gv.danh_gia.index')->with('success','Đã lưu đánh giá!')

=== Method: show($danhGiaId) ===
- Xem chi tiết 1 lượt đánh giá
- Return view('giang_vien.danh_gia.show', compact('danhGia','tieuChis'))

=== Method: edit($danhGiaId) ===
- Chỉ sửa được trong 7 ngày sau khi tạo
- Return view('giang_vien.danh_gia.edit', compact('danhGia','tieuChis'))
```

---

## BƯỚC E2 — Views Đánh Giá Học Viên

```
=== VIEW: giang_vien/danh_gia/index.blade.php ===
@extends('layouts.giang_vien')

Bộ lọc: Kỳ học (1/2) | Năm học

List card theo lớp:
  - Tên lớp + Số HV đã đánh giá / tổng
  - Progress bar
  - Nút [+ Đánh giá] (nếu còn HV chưa đánh giá) hoặc [✅ Hoàn thành]

=== VIEW: giang_vien/danh_gia/create.blade.php ===
@extends('layouts.giang_vien')

Chọn kỳ học + Năm học (ở header)

Với mỗi học viên — accordion panel:
  Header panel: Avatar | Tên HV | Mã HV | [badge: Chưa đánh giá]
  Body panel (mở khi click):
    Bảng tiêu chí:
      | Tiêu chí | Trọng số | Điểm (1-10) | Thanh điểm |
      Hàng tiêu chí: input range slider (1-10) + hiện số điểm
    Textarea nhận xét: placeholder "Nhận xét về học viên này..."
    Preview xếp loại realtime (tự tính khi kéo slider)

Footer: [💾 Lưu tất cả đánh giá]

JS: tính điểm TB realtime khi kéo slider, hiển thị xếp loại tức thì
```

---

# ══════════════════════════════════════
# MODULE F — HỒ SƠ CÁ NHÂN GIẢNG VIÊN
# ══════════════════════════════════════

---

## BƯỚC F1 — Controller & View Hồ Sơ

```
Mở rộng ProfileController đã có từ Phase 1:

=== Method: showGiangVien() ===
- Load user + giangVienProfile
- Thống kê: số lớp đang dạy, số buổi đã dạy, số học viên phụ trách
- Return view('giang_vien.profile.show', compact('user','profile','thongKe'))

=== Method: updateGiangVien(Request $request) ===
Validate + cập nhật:
  User: name, phone, avatar
  GiangVienProfile: chuyen_mon, hoc_vi, ngay_sinh, gioi_tinh, dia_chi

=== VIEW: giang_vien/profile/show.blade.php ===
@extends('layouts.giang_vien')

2 cột:
  Trái (1/3): Avatar lớn + upload + thống kê cá nhân
    - Upload avatar: click ảnh → input file → preview realtime
    - Thống kê: [Số lớp] [Số HV] [Số buổi đã dạy]
  Phải (2/3): Tab [Thông tin cá nhân] [Đổi mật khẩu]

  --- Tab Thông tin ---
  Form 2 cột:
    Họ tên | Email (readonly)
    Số điện thoại | Mã giảng viên (readonly)
    Học vị | Chuyên môn
    Ngày sinh | Giới tính
    Địa chỉ (full width)
  Nút [💾 Lưu thay đổi]

  --- Tab Đổi mật khẩu ---
  Mật khẩu hiện tại | Mật khẩu mới | Xác nhận
  Strength indicator (JS) cho mật khẩu mới
  Nút [🔑 Đổi mật khẩu]
```

---

## BƯỚC F2 — Routes Giảng Viên (Tổng hợp)

```
Thêm vào routes/web.php trong group middleware('giang_vien')->prefix('giang-vien')->name('gv.'):

// Module A — Lịch dạy
Route::get('lich-day',           [GiangVien\LichDayController::class, 'index'])->name('lich_day.index');
Route::get('lich-day/events',    [GiangVien\LichDayController::class, 'getEvents'])->name('lich_day.events');
Route::get('lich-day/{id}',      [GiangVien\LichDayController::class, 'show'])->name('lich_day.show');

// Module A — Yêu cầu đổi lịch
Route::get('yeu-cau-doi-lich',              [GiangVien\YeuCauDoiLichController::class, 'index'])->name('yeu_cau.index');
Route::get('yeu-cau-doi-lich/create',       [GiangVien\YeuCauDoiLichController::class, 'create'])->name('yeu_cau.create');
Route::post('yeu-cau-doi-lich',             [GiangVien\YeuCauDoiLichController::class, 'store'])->name('yeu_cau.store');
Route::delete('yeu-cau-doi-lich/{id}',      [GiangVien\YeuCauDoiLichController::class, 'destroy'])->name('yeu_cau.destroy');

// Module B — Quản lý lớp
Route::get('lop-hoc',                       [GiangVien\LopHocController::class, 'index'])->name('lop_hoc.index');
Route::get('lop-hoc/{id}',                  [GiangVien\LopHocController::class, 'show'])->name('lop_hoc.show');
Route::get('lop-hoc/{id}/hoc-vien',         [GiangVien\LopHocController::class, 'danhSachHocVien'])->name('lop_hoc.hoc_vien');
Route::get('lop-hoc/{id}/xuat-danh-sach',   [GiangVien\LopHocController::class, 'xuatDanhSach'])->name('lop_hoc.xuat');

// Module C — Điểm danh
Route::get('diem-danh',                     [GiangVien\DiemDanhController::class, 'index'])->name('diem_danh.index');
Route::get('diem-danh/create',              [GiangVien\DiemDanhController::class, 'create'])->name('diem_danh.create');
Route::post('diem-danh',                    [GiangVien\DiemDanhController::class, 'store'])->name('diem_danh.store');
Route::get('diem-danh/{lichHocId}',         [GiangVien\DiemDanhController::class, 'show'])->name('diem_danh.show');
Route::get('diem-danh/{lichHocId}/edit',    [GiangVien\DiemDanhController::class, 'edit'])->name('diem_danh.edit');
Route::put('diem-danh/{lichHocId}',         [GiangVien\DiemDanhController::class, 'store'])->name('diem_danh.update');
Route::get('diem-danh/thong-ke',            [GiangVien\DiemDanhController::class, 'thongKe'])->name('diem_danh.thong_ke');

// Module D — Quản lý điểm
Route::get('diem',                          [GiangVien\DiemController::class, 'index'])->name('diem.index');
Route::get('diem/{lopId}/bang-diem',        [GiangVien\DiemController::class, 'bangDiem'])->name('diem.bang_diem');
Route::post('diem/{lopId}/nhap',            [GiangVien\DiemController::class, 'nhapDiem'])->name('diem.nhap');
Route::post('diem/ajax',                    [GiangVien\DiemController::class, 'nhapDiemAjax'])->name('diem.ajax');
Route::patch('diem/{lopId}/khoa',           [GiangVien\DiemController::class, 'khoaDiem'])->name('diem.khoa');
Route::patch('diem/{lopId}/mo-khoa',        [GiangVien\DiemController::class, 'moKhoaDiem'])->name('diem.mo_khoa');
Route::get('diem/{lopId}/xuat',             [GiangVien\DiemController::class, 'xuatBangDiem'])->name('diem.xuat');

// Module E — Đánh giá học viên
Route::get('danh-gia',                      [GiangVien\DanhGiaHocVienController::class, 'index'])->name('danh_gia.index');
Route::get('danh-gia/{lopId}/create',       [GiangVien\DanhGiaHocVienController::class, 'create'])->name('danh_gia.create');
Route::post('danh-gia',                     [GiangVien\DanhGiaHocVienController::class, 'store'])->name('danh_gia.store');
Route::get('danh-gia/{id}',                 [GiangVien\DanhGiaHocVienController::class, 'show'])->name('danh_gia.show');
Route::get('danh-gia/{id}/edit',            [GiangVien\DanhGiaHocVienController::class, 'edit'])->name('danh_gia.edit');
Route::put('danh-gia/{id}',                 [GiangVien\DanhGiaHocVienController::class, 'update'])->name('danh_gia.update');

// Module F — Hồ sơ cá nhân
Route::get('ho-so',                         [ProfileController::class, 'showGiangVien'])->name('profile.show');
Route::patch('ho-so',                       [ProfileController::class, 'updateGiangVien'])->name('profile.update_gv');
Route::patch('ho-so/mat-khau',              [ProfileController::class, 'updatePassword'])->name('profile.password');
```

---

## BƯỚC F3 — Cập nhật Dashboard Giảng Viên

```
Mở rộng app/Http/Controllers/GiangVien/DashboardController.php:

Method index():
  $giangVienId = Auth::id();
  $today = today();

  // Lịch hôm nay
  $lichHomNay = LichHoc::whereHas('lopHoc', fn($q) => $q->where('giang_vien_id', $giangVienId))
                ->whereDate('ngay_hoc', $today)->orderBy('gio_bat_dau')->get();

  // Thống kê tổng quan
  $tongLop     = LopHoc::where('giang_vien_id', $giangVienId)->where('trang_thai','dang_hoc')->count();
  $tongHocVien = HocVienLopHoc::whereIn('lop_hoc_id', $lopIds)->where('trang_thai','dang_hoc')->count();
  $buoiChuaDiemDanh = LichHoc::whereIn('lop_hoc_id', $lopIds)
                      ->where('trang_thai','da_len_lich')->where('ngay_hoc','<=',$today)->count();
  $yeuCauChodDuyet = YeuCauDoiLich::where('giang_vien_id', $giangVienId)->where('trang_thai','cho_duyet')->count();

  // Lịch tuần này
  $lichTuanNay = LichHoc::whereIn('lop_hoc_id', $lopIds)
                 ->whereBetween('ngay_hoc', [now()->startOfWeek(), now()->endOfWeek()])
                 ->orderBy('ngay_hoc')->orderBy('gio_bat_dau')->get();

Cập nhật view giang_vien/dashboard.blade.php:

4 card thống kê:
  📚 Lớp đang dạy: {$tongLop}
  👥 Tổng học viên: {$tongHocVien}
  ⚠️ Buổi chưa điểm danh: {$buoiChuaDiemDanh} (màu đỏ nếu > 0)
  🔄 Yêu cầu đổi lịch: {$yeuCauChoduyet} đang chờ

Lịch hôm nay (timeline):
  Nếu không có lịch: "Hôm nay bạn không có buổi dạy nào 🎉"
  Nếu có: timeline các buổi dạy trong ngày (giờ | lớp | phòng | [Điểm danh])

Lịch tuần (mini calendar strip):
  7 ô ngày, mỗi ô hiển thị số buổi dạy, click → đến lịch dạy
```

---

## BƯỚC F4 — Kiểm thử Phase 3

```
Đăng nhập với tài khoản gv@academy.com và kiểm tra từng module:

=== Module A — Lịch dạy ===
1. Vào /giang-vien/lich-day → danh sách buổi dạy hiển thị đúng
2. Chuyển sang chế độ Calendar → FullCalendar load events
3. Click vào buổi hôm nay → hiện nút [Điểm danh]
4. Gửi yêu cầu đổi lịch → trang_thai = cho_duyet
5. Hủy yêu cầu đang chờ → trang_thai = tu_choi, lich_hoc khôi phục

=== Module B — Quản lý lớp ===
1. Vào /giang-vien/lop-hoc → hiện danh sách lớp của GV này
2. Xem chi tiết lớp → 4 tab hiển thị đúng
3. Tab điểm danh tổng hợp → bảng pivot đúng dữ liệu
4. Xuất Excel danh sách HV → download file thành công

=== Module C — Điểm danh ===
1. Click [Điểm danh] từ lịch dạy → form điểm danh load đúng HV
2. Nhấn [Tất cả có mặt] → tất cả chuyển sang co_mat
3. Đổi 1 HV sang vắng không phép → row highlight đỏ
4. Counter sticky bottom cập nhật realtime
5. Lưu điểm danh → redirect thành công, lich_hoc.trang_thai = hoan_thanh
6. Vào /giang-vien/diem-danh/thong-ke → bảng pivot + chart hiển thị

=== Module D — Quản lý điểm ===
1. Vào /giang-vien/diem → danh sách lớp + progress nhập điểm
2. Vào bảng điểm lớp → các input có thể nhập
3. Nhập điểm CK → AJAX cập nhật Điểm TB + Xếp loại realtime
4. Doughnut chart phân bố xếp loại cập nhật
5. Lưu tất cả → success message
6. Khóa điểm → không còn edit được
7. Xuất Excel → file đúng định dạng

=== Module E — Đánh giá HV ===
1. Vào /giang-vien/danh-gia → danh sách lớp
2. Tạo đánh giá → slider tiêu chí hoạt động
3. Preview xếp loại realtime khi kéo slider
4. Lưu → redirect thành công

=== Module F — Hồ sơ ===
1. Vào /giang-vien/ho-so → hiển thị đầy đủ thông tin
2. Upload avatar → preview realtime, lưu thành công
3. Đổi mật khẩu → validation + success

Báo cáo: ✅ OK hoặc ❌ Lỗi [mô tả]. Sửa lỗi ngay nếu có.
```

---

## 📁 CẤU TRÚC FILE SAU PHASE 3

```
app/Http/Controllers/GiangVien/
├── DashboardController.php      (cập nhật thống kê)
├── LichDayController.php        ← MỚI
├── YeuCauDoiLichController.php  ← MỚI
├── LopHocController.php         ← MỚI
├── DiemDanhController.php       ← MỚI
├── DiemController.php           ← MỚI
└── DanhGiaHocVienController.php ← MỚI

app/Models/
├── DiemDanh.php                 ← MỚI
└── BangDiem.php                 ← MỚI

app/Exports/
├── DanhSachHocVienExport.php    ← MỚI
└── BangDiemExport.php           ← MỚI

database/migrations/
├── xxxx_create_diem_danhs_table.php
└── xxxx_create_bang_diems_table.php

resources/views/
├── layouts/
│   └── giang_vien.blade.php    ← MỚI
└── giang_vien/
    ├── dashboard.blade.php      (cập nhật)
    ├── lich_day/
    │   ├── index.blade.php
    │   └── show.blade.php
    ├── yeu_cau_doi_lich/
    │   ├── index.blade.php
    │   └── create.blade.php
    ├── lop_hoc/
    │   ├── index.blade.php
    │   ├── show.blade.php
    │   └── danh_sach_hv.blade.php
    ├── diem_danh/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── show.blade.php
    │   └── thong_ke.blade.php
    ├── diem/
    │   ├── index.blade.php
    │   └── bang_diem.blade.php
    ├── danh_gia/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── show.blade.php
    └── profile/
        └── show.blade.php
```

---

## ✅ TIÊU CHÍ HOÀN THÀNH PHASE 3

- [ ] Lịch dạy: danh sách + FullCalendar + modal chi tiết
- [ ] Yêu cầu đổi lịch: gửi / hủy / xem trạng thái
- [ ] Quản lý lớp: chi tiết 4 tab + xuất Excel
- [ ] Điểm danh: form bulk với UX nhanh + counter realtime
- [ ] Thống kê điểm danh: bảng pivot + bar chart
- [ ] Quản lý điểm: inline edit AJAX + tính điểm realtime + khóa điểm
- [ ] Đánh giá HV: slider tiêu chí + preview xếp loại realtime
- [ ] Hồ sơ cá nhân: upload avatar + đổi mật khẩu
- [ ] Dashboard GV: thống kê đầy đủ + timeline hôm nay

---

## 🔜 PHASE TIẾP THEO

- **Phase 4**: Chức năng Học viên (xem lịch, xem điểm, điểm danh, đánh giá khóa học)
- **Phase 5**: Thông báo hệ thống + Báo cáo tổng hợp + Tối ưu & Deploy
