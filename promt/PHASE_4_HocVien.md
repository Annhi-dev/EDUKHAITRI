# PROMPT GEMINI AGENT — PHASE 4
## Học Viên: Lịch Học · Kết Quả · Điểm Danh · Đánh Giá Khóa Học · Hồ Sơ
### Dự án: Hệ thống Quản lý Giảng viên – Học viện (Laravel)

---

## 🧠 CONTEXT (nhắc lại để Gemini hiểu)

```
- Phase 1 ✅: Auth, phân quyền 3 role, dashboard riêng từng vai trò
- Phase 2 ✅: Admin CRUD người dùng, thời khóa biểu, đánh giá chất lượng
- Phase 3 ✅: Giảng viên: lịch dạy, lớp, điểm danh, điểm số, đánh giá HV
- Bảng đã có: users, hoc_vien_profiles, lop_hocs, lich_hocs, hoc_vien_lop_hocs,
              diem_danhs, bang_diems, danh_gia_hoc_viens, danh_gia_khoa_hocs,
              tieu_chi_danh_gias, khoa_hocs, giang_vien_profiles
- Route HV: prefix /hoc-vien, middleware 'hoc_vien', name 'hv.*'
- Stack: Laravel + Blade + Tailwind CSS + MySQL
- User đang login là học viên → lấy bằng Auth::user()
```

---

# ══════════════════════════════════════
# MODULE A — LAYOUT & DASHBOARD HỌC VIÊN
# ══════════════════════════════════════

---

## BƯỚC A1 — Layout Chung Học Viên

```
Tạo file: resources/views/layouts/hoc_vien.blade.php

Cấu trúc layout:
- Sidebar trái 240px cố định, màu chủ đạo blue-600 / blue-700
- Logo học viện + tên "Cổng Học viên"
- Avatar học viên + Tên + Mã học viên hiển thị ở đầu sidebar
- Menu items với Heroicons SVG inline:
    🏠 Dashboard           → route('hv.dashboard')
    📅 Lịch học            → route('hv.lich_hoc.index')
    📊 Kết quả học tập     → route('hv.ket_qua.index')
    ✅ Điểm danh           → route('hv.diem_danh.index')
    ⭐ Đánh giá khóa học  → route('hv.danh_gia.index')
    📚 Khóa học của tôi    → route('hv.khoa_hoc.index')
    👤 Hồ sơ cá nhân      → route('hv.profile.show')
- Active state: highlight bằng Route::is()
- Header top: Avatar nhỏ + Tên học viên + Dropdown (Hồ sơ / Đăng xuất)
- Flash message toast (success/error/info) tự ẩn sau 4 giây
- @yield('content') | @yield('scripts')
- Responsive: sidebar collapse thành icon trên mobile
```

---

## BƯỚC A2 — Cập Nhật Dashboard Học Viên

```
Mở rộng: app/Http/Controllers/HocVien/DashboardController.php

Method index():
  $hocVienId = Auth::id();
  $today = today();

  // Lịch hôm nay
  $lichHomNay = LichHoc::whereHas('lopHoc.hocViens', fn($q) =>
      $q->where('users.id', $hocVienId)
        ->where('hoc_vien_lop_hocs.trang_thai', 'dang_hoc')
    )->whereDate('ngay_hoc', $today)
     ->orderBy('gio_bat_dau')->with('lopHoc.giangVien')->get();

  // Lịch 7 ngày tới
  $lichTuanNay = LichHoc::whereHas('lopHoc.hocViens', fn($q) =>
      $q->where('users.id', $hocVienId)
    )->whereBetween('ngay_hoc', [now()->startOfWeek(), now()->endOfWeek()])
     ->orderBy('ngay_hoc')->orderBy('gio_bat_dau')->get();

  // Thống kê học tập
  $lopDangHoc   = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
                  ->where('trang_thai', 'dang_hoc')->count();
  $diemTrungBinh = BangDiem::where('hoc_vien_id', $hocVienId)
                   ->whereNotNull('diem_trung_binh')->avg('diem_trung_binh');
  $tileChuyenCan = // tính từ diem_danhs: có_mặt / tổng buổi đã học * 100
  $chuaDanhGia   = // số lớp đã hoàn thành nhưng chưa đánh giá khóa học

  // Thông báo chưa xác nhận điểm danh hôm nay (nếu có tính năng tự xác nhận)
  $canDiemDanh  = $lichHomNay->filter(fn($l) =>
      !DiemDanh::where('lich_hoc_id',$l->id)
               ->where('hoc_vien_id',$hocVienId)->exists()
  )->count();

  Return view('hoc_vien.dashboard', compact(
      'lichHomNay','lichTuanNay',
      'lopDangHoc','diemTrungBinh','tileChuyenCan',
      'chuaDanhGia','canDiemDanh'
  ));

=== VIEW: hoc_vien/dashboard.blade.php ===
@extends('layouts.hoc_vien')

PHẦN 1 — Banner chào (gradient xanh nhạt):
  "Xin chào, {tên học viên} 👋"
  Mã HV: {ma_hoc_vien} | Ngày: {today dạng 'Thứ X, dd/mm/yyyy'}

PHẦN 2 — 4 Card thống kê:
  📚 Lớp đang học       : {$lopDangHoc}
  📊 Điểm TB            : {$diemTrungBinh ?? '--'}/10
  ✅ Chuyên cần         : {$tileChuyenCan ?? '--'}%
  ⭐ Chờ đánh giá       : {$chuaDanhGia} khóa học
  → Card "Chờ đánh giá" màu vàng + link đến trang đánh giá nếu > 0

PHẦN 3 — Lịch hôm nay (timeline):
  Tiêu đề: "📅 Hôm nay — {date}"
  Nếu không có lịch:
    Icon 🎉 + "Hôm nay bạn không có buổi học nào. Hãy nghỉ ngơi!"
  Nếu có:
    Timeline từng buổi học:
    [08:00] ──● Lớp Toán A1 · Phòng A101 · GV Nguyễn Văn An
                Badge: "Đã điểm danh ✅" hoặc "Chưa xác nhận ⚠️"

PHẦN 4 — Lịch tuần (7 ô ngày ngang):
  Mỗi ô: Thứ | Ngày | Số buổi học
  - Ô hôm nay: viền xanh đậm, nền xanh nhạt
  - Ô có lịch: dot xanh phía dưới
  - Click vào ô → link đến lịch học ngày đó

PHẦN 5 — Kết quả học tập gần đây:
  Bảng nhỏ 5 hàng: Lớp | Khóa học | Điểm TB | Xếp loại
  Nút [Xem tất cả →]
```

---

# ══════════════════════════════════════
# MODULE B — LỊCH HỌC
# ══════════════════════════════════════

---

## BƯỚC B1 — Controller Lịch Học

```
Tạo file: app/Http/Controllers/HocVien/LichHocController.php

=== Method: index() ===
- Lấy $hocVienId = Auth::id()
- Lấy tất cả lop_hoc_ids học viên đang tham gia:
  $lopIds = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', 'dang_hoc')->pluck('lop_hoc_id')

- Query LichHoc::whereIn('lop_hoc_id', $lopIds)
  ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])
  ->orderBy('ngay_hoc')->orderBy('gio_bat_dau')

- Filter:
    + ?tuan=YYYY-Wnn (tuần ISO) — mặc định tuần hiện tại
    + ?thang=mm&nam=yyyy
    + ?lop_id=X
    + ?view=list|calendar (mặc định list)

- Tính: với mỗi lich_hoc → kiểm tra diem_danhs của HV này đã có chưa
- Thêm property: $lichHoc->da_diem_danh (bool)

- Return view('hoc_vien.lich_hoc.index', compact(
    'lichHocs','lopIds','lopHocs','filters','currentTuan'
  ))

=== Method: getEvents() ===
- AJAX cho FullCalendar: trả JSON
- Query lich_hocs của học viên theo ?start= ?end=
- Format event:
  {
    "id": 1,
    "title": "Toán A1 · A101",
    "start": "2025-06-10T08:00:00",
    "end":   "2025-06-10T10:00:00",
    "backgroundColor": "#2563eb",   // xanh=da_len_lich, xám=hoan_thanh, đỏ=huy
    "borderColor": "#1d4ed8",
    "extendedProps": {
      "lop":        "Lớp Toán A1",
      "giang_vien": "GV Nguyễn Văn An",
      "phong":      "A101",
      "khoa_hoc":   "Toán nâng cao",
      "da_diem_danh": true,
      "trang_thai": "hoan_thanh"
    }
  }

=== Method: show($lichHocId) ===
- Chi tiết 1 buổi học
- Kiểm tra học viên có trong lớp đó không (abort 403 nếu không)
- Load: lichHoc → lopHoc → khoaHoc, giangVien + giangVienProfile
- Load: diemDanh của HV cho buổi này (nếu có)
- Return view('hoc_vien.lich_hoc.show', compact('lichHoc','diemDanh'))
```

---

## BƯỚC B2 — Views Lịch Học

```
=== VIEW: hoc_vien/lich_hoc/index.blade.php ===
@extends('layouts.hoc_vien')

HEADER:
  Tiêu đề "Lịch học của tôi"
  Toggle: [📋 Danh sách] [📅 Lịch tháng]
  Bộ lọc: Tuần (prev ◀ | label "Tuần X, Tháng Y" | next ▶) | Lớp (select)

--- CHẾ ĐỘ DANH SÁCH ---
Nhóm theo ngày (group by ngay_hoc):

  Nhóm ngày: header "📅 Thứ 3, 10/06/2025"
  → Nếu là hôm nay: header màu xanh đậm + badge "Hôm nay"
  → Nếu là ngày qua: header xám mờ

  Mỗi buổi học: card ngang
  ┌──────────────────────────────────────────────────┐
  │ [08:00–10:00]  Lớp Toán A1                       │
  │ 📚 Toán nâng cao  |  👨‍🏫 GV Nguyễn Văn An        │
  │ 🏛️ Phòng A101     |  [✅ Đã điểm danh] / [⚠️ Vắng]│
  └──────────────────────────────────────────────────┘
  - Border trái màu theo trạng thái: xanh=da_len_lich, xám=hoan_thanh, đỏ=huy
  - Badge điểm danh: xanh="Có mặt", vàng="Đi muộn", đỏ="Vắng", xám="Chưa có"
  - Click card → gv.lich_hoc.show (xem chi tiết)

--- CHẾ ĐỘ LỊCH THÁNG ---
FullCalendar CDN: https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js
- locale: 'vi'
- initialView: 'dayGridMonth'
- events: fetch từ /hoc-vien/lich-hoc/events?start=...&end=...
- eventClick: mở modal popup:
    Tên lớp | Khóa học | Giảng viên | Phòng học | Giờ
    Badge điểm danh của HV
    Nút [Xem chi tiết]
- Màu event:
    Đã điểm danh có mặt   → #16a34a (xanh)
    Vắng có phép          → #d97706 (vàng)
    Vắng không phép       → #dc2626 (đỏ)
    Chưa điểm danh        → #2563eb (xanh dương)
    Buổi bị hủy           → #6b7280 (xám)

=== VIEW: hoc_vien/lich_hoc/show.blade.php ===
@extends('layouts.hoc_vien')

Breadcrumb: Lịch học > {Thứ, ngày}

2 cột:
TRÁI — Thông tin buổi học:
  - Ngày học, Thứ, Giờ học, Phòng học
  - Badge trạng thái buổi học
  - Tên lớp + Khóa học (link đến khoa_hoc.show)

PHẢI — Thông tin giảng viên:
  - Avatar GV + Tên + Học vị + Chuyên môn
  - Card nhỏ liên hệ (email nếu muốn hiện)

PHẦN DƯỚI — Trạng thái điểm danh:
  Nếu chưa có diem_danh:
    Card màu vàng: "⚠️ Chưa có dữ liệu điểm danh cho buổi này"
  Nếu đã có:
    Card màu tương ứng:
    "✅ Có mặt | 🕐 08:05 | Ghi chú: ..."
    hoặc "❌ Vắng không phép | Ghi chú: ..."
```

---

# ══════════════════════════════════════
# MODULE C — KẾT QUẢ HỌC TẬP (XEM ĐIỂM)
# ══════════════════════════════════════

---

## BƯỚC C1 — Controller Kết Quả Học Tập

```
Tạo file: app/Http/Controllers/HocVien/KetQuaController.php

=== Method: index() ===
- Lấy tất cả BangDiem của HV hiện tại
- Eager load: lopHoc → khoaHoc, giangVien
- Group by kỳ/năm học (tính từ ngay_bat_dau lop)
- Tính tổng hợp:
    $diemTBToanKhoa = avg(diem_trung_binh) tất cả lớp
    $xepLoaiChung   = tính từ diemTBToanKhoa
    $lopDaHoanThanh = count lớp có bang_diem
    $lopDangHoc     = count lớp đang học chưa có điểm CK
- Return view('hoc_vien.ket_qua.index', compact(
    'bangDiems','diemTBToanKhoa','xepLoaiChung',
    'lopDaHoanThanh','lopDangHoc'
  ))

=== Method: chiTiet($lopId) ===
- Lấy BangDiem của HV cho lớp cụ thể
- Kiểm tra HV có trong lớp đó không
- Load: lopHoc → khoaHoc, giangVien + giangVienProfile
- Load: diemDanhs của HV trong lớp này → tính tỷ lệ chuyên cần
- Load: danhGiaHocVien của GV cho HV này (nếu có)
- Tính chi tiết từng thành phần điểm + trọng số
- Return view('hoc_vien.ket_qua.chi_tiet', compact(
    'bangDiem','lopHoc','diemDanhs','tileChuyenCan','danhGia'
  ))

=== Method: inBangDiem($lopId) ===
- Xuất PDF bảng điểm cá nhân 1 lớp
- Dùng barryvdh/laravel-dompdf
- Template: resources/views/hoc_vien/ket_qua/pdf.blade.php
  (logo học viện, thông tin HV, bảng điểm, chữ ký GV)
- File name: "BangDiem_{maHV}_{maLop}.pdf"
- Return response PDF download
```

---

## BƯỚC C2 — Views Kết Quả Học Tập

```
=== VIEW: hoc_vien/ket_qua/index.blade.php ===
@extends('layouts.hoc_vien')

PHẦN 1 — Tổng quan thành tích:
  Card lớn gradient xanh:
    Điểm TB toàn khóa: [8.5/10] — hiển thị to, font-bold
    Xếp loại chung: [GIỎI] — badge màu vàng lớn
    Đã hoàn thành: X lớp | Đang học: Y lớp

PHẦN 2 — Biểu đồ radar (Chart.js):
  Radar chart các môn/lớp so sánh điểm thành phần:
  Trục: Chuyên cần | Kiểm tra 1 | Kiểm tra 2 | Giữa kỳ | Cuối kỳ
  Màu fill: blue với opacity 0.3, borderColor blue-600

PHẦN 3 — Bảng kết quả theo từng lớp:

  Với mỗi kỳ/năm học → Accordion group:
    Header: "Kỳ 1 — Năm học 2024-2025"  [mở/đóng]
    Body: Bảng các lớp trong kỳ đó:
      Cột: Lớp | Khóa học | Giảng viên | CC | KT1 | KT2 | GK | CK | Điểm TB | Xếp loại | Thao tác
      - Ô điểm: màu xanh nếu >= 5, đỏ nếu < 5
      - Xếp loại: badge màu theo mức
      - Thao tác: [Chi tiết] [In bảng điểm PDF]
      - Nếu đang học (chưa có điểm CK): hiện "--" + tag "Đang học"

PHẦN 4 — Chart điểm TB từng lớp (Bar chart nằm ngang):
  Trục Y: tên lớp/khóa học
  Trục X: điểm 0-10
  Màu bar: gradient xanh-xanh đậm, đường thẳng 5.0 màu đỏ nhạt làm mốc đạt

=== VIEW: hoc_vien/ket_qua/chi_tiet.blade.php ===
@extends('layouts.hoc_vien')

Breadcrumb: Kết quả > {tên lớp}

PHẦN 1 — Header:
  Tên lớp + Khóa học | GV: Tên + Học vị | Thời gian: ngày bắt đầu - kết thúc
  Nút [🖨️ In bảng điểm PDF] (góc phải)

PHẦN 2 — Bảng điểm chi tiết với trọng số:
  ┌─────────────────────────────────────────────────────────┐
  │  Thành phần       │ Trọng số │ Điểm     │ Điểm × Trọng │
  ├─────────────────────────────────────────────────────────┤
  │  Chuyên cần (CC)  │  10%     │  9.5     │  0.95        │
  │  Kiểm tra 1       │  15%     │  7.0     │  1.05        │
  │  Kiểm tra 2       │  15%     │  8.0     │  1.20        │
  │  Giữa kỳ (GK)     │  20%     │  7.5     │  1.50        │
  │  Cuối kỳ (CK)     │  40%     │  8.0     │  3.20        │
  ├─────────────────────────────────────────────────────────┤
  │  ĐIỂM TRUNG BÌNH  │  100%    │  [7.90]  │   ← to đậm  │
  │  XẾP LOẠI         │          │  [KHÁ]   │  badge màu  │
  └─────────────────────────────────────────────────────────┘

PHẦN 3 — Chuyên cần chi tiết:
  Progress bar lớn: "Có mặt: 18/24 buổi (75%)"
  Mini bảng: mỗi buổi học → icon ✅/❌/📝/⚠️

PHẦN 4 — Nhận xét của giảng viên (nếu có):
  Card xám nhạt:
    Avatar GV + Tên + Ngày đánh giá
    Xếp loại GV đánh giá: badge
    Nhận xét: text đầy đủ

=== VIEW: hoc_vien/ket_qua/pdf.blade.php ===
(Template PDF — không extends layout)

<!DOCTYPE html>
<html>
Nội dung:
  - Header: Logo học viện + tên học viện + địa chỉ
  - Tiêu đề: "BẢNG KẾT QUẢ HỌC TẬP"
  - Thông tin học viên: Họ tên | Mã HV | Lớp | Khóa học
  - Thông tin giảng viên: Họ tên GV | Học vị
  - Bảng điểm (như phần trên, dạng bảng in)
  - Nhận xét giảng viên
  - Footer: Ngày in | Chữ ký xác nhận (dạng placeholder text)
Style: font-size nhỏ hơn, dùng CSS thuần cho dompdf
```

---

# ══════════════════════════════════════
# MODULE D — ĐIỂM DANH HỌC VIÊN
# ══════════════════════════════════════

---

## BƯỚC D1 — Controller Điểm Danh Học Viên

```
Tạo file: app/Http/Controllers/HocVien/DiemDanhController.php

=== Method: index() ===
- Lấy tất cả lớp HV đang tham gia
- Với mỗi lớp tính:
    + tong_buoi_da_hoc   = count lich_hocs trang_thai = hoan_thanh
    + so_buoi_co_mat     = count diem_danhs của HV với trang_thai = co_mat
    + so_buoi_vang_cp    = count diem_danhs trang_thai = vang_co_phep
    + so_buoi_vang_kp    = count diem_danhs trang_thai = vang_khong_phep
    + ti_le_chuyen_can   = (co_mat + di_muon + ve_som) / tong_buoi * 100
- Return view('hoc_vien.diem_danh.index', compact('lopHocs','thongKes'))

=== Method: chiTiet($lopId) ===
- Kiểm tra HV có trong lớp
- Lấy TẤT CẢ lich_hocs của lớp (đã và chưa dạy)
- Với mỗi lich_hoc: lấy diem_danh của HV (nếu có)
- Tính thống kê chi tiết
- Return view('hoc_vien.diem_danh.chi_tiet', compact(
    'lopHoc','lichHocs','thongKe'
  ))

=== Method: xacNhanDiemDanh(Request $request) ===
TÍNH NĂNG: Học viên tự xác nhận có mặt (như check-in)
Chỉ hoạt động khi:
  1. Buổi học đang diễn ra (ngay_hoc = today VÀ
     gio_bat_dau - 15 phút <= now <= gio_ket_thuc)
  2. GV chưa điểm danh buổi đó
  3. HV chưa tự xác nhận

Validate:
  lich_hoc_id: required|exists:lich_hocs,id

Xử lý:
  - Kiểm tra điều kiện trên
  - Tạo DiemDanh tạm: trang_thai = 'co_mat', ghi_chu = 'Học viên tự xác nhận'
  - GV vẫn có thể override sau khi điểm danh chính thức
  - Return JSON: { success: true, message: 'Đã xác nhận có mặt!' }

NOTE: Nếu không muốn tính năng tự xác nhận này, bỏ method này đi,
chỉ để học viên XEM điểm danh do GV nhập.
```

---

## BƯỚC D2 — Views Điểm Danh Học Viên

```
=== VIEW: hoc_vien/diem_danh/index.blade.php ===
@extends('layouts.hoc_vien')

PHẦN 1 — Tổng quan chuyên cần (tất cả lớp):
  Card lớn: Chuyên cần TB tất cả lớp: [82%]
  Progress bar tổng: màu xanh nếu >= 80%, vàng nếu >= 60%, đỏ nếu < 60%

PHẦN 2 — Danh sách lớp học:
  Mỗi lớp = 1 card:
  ┌─────────────────────────────────────────────────────┐
  │ Lớp Toán A1  ·  Toán nâng cao                       │
  │ GV: Nguyễn Văn An                                    │
  │ ████████████░░░  Chuyên cần: 18/24 buổi (75%)       │
  │ ✅ 16  📝 2  ❌ 3  ⚠️ 3  ──  Còn lại: 6 buổi       │
  │ [Xem chi tiết →]                                     │
  └─────────────────────────────────────────────────────┘
  Progress bar màu theo tỷ lệ:
  - Xanh >= 80% | Vàng >= 60% | Đỏ < 60%
  - Cảnh báo nhỏ màu đỏ nếu < 70%: "⚠️ Nguy cơ không đủ điều kiện thi"

=== VIEW: hoc_vien/diem_danh/chi_tiet.blade.php ===
@extends('layouts.hoc_vien')

Breadcrumb: Điểm danh > {tên lớp}

PHẦN 1 — Thống kê chuyên cần lớp này:
  5 card nhỏ:
  ✅ Có mặt: 16  |  ⚠️ Đi muộn: 2  |  🔵 Về sớm: 1  |  📝 Vắng CP: 2  |  ❌ Vắng KP: 3
  Progress bar lớn: "75% — Chuyên cần"

  Cảnh báo nếu < 80%:
  "⚠️ Chuyên cần hiện tại dưới 80%. Bạn cần có mặt ít nhất X buổi còn lại để đạt yêu cầu."
  (Tính toán: cần bao nhiêu buổi nữa để đạt 80%)

PHẦN 2 — Lịch điểm danh theo buổi:

  Bảng đầy đủ:
  Cột: # | Ngày | Thứ | Giờ | Trạng thái | Giờ đến thực tế | Ghi chú GV

  - Badge trạng thái:
      ✅ xanh  = có mặt
      ⚠️ cam   = đi muộn
      🔵 nhạt  = về sớm
      📝 vàng  = vắng có phép
      ❌ đỏ   = vắng không phép
      ➖ xám   = buổi sắp tới (chưa dạy)
      🚫 gạch  = buổi bị hủy

  - Row highlight: buổi hôm nay nổi bật
  - Hover row: hiện tooltip ghi chú đầy đủ

PHẦN 3 — Chart (Chart.js):
  Doughnut: phân bố các trạng thái điểm danh
  Màu: xanh/cam/xanh nhạt/vàng/đỏ
```

---

# ══════════════════════════════════════
# MODULE E — ĐÁNH GIÁ KHÓA HỌC
# ══════════════════════════════════════

---

## BƯỚC E1 — Controller Đánh Giá Khóa Học

```
Tạo file: app/Http/Controllers/HocVien/DanhGiaController.php

=== Method: index() ===
- Lấy các lớp HV đã hoàn thành (trang_thai = da_hoan_thanh HOẶC lop_hoc.trang_thai = da_ket_thuc)
- Với mỗi lớp: kiểm tra đã có danh_gia_khoa_hocs chưa
- Phân loại:
    $chuaDanhGia = danh sách lớp chưa đánh giá
    $daDanhGia   = danh sách đánh giá đã làm
- Return view('hoc_vien.danh_gia.index', compact('chuaDanhGia','daDanhGia'))

=== Method: create($lopId) ===
- Kiểm tra HV có trong lớp, lớp đã kết thúc
- Kiểm tra chưa đánh giá (nếu rồi → redirect với message)
- Load: lopHoc → khoaHoc, giangVien + giangVienProfile
- Load: tieuChis = TieuChiDanhGia::where('loai','khoa_hoc')->where('is_active',true)->get()
- Return view('hoc_vien.danh_gia.create', compact('lopHoc','tieuChis'))

=== Method: store(Request $request) ===
Validate:
  lop_hoc_id           : required|exists:lop_hocs,id
  diem_noi_dung        : required|integer|min:1|max:5
  diem_giang_vien      : required|integer|min:1|max:5
  diem_co_so_vat_chat  : required|integer|min:1|max:5
  chi_tiet_danh_gia    : required|array        // [{tieu_chi_id, diem}]
  chi_tiet_danh_gia.*.diem: required|numeric|min:1|max:10
  gop_y                : nullable|string|max:1000
  an_danh              : boolean

Xử lý:
  - Kiểm tra chưa đánh giá lớp này (unique)
  - Tính diem_trung_binh từ chi_tiet_danh_gia (có trọng số)
  - Tạo DanhGiaKhoaHoc::create([...])
  - Return redirect()->route('hv.danh_gia.index')
              ->with('success','Cảm ơn bạn đã đánh giá! Phản hồi của bạn rất có giá trị.')

=== Method: show($id) ===
- Xem đánh giá đã gửi (readonly)
- Return view('hoc_vien.danh_gia.show', compact('danhGia','tieuChis'))

=== Method: edit($id) ===
- Chỉ cho sửa trong 7 ngày sau khi tạo
- Return view('hoc_vien.danh_gia.edit', compact('danhGia','lopHoc','tieuChis'))

=== Method: update(Request $request, $id) ===
- Cập nhật đánh giá (validate như store)
- Return redirect()->route('hv.danh_gia.index')->with('success','Đã cập nhật đánh giá!')
```

---

## BƯỚC E2 — Views Đánh Giá Khóa Học

```
=== VIEW: hoc_vien/danh_gia/index.blade.php ===
@extends('layouts.hoc_vien')

PHẦN 1 — Cần đánh giá (nổi bật):
  Tiêu đề: "⭐ Khóa học chờ đánh giá của bạn ({count})"
  Card màu vàng nhạt cho từng lớp chưa đánh giá:
  ┌──────────────────────────────────────────────┐
  │ 📚 Toán nâng cao  ·  Lớp Toán A1             │
  │ 👨‍🏫 GV Nguyễn Văn An   📅 Kết thúc 30/05/2025 │
  │ [⭐ Đánh giá ngay →]    ← nút màu vàng       │
  └──────────────────────────────────────────────┘

  Nếu không có lớp nào cần đánh giá:
  "🎉 Bạn đã đánh giá tất cả các khóa học!"

PHẦN 2 — Đã đánh giá:
  Tiêu đề: "Đánh giá đã gửi"
  List card nhỏ hơn, màu xám nhạt:
  ┌──────────────────────────────────────────────┐
  │ Lập trình Python  · Lớp Python B1            │
  │ ★★★★☆ 4.2/5  |  Ngày đánh giá: 01/06/2025   │
  │ [Xem] [Sửa] (nếu trong 7 ngày)              │
  └──────────────────────────────────────────────┘

=== VIEW: hoc_vien/danh_gia/create.blade.php ===
@extends('layouts.hoc_vien')

Breadcrumb: Đánh giá > {tên khóa học}

HEADER — Thông tin khóa học:
  Card: Tên khóa | Lớp | GV (avatar + tên + học vị) | Thời gian học

FORM ĐÁNH GIÁ:

SECTION 1 — Đánh giá tổng quan (Star rating):
  3 hàng star rating (click để chọn 1-5 sao):
  "Nội dung khóa học" ★★★★☆
  "Giảng viên"         ★★★★★
  "Cơ sở vật chất"    ★★★☆☆

  JS Star Rating:
  - 5 icon ★ SVG, click → fill sao từ 1 đến điểm chọn
  - Hover effect: preview sao trước khi click
  - Hidden input lưu giá trị

SECTION 2 — Đánh giá chi tiết theo tiêu chí:
  Mỗi tiêu chí: label + slider (1-10) + hiện số điểm
  Slider có màu thay đổi: đỏ (1-4) → vàng (5-6) → xanh (7-10)
  Hiện label text: "Rất tệ / Tệ / Bình thường / Tốt / Xuất sắc"

SECTION 3 — Góp ý:
  Textarea: placeholder "Chia sẻ cảm nhận của bạn về khóa học này...
                         (góp ý giúp chúng tôi cải thiện chất lượng)"
  Character counter: "0/1000 ký tự"

SECTION 4 — Tùy chọn ẩn danh:
  Toggle switch: "Gửi ẩn danh (tên bạn sẽ không hiển thị trong báo cáo)"
  Mặc định: BẬT (an_danh = true)

FOOTER:
  [Gửi đánh giá 📤]  |  [Hủy]
  Text nhỏ: "Đánh giá của bạn sẽ được giữ bí mật và chỉ dùng để cải thiện chất lượng đào tạo."

=== VIEW: hoc_vien/danh_gia/show.blade.php ===
@extends('layouts.hoc_vien')

Hiển thị đánh giá đã gửi (readonly, đẹp):
- Tên khóa + GV
- Star rating hiển thị (readonly)
- Chi tiết tiêu chí (progress bar readonly)
- Góp ý (blockquote style)
- Badge: "Ẩn danh" hoặc "Hiển thị tên"
- Ngày gửi + nút [✏️ Chỉnh sửa] nếu trong 7 ngày
```

---

# ══════════════════════════════════════
# MODULE F — KHÓA HỌC CỦA TÔI
# ══════════════════════════════════════

---

## BƯỚC F1 — Controller & View Khóa Học

```
Tạo file: app/Http/Controllers/HocVien/KhoaHocController.php

=== Method: index() ===
- Lấy tất cả lớp HV đã/đang tham gia
- Phân nhóm: Đang học | Đã hoàn thành | Bảo lưu
- Return view('hoc_vien.khoa_hoc.index', compact('lopHocs'))

=== Method: show($lopId) ===
- Chi tiết 1 lớp/khóa học của HV
- Load: lopHoc → khoaHoc, giangVien + profile, lichHocs, bangDiem, diemDanhs
- Tính tiến độ: % buổi đã học, điểm hiện tại (nếu có)
- Return view('hoc_vien.khoa_hoc.show', compact('lopHoc','bangDiem','tienDo'))

=== VIEW: hoc_vien/khoa_hoc/index.blade.php ===
@extends('layouts.hoc_vien')

Tab: [Đang học] [Đã hoàn thành] [Bảo lưu]

Mỗi tab: grid 2-3 cột card:
┌──────────────────────────────────────────┐
│ 📚 Toán nâng cao                          │
│ Lớp Toán A1  |  GV Nguyễn Văn An         │
│ ████████░░░░  Tiến độ: 16/24 buổi (67%)  │
│ 📅 01/01 → 30/06/2025                    │
│ Điểm TB hiện tại: 7.8 (nếu có)           │
│ [Xem chi tiết]                            │
└──────────────────────────────────────────┘

=== VIEW: hoc_vien/khoa_hoc/show.blade.php ===
@extends('layouts.hoc_vien')

Trang chi tiết khóa học/lớp học của HV:

PHẦN 1 — Header:
  Tên khóa + Lớp + Badge trạng thái
  Thời gian: ngày bắt đầu → kết thúc
  Tiến độ tổng: progress bar lớn

PHẦN 2 — Thông tin giảng viên:
  Card: Avatar + Tên + Học vị + Chuyên môn + Email

PHẦN 3 — Tab nhanh:
  [📅 Lịch học] [📊 Điểm số] [✅ Điểm danh] [⭐ Đánh giá]
  → Mỗi tab link đến đúng trang module tương ứng, lọc theo lop_id này
```

---

# ══════════════════════════════════════
# MODULE G — HỒ SƠ CÁ NHÂN HỌC VIÊN
# ══════════════════════════════════════

---

## BƯỚC G1 — Controller & View Hồ Sơ

```
Mở rộng ProfileController:

=== Method: showHocVien() ===
- Load user + hocVienProfile
- Thống kê: số lớp đang học, điểm TB, tỷ lệ chuyên cần
- Return view('hoc_vien.profile.show', compact('user','profile','thongKe'))

=== Method: updateHocVien(Request $request) ===
Validate:
  name     : required|string|max:100
  phone    : nullable|digits:10
  avatar   : nullable|image|mimes:jpg,png,jpeg|max:2048
  ngay_sinh: nullable|date|before:today
  gioi_tinh: nullable|in:nam,nu,khac
  dia_chi  : nullable|string|max:255

Xử lý:
  - Upload avatar nếu có → xóa ảnh cũ nếu tồn tại
  - Update user (name, phone)
  - Update hocVienProfile (ngay_sinh, gioi_tinh, dia_chi)
  - Return back()->with('success','Cập nhật thành công!')

=== VIEW: hoc_vien/profile/show.blade.php ===
@extends('layouts.hoc_vien')

Layout 2 cột:

TRÁI (1/3) — Card hồ sơ:
  Avatar lớn (click → input upload → preview realtime)
  Tên học viên — to đậm
  Mã học viên: HV001 — xám
  Badge tình trạng: Đang học / Bảo lưu

  Thống kê cá nhân:
  📚 X lớp đang học
  📊 Điểm TB: 8.2
  ✅ Chuyên cần: 85%
  📅 Ngày nhập học: dd/mm/yyyy

PHẢI (2/3) — Tab:

  [👤 Thông tin cá nhân]  [🔑 Đổi mật khẩu]

  --- Tab Thông tin ---
  Form 2 cột:
    Họ tên (*)     | Email (readonly + badge "Không thể đổi")
    Số điện thoại  | Mã học viên (readonly)
    Ngày sinh      | Giới tính (radio: Nam/Nữ/Khác)
    Địa chỉ (full width — textarea)
  Nút [💾 Lưu thay đổi]

  --- Tab Đổi mật khẩu ---
  Mật khẩu hiện tại | (eye toggle show/hide)
  Mật khẩu mới     | Strength bar (Yếu/TB/Mạnh/Rất mạnh)
  Xác nhận mật khẩu
  Nút [🔑 Đổi mật khẩu]

  JS Strength indicator:
  - Yếu: < 8 ký tự → bar đỏ
  - Trung bình: 8+ ký tự → bar vàng
  - Mạnh: 8+ ký tự + số + chữ → bar xanh
  - Rất mạnh: + ký tự đặc biệt → bar xanh đậm
```

---

## BƯỚC G2 — Routes Học Viên (Tổng hợp)

```
Thêm vào routes/web.php trong group middleware('hoc_vien')->prefix('hoc-vien')->name('hv.'):

// Module B — Lịch học
Route::get('lich-hoc',                [HocVien\LichHocController::class, 'index'])->name('lich_hoc.index');
Route::get('lich-hoc/events',         [HocVien\LichHocController::class, 'getEvents'])->name('lich_hoc.events');
Route::get('lich-hoc/{id}',           [HocVien\LichHocController::class, 'show'])->name('lich_hoc.show');

// Module C — Kết quả học tập
Route::get('ket-qua',                 [HocVien\KetQuaController::class, 'index'])->name('ket_qua.index');
Route::get('ket-qua/{lopId}',         [HocVien\KetQuaController::class, 'chiTiet'])->name('ket_qua.chi_tiet');
Route::get('ket-qua/{lopId}/in-pdf',  [HocVien\KetQuaController::class, 'inBangDiem'])->name('ket_qua.pdf');

// Module D — Điểm danh
Route::get('diem-danh',               [HocVien\DiemDanhController::class, 'index'])->name('diem_danh.index');
Route::get('diem-danh/{lopId}',       [HocVien\DiemDanhController::class, 'chiTiet'])->name('diem_danh.chi_tiet');
Route::post('diem-danh/xac-nhan',     [HocVien\DiemDanhController::class, 'xacNhanDiemDanh'])->name('diem_danh.xac_nhan');

// Module E — Đánh giá khóa học
Route::get('danh-gia',                [HocVien\DanhGiaController::class, 'index'])->name('danh_gia.index');
Route::get('danh-gia/{lopId}/create', [HocVien\DanhGiaController::class, 'create'])->name('danh_gia.create');
Route::post('danh-gia',               [HocVien\DanhGiaController::class, 'store'])->name('danh_gia.store');
Route::get('danh-gia/{id}',           [HocVien\DanhGiaController::class, 'show'])->name('danh_gia.show');
Route::get('danh-gia/{id}/edit',      [HocVien\DanhGiaController::class, 'edit'])->name('danh_gia.edit');
Route::put('danh-gia/{id}',           [HocVien\DanhGiaController::class, 'update'])->name('danh_gia.update');

// Module F — Khóa học của tôi
Route::get('khoa-hoc',                [HocVien\KhoaHocController::class, 'index'])->name('khoa_hoc.index');
Route::get('khoa-hoc/{lopId}',        [HocVien\KhoaHocController::class, 'show'])->name('khoa_hoc.show');

// Module G — Hồ sơ cá nhân
Route::get('ho-so',                   [ProfileController::class, 'showHocVien'])->name('profile.show');
Route::patch('ho-so',                 [ProfileController::class, 'updateHocVien'])->name('profile.update_hv');
Route::patch('ho-so/mat-khau',        [ProfileController::class, 'updatePassword'])->name('profile.password');
```

---

## BƯỚC G3 — Seeder Dữ Liệu Mẫu Phase 4

```
Tạo file: database/seeders/Phase4Seeder.php

Tạo dữ liệu mẫu để test:

1. Thêm học viên HV001 → HV003 vào Lớp L001 (Toán A1) và L002 (Python B1)
   → HocVienLopHoc::create([...])

2. Tạo điểm danh mẫu cho 10 buổi học đã qua:
   - HV001: 8 có mặt, 1 vắng CP, 1 vắng KP
   - HV002: 9 có mặt, 1 vắng KP
   - HV003: 6 có mặt, 2 đi muộn, 2 vắng KP

3. Tạo bảng điểm mẫu:
   - HV001/L001: KT1=7, KT2=8, GK=7.5, CK=8 → TB tự tính
   - HV002/L001: KT1=9, KT2=8.5, GK=9, CK=8.5 → TB tự tính

4. Tạo đánh giá mẫu từ GV cho HV001 trong L001

5. Tạo tiêu chí đánh giá khóa học nếu chưa có (5 tiêu chí loại khoa_hoc)

Chạy: php artisan db:seed --class=Phase4Seeder
```

---

## BƯỚC G4 — Kiểm Thử Phase 4

```
Đăng nhập với hv@academy.com và kiểm tra từng module:

=== Dashboard ===
1. /hoc-vien/dashboard → 4 card thống kê hiển thị đúng
2. Timeline hôm nay → hiển thị lịch học hôm nay (hoặc "Không có lịch")
3. Lịch 7 ngày ngang → dot xuất hiện ở ngày có lịch
4. Bảng kết quả gần đây → 5 hàng đúng dữ liệu

=== Module B — Lịch học ===
1. /hoc-vien/lich-hoc → danh sách nhóm theo ngày, đúng lớp của HV
2. Chuyển calendar → FullCalendar load events đúng màu
3. Click event → modal chi tiết đúng thông tin
4. Vào show buổi học → hiển thị trạng thái điểm danh

=== Module C — Kết quả ===
1. /hoc-vien/ket-qua → card tổng quan + radar chart + bảng theo kỳ
2. Click chi tiết lớp → bảng điểm với trọng số đúng công thức
3. Chuyên cần chi tiết → từng buổi đúng icon
4. Nhận xét GV hiển thị (nếu có)
5. In PDF → file download được, đúng template

=== Module D — Điểm danh ===
1. /hoc-vien/diem-danh → danh sách lớp + progress bar đúng màu
2. Cảnh báo đỏ nếu < 70%
3. Chi tiết lớp → bảng từng buổi đúng icon
4. Tính toán "cần X buổi nữa để đạt 80%" đúng
5. Doughnut chart hiển thị

=== Module E — Đánh giá ===
1. /hoc-vien/danh-gia → card vàng lớp chưa đánh giá + list đã đánh giá
2. Vào create → star rating click được + hover effect
3. Slider tiêu chí đổi màu đỏ/vàng/xanh
4. Toggle ẩn danh hoạt động
5. Submit → success message, redirect đúng
6. Xem đánh giá đã gửi → readonly đẹp
7. Trong 7 ngày → nút sửa xuất hiện

=== Module F — Khóa học ===
1. /hoc-vien/khoa-hoc → 3 tab (đang/đã/bảo lưu) đúng dữ liệu
2. Chi tiết lớp → thông tin đầy đủ + link đến các module khác

=== Module G — Hồ sơ ===
1. /hoc-vien/ho-so → thông tin đầy đủ, thống kê đúng
2. Upload avatar → preview realtime + lưu thành công
3. Strength bar mật khẩu hoạt động
4. Đổi mật khẩu → validation + success

Báo cáo: ✅ OK hoặc ❌ Lỗi [mô tả]. Sửa lỗi ngay nếu có.
```

---

## 📁 CẤU TRÚC FILE SAU PHASE 4

```
app/Http/Controllers/HocVien/
├── DashboardController.php       (cập nhật thống kê đầy đủ)
├── LichHocController.php         ← MỚI
├── KetQuaController.php          ← MỚI
├── DiemDanhController.php        ← MỚI
├── DanhGiaController.php         ← MỚI
├── KhoaHocController.php         ← MỚI
└── (ProfileController chung)

database/seeders/
└── Phase4Seeder.php              ← MỚI

resources/views/
├── layouts/
│   └── hoc_vien.blade.php        ← MỚI
└── hoc_vien/
    ├── dashboard.blade.php        (cập nhật đầy đủ)
    ├── lich_hoc/
    │   ├── index.blade.php        (list + FullCalendar)
    │   └── show.blade.php
    ├── ket_qua/
    │   ├── index.blade.php        (radar chart + bảng kỳ)
    │   ├── chi_tiet.blade.php     (bảng trọng số + chuyên cần)
    │   └── pdf.blade.php          (template in PDF)
    ├── diem_danh/
    │   ├── index.blade.php        (list lớp + progress)
    │   └── chi_tiet.blade.php     (bảng từng buổi + chart)
    ├── danh_gia/
    │   ├── index.blade.php        (chờ đánh giá + đã đánh giá)
    │   ├── create.blade.php       (star rating + slider)
    │   ├── edit.blade.php
    │   └── show.blade.php         (readonly đẹp)
    ├── khoa_hoc/
    │   ├── index.blade.php        (3 tab)
    │   └── show.blade.php
    └── profile/
        └── show.blade.php         (2 cột + strength bar)
```

---

## ✅ TIÊU CHÍ HOÀN THÀNH PHASE 4

- [ ] Layout học viên: sidebar xanh + menu đầy đủ + flash toast
- [ ] Dashboard: 4 card + timeline hôm nay + lịch tuần + bảng kết quả
- [ ] Lịch học: danh sách nhóm ngày + FullCalendar + modal click
- [ ] Kết quả: tổng quan + radar chart + bảng trọng số + in PDF
- [ ] Điểm danh: progress bar màu + cảnh báo ngưỡng + từng buổi + doughnut
- [ ] Đánh giá: star rating JS + slider màu + toggle ẩn danh + readonly show
- [ ] Khóa học: 3 tab trạng thái + chi tiết đầy đủ
- [ ] Hồ sơ: upload avatar preview + strength bar + đổi mật khẩu
- [ ] Seeder Phase 4 chạy được với dữ liệu test đầy đủ

---

## 🔜 PHASE TIẾP THEO

- **Phase 5**: Thông báo real-time (Laravel Echo / Pusher) · Tìm kiếm toàn hệ thống · Báo cáo tổng hợp Admin · Tối ưu performance · Deploy checklist
