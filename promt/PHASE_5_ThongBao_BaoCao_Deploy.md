# PROMPT GEMINI AGENT — PHASE 5
## Thông Báo · Báo Cáo Tổng Hợp · Tối Ưu Performance · Deploy
### Dự án: Hệ thống Quản lý Giảng viên – Học viện (Laravel)

---

## 🧠 CONTEXT (nhắc lại để Gemini hiểu)

```
- Phase 1 ✅: Auth, phân quyền 3 role, dashboard
- Phase 2 ✅: Admin CRUD người dùng, thời khóa biểu, đánh giá chất lượng
- Phase 3 ✅: Giảng viên: lịch dạy, điểm danh, quản lý điểm, đánh giá HV
- Phase 4 ✅: Học viên: lịch học, kết quả, điểm danh, đánh giá khóa học
- Stack: Laravel + Blade + Tailwind CSS + MySQL
- Package đã có: Spatie Permission, Breeze, maatwebsite/excel, barryvdh/dompdf
- Phase 5 là phase CUỐI: Thông báo + Báo cáo + Tối ưu + Deploy
```

---

# ══════════════════════════════════════
# MODULE A — HỆ THỐNG THÔNG BÁO
# ══════════════════════════════════════

---

## BƯỚC A1 — Migration & Model Thông Báo

```
Tạo migration:

=== Bảng: thong_baos ===
Schema::create('thong_baos', function (Blueprint $table) {
    $table->id();
    $table->string('tieu_de');
    $table->text('noi_dung');
    $table->enum('loai', [
        'lich_hoc',         // thay đổi lịch học
        'diem_so',          // cập nhật điểm
        'diem_danh',        // nhắc điểm danh
        'yeu_cau_doi_lich', // yêu cầu đổi lịch
        'he_thong',         // thông báo hệ thống
        'danh_gia',         // nhắc đánh giá khóa học
        'chung'             // thông báo chung
    ])->default('chung');
    $table->enum('muc_do', ['info','success','warning','danger'])->default('info');
    $table->string('url')->nullable();           // Link đính kèm
    $table->string('icon')->nullable();          // Tên icon Heroicons
    $table->boolean('gui_tat_ca')->default(false); // Gửi toàn bộ user
    $table->foreignId('created_by')->nullable()->constrained('users');
    $table->timestamps();
});

=== Bảng: thong_bao_users (pivot — ai nhận TB gì) ===
Schema::create('thong_bao_users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('thong_bao_id')->constrained('thong_baos')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->boolean('da_doc')->default(false);
    $table->timestamp('doc_luc')->nullable();
    $table->timestamps();
    $table->unique(['thong_bao_id', 'user_id']);
});

Tạo Models:
- app/Models/ThongBao.php
  belongsTo User as createdBy (FK: created_by)
  belongsToMany User through thong_bao_users
  scope: chuaDoc, daDoc, theoLoai

- app/Models/ThongBaoUser.php
  belongsTo ThongBao
  belongsTo User

Thêm vào User.php:
  public function thongBaos() {
      return $this->belongsToMany(ThongBao::class, 'thong_bao_users')
                  ->withPivot('da_doc','doc_luc')->withTimestamps();
  }
  public function soThongBaoChuaDoc() {
      return $this->thongBaos()->wherePivot('da_doc', false)->count();
  }

Chạy: php artisan migrate
```

---

## BƯỚC A2 — Service Gửi Thông Báo

```
Tạo file: app/Services/ThongBaoService.php

class ThongBaoService {

    // Gửi TB đến 1 hoặc nhiều user cụ thể
    public function gui(array $data, array|int $userIds): ThongBao
    {
        $thongBao = ThongBao::create([
            'tieu_de'    => $data['tieu_de'],
            'noi_dung'   => $data['noi_dung'],
            'loai'       => $data['loai'] ?? 'chung',
            'muc_do'     => $data['muc_do'] ?? 'info',
            'url'        => $data['url'] ?? null,
            'icon'       => $data['icon'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $userIds = is_array($userIds) ? $userIds : [$userIds];
        $pivot = collect($userIds)->mapWithKeys(fn($id) => [
            $id => ['da_doc' => false]
        ])->all();
        $thongBao->users()->attach($pivot);

        return $thongBao;
    }

    // Gửi TB đến toàn bộ user theo role
    public function guiTheoRole(array $data, string|array $roles): ThongBao
    {
        $userIds = User::role($roles)->pluck('id')->toArray();
        return $this->gui($data, $userIds);
    }

    // Gửi TB đến tất cả user
    public function guiTatCa(array $data): ThongBao
    {
        $thongBao = ThongBao::create(array_merge($data, ['gui_tat_ca' => true]));
        $userIds = User::where('is_active', true)->pluck('id')->toArray();
        $thongBao->users()->attach(array_fill_keys($userIds, ['da_doc' => false]));
        return $thongBao;
    }

    // Đánh dấu đã đọc
    public function danhDauDaDoc(int $thongBaoId, int $userId): void
    {
        ThongBaoUser::where('thong_bao_id', $thongBaoId)
                    ->where('user_id', $userId)
                    ->update(['da_doc' => true, 'doc_luc' => now()]);
    }

    // Đánh dấu tất cả đã đọc
    public function danhDauTatCaDaDoc(int $userId): void
    {
        ThongBaoUser::where('user_id', $userId)
                    ->where('da_doc', false)
                    ->update(['da_doc' => true, 'doc_luc' => now()]);
    }
}
```

---

## BƯỚC A3 — Tích Hợp Thông Báo Tự Động

```
Tích hợp ThongBaoService vào các Controller đã có:

=== 1. Khi Admin duyệt yêu cầu đổi lịch ===
Trong Admin\YeuCauDoiLichController@duyet():
  app(ThongBaoService::class)->gui([
      'tieu_de'  => 'Yêu cầu đổi lịch đã được duyệt',
      'noi_dung' => 'Yêu cầu đổi lịch buổi học ngày '.$lichHoc->ngay_hoc->format('d/m/Y').
                    ' của lớp '.$lopHoc->ten_lop.' đã được Admin duyệt.',
      'loai'     => 'yeu_cau_doi_lich',
      'muc_do'   => 'success',
      'url'      => route('gv.lich_day.index'),
      'icon'     => 'calendar-check',
  ], $yeuCau->giang_vien_id);

=== 2. Khi Admin từ chối yêu cầu đổi lịch ===
Trong Admin\YeuCauDoiLichController@tuChoi():
  app(ThongBaoService::class)->gui([
      'tieu_de'  => 'Yêu cầu đổi lịch bị từ chối',
      'noi_dung' => 'Yêu cầu đổi lịch ngày '.$ngayGoc.' bị từ chối. Lý do: '.$request->ghi_chu_admin,
      'loai'     => 'yeu_cau_doi_lich',
      'muc_do'   => 'danger',
      'url'      => route('gv.yeu_cau.index'),
      'icon'     => 'x-circle',
  ], $yeuCau->giang_vien_id);

=== 3. Khi GV cập nhật điểm ===
Trong GiangVien\DiemController@nhapDiem():
  // Gửi cho từng HV trong lớp
  $hocVienIds = HocVienLopHoc::where('lop_hoc_id',$lopId)->pluck('hoc_vien_id')->toArray();
  app(ThongBaoService::class)->gui([
      'tieu_de'  => 'Điểm số đã được cập nhật',
      'noi_dung' => 'Giảng viên vừa cập nhật bảng điểm lớp '.$lopHoc->ten_lop.'. Xem ngay!',
      'loai'     => 'diem_so',
      'muc_do'   => 'info',
      'url'      => route('hv.ket_qua.chi_tiet', $lopId),
      'icon'     => 'academic-cap',
  ], $hocVienIds);

=== 4. Khi Admin thay đổi lịch học ===
Trong Admin\LichHocController@update():
  // Gửi cho GV phụ trách + tất cả HV trong lớp
  $giangVienId = $lopHoc->giang_vien_id;
  $hocVienIds  = HocVienLopHoc::where('lop_hoc_id',$lopHoc->id)->pluck('hoc_vien_id')->toArray();
  app(ThongBaoService::class)->gui([
      'tieu_de'  => 'Lịch học có thay đổi',
      'noi_dung' => 'Buổi học ngày '.$lichHoc->ngay_hoc->format('d/m/Y').
                    ' lớp '.$lopHoc->ten_lop.' đã được cập nhật. Vui lòng kiểm tra lại lịch.',
      'loai'     => 'lich_hoc',
      'muc_do'   => 'warning',
      'url'      => route('hv.lich_hoc.index'),
      'icon'     => 'calendar',
  ], array_merge([$giangVienId], $hocVienIds));

=== 5. Nhắc HV đánh giá khóa học sau khi lớp kết thúc ===
Tạo Artisan Command: app/Console/Commands/NhacDanhGiaKhoaHoc.php

php artisan make:command NhacDanhGiaKhoaHoc --command=nhac:danh-gia

Trong handle():
  $hom_nay = today();
  // Tìm các lớp kết thúc trong 7 ngày qua
  $lopDaKetThuc = LopHoc::where('trang_thai','da_ket_thuc')
      ->whereBetween('ngay_ket_thuc', [$hom_nay->subDays(7), $hom_nay])
      ->with('hocViens')->get();

  foreach ($lopDaKetThuc as $lop) {
      foreach ($lop->hocViens as $hv) {
          // Kiểm tra chưa đánh giá
          $daDanhGia = DanhGiaKhoaHoc::where('hoc_vien_id', $hv->id)
                       ->where('lop_hoc_id', $lop->id)->exists();
          if (!$daDanhGia) {
              app(ThongBaoService::class)->gui([
                  'tieu_de'  => 'Đánh giá khóa học của bạn',
                  'noi_dung' => 'Khóa học '.$lop->khoaHoc->ten_khoa_hoc.' đã kết thúc. '.
                                'Hãy dành 2 phút đánh giá để giúp chúng tôi cải thiện!',
                  'loai'     => 'danh_gia',
                  'muc_do'   => 'info',
                  'url'      => route('hv.danh_gia.create', $lop->id),
                  'icon'     => 'star',
              ], $hv->id);
          }
      }
  }
  $this->info('Đã gửi nhắc nhở đánh giá!');

Đăng ký schedule trong routes/console.php (Laravel 11) hoặc app/Console/Kernel.php (L10):
  $schedule->command('nhac:danh-gia')->dailyAt('08:00');

=== 6. Nhắc GV điểm danh buổi học hôm nay (chưa điểm) ===
Tạo command: NhacDiemDanh.php  --command=nhac:diem-danh

Chạy lúc 09:00 hàng ngày:
  Tìm lich_hocs: ngay_hoc = today, trang_thai = da_len_lich, gio_bat_dau < now
  → Gửi TB cho giảng viên phụ trách lớp đó: "Bạn chưa điểm danh buổi học lúc HH:MM..."
```

---

## BƯỚC A4 — Controller & View Thông Báo

```
Tạo: app/Http/Controllers/ThongBaoController.php

=== Method: index() ===
  $userId = Auth::id();
  $thongBaos = Auth::user()->thongBaos()
      ->orderByPivot('created_at', 'desc')
      ->paginate(20);
  $soChưaDoc = Auth::user()->soThongBaoChuaDoc();
  Return view('thong_bao.index', compact('thongBaos','soChưaDoc'))

=== Method: daDoc($id) ===
  app(ThongBaoService::class)->danhDauDaDoc($id, Auth::id());
  // Redirect đến URL đính kèm nếu có
  $thongBao = ThongBao::find($id);
  if ($thongBao->url) return redirect($thongBao->url);
  Return redirect()->back();

=== Method: docTatCa() ===
  app(ThongBaoService::class)->danhDauTatCaDaDoc(Auth::id());
  Return redirect()->back()->with('success','Đã đánh dấu tất cả là đã đọc');

=== Method: layMoi() (AJAX polling) ===
  // Trả JSON cho dropdown header
  $thongBaos = Auth::user()->thongBaos()
      ->wherePivot('da_doc', false)
      ->latest()->take(10)->get();
  Return response()->json([
      'so_chua_doc' => Auth::user()->soThongBaoChuaDoc(),
      'danh_sach'   => $thongBaos->map(fn($tb) => [
          'id'       => $tb->id,
          'tieu_de'  => $tb->tieu_de,
          'noi_dung' => Str::limit($tb->noi_dung, 80),
          'loai'     => $tb->loai,
          'muc_do'   => $tb->muc_do,
          'url'      => $tb->url,
          'icon'     => $tb->icon,
          'thoi_gian'=> $tb->pivot->created_at->diffForHumans(),
      ])->toArray()
  ]);

=== Admin: Tạo thông báo hệ thống ===
Tạo: app/Http/Controllers/Admin/ThongBaoController.php

Method: create() → view form tạo TB
Method: store() → validate + gọi ThongBaoService
  Validate:
    tieu_de  : required|string|max:200
    noi_dung : required|string|max:1000
    loai     : required|in:[danh sách loại]
    muc_do   : required|in:info,success,warning,danger
    url      : nullable|url
    gui_den  : required|in:tat_ca,admin,giang_vien,hoc_vien,cu_the
    user_ids : required_if:gui_den,cu_the|array
Method: index() → danh sách TB đã gửi (admin xem lại)

=== Routes thêm vào web.php ===
// Thông báo (tất cả role)
Route::middleware('auth')->prefix('thong-bao')->name('thong_bao.')->group(function () {
    Route::get('/',         [ThongBaoController::class, 'index'])->name('index');
    Route::get('/lay-moi',  [ThongBaoController::class, 'layMoi'])->name('lay_moi');
    Route::patch('/{id}/doc', [ThongBaoController::class, 'daDoc'])->name('doc');
    Route::patch('/doc-tat-ca', [ThongBaoController::class, 'docTatCa'])->name('doc_tat_ca');
});

// Admin quản lý TB
Route::middleware('admin')->prefix('admin/thong-bao')->name('admin.thong_bao.')->group(function () {
    Route::get('/',        [Admin\ThongBaoController::class, 'index'])->name('index');
    Route::get('/create',  [Admin\ThongBaoController::class, 'create'])->name('create');
    Route::post('/',       [Admin\ThongBaoController::class, 'store'])->name('store');
});

=== VIEW: thong_bao/index.blade.php ===
@extends layout của user hiện tại (admin/giang_vien/hoc_vien)

Header: "Thông báo của tôi" + badge số chưa đọc + nút [Đánh dấu tất cả đã đọc]

Filter tab: [Tất cả] [Chưa đọc] [Lịch học] [Điểm số] [Hệ thống]

List thông báo:
  Mỗi item:
  ┌────────────────────────────────────────────────────┐
  │ 🔵 [ICON] Điểm số đã được cập nhật          2 giờ │
  │     Giảng viên vừa cập nhật bảng điểm lớp Toán A1 │
  │     [→ Xem ngay]                   ● (chấm chưa đọc)│
  └────────────────────────────────────────────────────┘
  - Chưa đọc: nền xanh nhạt + bold tiêu đề + chấm xanh góc phải
  - Đã đọc: nền trắng, text xám
  - Icon theo loại: calendar/academic-cap/star/bell/check-circle
  - Click → gọi route daDoc → redirect đến url nếu có

=== DROPDOWN THÔNG BÁO HEADER (tất cả layout) ===
Thêm vào header của cả 3 layout (admin/giang_vien/hoc_vien):

Button chuông 🔔 + badge số đỏ (nếu > 0):
  <div x-data="{ open: false }" class="relative">
    <button @click="open=!open">
      🔔 <span class="badge">{số chưa đọc}</span>
    </button>
    <div x-show="open" class="dropdown-panel">
      <!-- List 5 TB mới nhất (AJAX) -->
      <div id="tb-list">Loading...</div>
      <a href="route thong_bao.index">Xem tất cả →</a>
    </div>
  </div>

JS polling (mỗi 60 giây):
  async function fetchThongBao() {
    const res = await fetch('/thong-bao/lay-moi');
    const data = await res.json();
    document.querySelector('.badge-tb').textContent = data.so_chua_doc || '';
    // Render danh sách TB trong dropdown
    renderThongBaos(data.danh_sach);
  }
  setInterval(fetchThongBao, 60000);
  fetchThongBao(); // gọi ngay khi load

Cài thêm: Alpine.js (từ CDN) để toggle dropdown:
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

# ══════════════════════════════════════
# MODULE B — BÁO CÁO TỔNG HỢP (ADMIN)
# ══════════════════════════════════════

---

## BƯỚC B1 — Controller Báo Cáo

```
Tạo: app/Http/Controllers/Admin/BaoCaoController.php

=== Method: index() ===
Dashboard báo cáo tổng quan:
  $namHoc = $request->nam_hoc ?? now()->year;
  $kyHoc  = $request->ky_hoc  ?? 1;

  $tongQuan = [
      'tong_giang_vien' => User::role('giang_vien')->where('is_active',true)->count(),
      'tong_hoc_vien'   => User::role('hoc_vien')->where('is_active',true)->count(),
      'tong_lop_hoc'    => LopHoc::where('trang_thai','dang_hoc')->count(),
      'tong_khoa_hoc'   => KhoaHoc::where('trang_thai','dang_mo')->count(),
      'diem_tb_he_thong'=> BangDiem::whereNotNull('diem_trung_binh')->avg('diem_trung_binh'),
      'ti_le_chuyen_can'=> // avg tỷ lệ chuyên cần toàn hệ thống
      'so_danh_gia_thang_nay' => DanhGiaKhoaHoc::whereMonth('created_at', now()->month)->count(),
  ];

  $bieuDoTheoThang = // dữ liệu 12 tháng cho line chart
  $topGiangVien    = // top 5 GV điểm đánh giá cao nhất
  $phanBoXepLoai   = // phân bổ xếp loại HV toàn hệ thống

  Return view('admin.bao_cao.index', compact(
      'tongQuan','bieuDoTheoThang','topGiangVien','phanBoXepLoai','namHoc','kyHoc'
  ))

=== Method: giangVien() ===
Báo cáo chi tiết giảng viên:
  Filter: $namHoc, $kyHoc, $giangVienId (optional)
  Lấy:
  - Danh sách GV + thống kê: số lớp, số HV, tỷ lệ DD, điểm đánh giá
  - Biểu đồ so sánh điểm đánh giá GV (bar chart)
  - Top 3 GV tốt nhất & cần cải thiện
  Return view('admin.bao_cao.giang_vien', compact(...))

=== Method: hocVien() ===
Báo cáo chi tiết học viên:
  Filter: $namHoc, $kyHoc, $lopId, $xepLoai
  Lấy:
  - Tỷ lệ xếp loại toàn hệ thống (doughnut)
  - Phân bổ điểm theo khóa học (radar)
  - Danh sách HV xuất sắc (điểm TB >= 9)
  - Danh sách HV cần hỗ trợ (điểm TB < 5 hoặc CC < 60%)
  Return view('admin.bao_cao.hoc_vien', compact(...))

=== Method: khoaHoc() ===
Báo cáo khóa học:
  - Điểm đánh giá TB từng khóa (bar chart nằm ngang)
  - Tỷ lệ hoàn thành học viên từng lớp
  - So sánh điểm đánh giá các tiêu chí (radar chart)
  - Lớp học có tỷ lệ vắng cao nhất
  Return view('admin.bao_cao.khoa_hoc', compact(...))

=== Method: diemDanh() ===
Báo cáo điểm danh toàn trường:
  Filter: $thang, $nam, $lopId, $giangVienId
  - Tỷ lệ chuyên cần TB toàn trường
  - Top lớp có tỷ lệ vắng cao
  - Biểu đồ chuyên cần theo tháng (line chart)
  - Bảng chi tiết từng GV: số buổi đã dạy/chưa điểm danh
  Return view('admin.bao_cao.diem_danh', compact(...))

=== Method: xuatBaoCaoTongHop(Request $request) ===
Xuất file tổng hợp:
Validate:
  loai_bao_cao: required|in:giang_vien,hoc_vien,khoa_hoc,diem_danh,tong_hop
  dinh_dang   : required|in:excel,pdf
  nam_hoc     : required|integer
  ky_hoc      : nullable|in:1,2

Xử lý:
  - Nếu dinh_dang = 'excel': dùng maatwebsite/excel → download
  - Nếu dinh_dang = 'pdf':   dùng barryvdh/dompdf → download
  - Ghi log xuất báo cáo (ai, lúc nào, loại gì)

=== Method: chartData() ===
AJAX endpoint trả JSON cho Chart.js:
  ?loai=diem_theo_thang&nam=2025
  ?loai=phan_bo_xep_loai&ky=1&nam=2025
  ?loai=so_sanh_gv&ky=1&nam=2025
  Return JSON theo từng loại
```

---

## BƯỚC B2 — Views Báo Cáo

```
=== VIEW: admin/bao_cao/index.blade.php ===
@extends('layouts.admin')

PHẦN 1 — Filter bar:
  Năm học (select) | Kỳ học (1/2) | [Áp dụng]
  Nút: [📥 Xuất báo cáo tổng hợp] → mở modal chọn định dạng

PHẦN 2 — 7 card KPI (2 hàng):
  Hàng 1 (4 card):
    👨‍🏫 Giảng viên đang hoạt động
    👥 Học viên đang học
    🏫 Lớp học đang mở
    📚 Khóa học đang có

  Hàng 2 (3 card màu nổi):
    📊 Điểm TB hệ thống: [7.8/10] — xanh
    ✅ Tỷ lệ chuyên cần: [82%]   — xanh
    ⭐ Đánh giá tháng này: [124] — vàng

PHẦN 3 — 2 biểu đồ lớn (grid 2 cột):

  Chart 1: Line chart — Học viên nhập học + Điểm TB theo tháng
    2 datasets trên 1 chart (dual axis):
    - Dataset 1 (trục trái): Số HV nhập học mỗi tháng → màu xanh
    - Dataset 2 (trục phải): Điểm TB theo tháng → màu cam
    CDN Chart.js: https://cdn.jsdelivr.net/npm/chart.js

  Chart 2: Doughnut — Phân bổ xếp loại học viên
    Xuất sắc/Giỏi/Khá/TB/Yếu
    Legend bên phải với số lượng + phần trăm

PHẦN 4 — Top 5 Giảng viên xuất sắc kỳ này:
  Bảng: Hạng | Avatar | Tên GV | Số lớp | Số HV | Điểm đánh giá | Badge xếp loại
  Row đầu tiên: nền vàng nhạt + icon 🥇

PHẦN 5 — Cảnh báo cần chú ý (Warning panel đỏ nhạt):
  - GV chưa điểm danh: X buổi học quá hạn
  - Lớp có tỷ lệ vắng > 30%: X lớp
  - HV điểm TB < 5.0: X học viên
  [Xem chi tiết →] link đến báo cáo tương ứng

PHẦN 6 — Hoạt động gần đây (Activity feed):
  Timeline 10 hoạt động mới nhất:
  [08:32] GV Nguyễn Văn An vừa điểm danh lớp Toán A1 (15/15 HV)
  [08:15] HV Trần Thị B vừa đánh giá khóa học Python B1 (4.5★)
  [Yesterday] Admin đã duyệt 3 yêu cầu đổi lịch

=== VIEW: admin/bao_cao/giang_vien.blade.php ===
@extends('layouts.admin')

Filter: Kỳ | Năm | GV (search select)

PHẦN 1 — Bar chart nằm ngang: So sánh điểm đánh giá GV (Chart.js)
  Trục Y: tên GV (top 10)
  Trục X: điểm 0-10
  Màu bar: gradient xanh

PHẦN 2 — Bảng chi tiết GV:
  Cột: Avatar | Tên GV | Chuyên môn | Số lớp | Số HV | Buổi đã dạy | Chuyên cần% | Điểm đánh giá | Xếp loại

PHẦN 3 — Phân tích:
  2 card song song:
  🏆 Top 3 GV xuất sắc (nền xanh nhạt)
  ⚠️ GV cần hỗ trợ (nền vàng nhạt) — điểm < 6.5

=== VIEW: admin/bao_cao/hoc_vien.blade.php ===

Filter: Kỳ | Năm | Lớp | Xếp loại

PHẦN 1 — 2 chart (grid 2 col):
  Doughnut: Phân bổ xếp loại
  Bar: Điểm TB theo từng lớp/khóa học

PHẦN 2 — 2 bảng song song:
  🌟 HV Xuất sắc (điểm >= 9): Tên | Lớp | Điểm TB | Nút khen thưởng
  ⚠️ HV cần hỗ trợ (điểm < 5 hoặc CC < 60%): Tên | Lớp | Vấn đề | Nút liên hệ

PHẦN 3 — Bảng đầy đủ paginate:
  Cột: Avatar | Mã HV | Tên | Lớp | CC | KT1 | KT2 | GK | CK | Điểm TB | Xếp loại

PHẦN 4 — Nút xuất:
  [📥 Xuất Excel] [📄 Xuất PDF]

=== VIEW: admin/bao_cao/diem_danh.blade.php ===

Filter: Tháng | Năm | Lớp | GV

PHẦN 1 — 3 card: Tỷ lệ CC toàn trường | Tổng buổi đã dạy | Buổi chưa điểm danh

PHẦN 2 — Line chart: Tỷ lệ chuyên cần theo tháng (12 tháng)
  Đường ngang 80%: ngưỡng yêu cầu (màu đỏ đứt đoạn)

PHẦN 3 — Bảng lớp có tỷ lệ vắng cao (sort desc by ti_le_vang):
  Lớp | GV | Tỷ lệ vắng % | Số buổi đã dạy | [Xem chi tiết]

PHẦN 4 — Bảng GV chưa điểm danh:
  GV | Buổi học | Lớp | Ngày (quá hạn X ngày) | [Nhắc nhở]
```

---

# ══════════════════════════════════════
# MODULE C — TỐI ƯU PERFORMANCE
# ══════════════════════════════════════

---

## BƯỚC C1 — Database Optimization

```
Tạo migration thêm indexes tối ưu query:

php artisan make:migration add_indexes_for_performance

Trong migration:
Schema::table('lich_hocs', function (Blueprint $table) {
    $table->index(['lop_hoc_id', 'ngay_hoc']);
    $table->index(['ngay_hoc', 'trang_thai']);
});

Schema::table('diem_danhs', function (Blueprint $table) {
    $table->index(['lich_hoc_id', 'hoc_vien_id']);
    $table->index(['hoc_vien_id', 'trang_thai']);
});

Schema::table('bang_diems', function (Blueprint $table) {
    $table->index(['hoc_vien_id', 'lop_hoc_id']);
    $table->index('giang_vien_id');
});

Schema::table('hoc_vien_lop_hocs', function (Blueprint $table) {
    $table->index(['hoc_vien_id', 'trang_thai']);
    $table->index('lop_hoc_id');
});

Schema::table('thong_bao_users', function (Blueprint $table) {
    $table->index(['user_id', 'da_doc']);
});

Schema::table('lop_hocs', function (Blueprint $table) {
    $table->index(['giang_vien_id', 'trang_thai']);
});

Chạy: php artisan migrate
```

---

## BƯỚC C2 — Laravel Caching

```
Cấu hình cache trong .env:
CACHE_DRIVER=file   // hoặc redis nếu có

Thêm caching vào các query nặng:

=== 1. Cache thống kê dashboard Admin ===
Trong Admin\DashboardController@index():
  $thongKe = Cache::remember('admin_thong_ke_'.now()->format('Y-m-d'), 1800, function() {
      return [
          'tong_giang_vien' => User::role('giang_vien')->count(),
          'tong_hoc_vien'   => User::role('hoc_vien')->count(),
          // ...
      ];
  });

=== 2. Cache danh sách khóa học (ít thay đổi) ===
Trong các form select khóa học:
  $khoaHocs = Cache::remember('danh_sach_khoa_hoc', 3600, function() {
      return KhoaHoc::where('trang_thai','dang_mo')->orderBy('ten_khoa_hoc')->get();
  });

=== 3. Xóa cache khi có thay đổi ===
Thêm vào KhoaHocController@store()/update()/destroy():
  Cache::forget('danh_sach_khoa_hoc');

Thêm vào DashboardController hoặc Observer:
  Cache::forget('admin_thong_ke_'.now()->format('Y-m-d'));

=== 4. Cache số thông báo chưa đọc ===
Trong layout blade, thay vì query mỗi request:
  $soTB = Cache::remember('tb_chua_doc_'.Auth::id(), 60, function() {
      return Auth::user()->soThongBaoChuaDoc();
  });
  // Xóa cache khi có TB mới hoặc đọc TB:
  Cache::forget('tb_chua_doc_'.Auth::id());
```

---

## BƯỚC C3 — Eager Loading & Query Optimization

```
Kiểm tra và sửa N+1 query trong toàn dự án:

=== Trong GiangVien\LopHocController@index() ===
BEFORE (N+1):
  $lopHocs = LopHoc::where('giang_vien_id', Auth::id())->get();
  // Trong view: $lop->hocViens->count() → query N lần

AFTER (1 query):
  $lopHocs = LopHoc::where('giang_vien_id', Auth::id())
      ->withCount(['hocViens as so_hoc_vien' => fn($q) => $q->where('trang_thai','dang_hoc')])
      ->withCount(['lichHocs as so_buoi_hoan_thanh' => fn($q) => $q->where('trang_thai','hoan_thanh')])
      ->withCount(['lichHocs as so_buoi_con_lai'    => fn($q) => $q->where('trang_thai','da_len_lich')])
      ->with(['khoaHoc:id,ten_khoa_hoc'])
      ->get();

=== Trong Admin\GiangVienController@index() ===
  $giangViens = User::role('giang_vien')
      ->with(['giangVienProfile:user_id,ma_giang_vien,chuyen_mon,hoc_vi,trang_thai'])
      ->withCount(['lopHocs as so_lop_dang_day' => fn($q) => $q->where('trang_thai','dang_hoc')])
      ->paginate(15);

=== Trong HocVien\KetQuaController@index() ===
  $bangDiems = BangDiem::where('hoc_vien_id', Auth::id())
      ->with([
          'lopHoc:id,ten_lop,ma_lop,khoa_hoc_id,giang_vien_id',
          'lopHoc.khoaHoc:id,ten_khoa_hoc',
          'lopHoc.giangVien:id,name',
      ])
      ->orderByDesc('created_at')
      ->get();

=== Thêm Laravel Debugbar (chỉ môi trường local) ===
  composer require barryvdh/laravel-debugbar --dev
  // Tự động active khi APP_DEBUG=true
  // Dùng để phát hiện N+1 query còn sót

  Kiểm tra tất cả trang chính trong local:
  Trang nào query > 20 lần → tìm N+1 và fix với eager loading

=== Tối ưu Pagination ===
Thay vì paginate() cho bảng lớn, dùng simplePaginate():
  // simplePaginate: chỉ có "Trang trước / Trang sau" — nhanh hơn vì không đếm tổng
  $lichHocs->simplePaginate(20); // dùng cho lich_hocs (có thể rất nhiều dòng)
  $thongBaos->paginate(20);      // paginate thường cho TB (cần biết tổng số trang)
```

---

## BƯỚC C4 — Artisan Commands & Scheduled Tasks

```
Tạo thêm các Artisan command hữu ích:

=== 1. Command: DonDep (dọn dẹp định kỳ) ===
php artisan make:command DonDep --command=system:don-dep

Trong handle():
  // Xóa thông báo cũ hơn 90 ngày đã đọc
  $soXoa = ThongBaoUser::where('da_doc', true)
           ->where('created_at', '<', now()->subDays(90))
           ->delete();
  $this->info("Đã xóa $soXoa thông báo cũ.");

  // Xóa avatar orphan (file có trong storage nhưng không có trong DB)
  // ... (scan storage/app/public/avatars/ so với users.avatar)

  // Clear expired cache
  Artisan::call('cache:clear');
  $this->info('Đã dọn dẹp cache.');

=== 2. Command: TaoLichHoc tự động cho kỳ mới ===
php artisan make:command TaoLichHocKyMoi --command=lich:tao-ky-moi

Cho phép admin chạy command để auto-generate lịch học cho kỳ mới
dựa trên template các lớp học.

=== 3. Schedule đầy đủ trong routes/console.php (Laravel 11) ===
use Illuminate\Support\Facades\Schedule;

Schedule::command('nhac:diem-danh')->dailyAt('09:00');
Schedule::command('nhac:danh-gia')->dailyAt('08:00');
Schedule::command('system:don-dep')->weeklyOn(0, '02:00'); // CN 2:00 sáng
Schedule::command('cache:clear')->dailyAt('00:00');

Để chạy scheduler (production):
  * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
  (Thêm vào crontab)
```

---

# ══════════════════════════════════════
# MODULE D — DEPLOY CHECKLIST
# ══════════════════════════════════════

---

## BƯỚC D1 — Chuẩn Bị Môi Trường Production

```
Hãy thực hiện tuần tự các bước chuẩn bị deploy:

=== BƯỚC 1: Kiểm tra .env.example ===
Đảm bảo file .env.example có đầy đủ các key cần thiết:
  APP_NAME=
  APP_ENV=production
  APP_KEY=
  APP_DEBUG=false
  APP_URL=

  DB_CONNECTION=mysql
  DB_HOST=
  DB_PORT=3306
  DB_DATABASE=
  DB_USERNAME=
  DB_PASSWORD=

  CACHE_DRIVER=file
  SESSION_DRIVER=file
  QUEUE_CONNECTION=database

  MAIL_MAILER=smtp
  MAIL_HOST=
  MAIL_PORT=587
  MAIL_USERNAME=
  MAIL_PASSWORD=
  MAIL_FROM_ADDRESS=
  MAIL_FROM_NAME=

  FILESYSTEM_DISK=public

=== BƯỚC 2: Security hardening ===

2a. Cấu hình CORS trong config/cors.php:
  'allowed_origins' => [env('APP_URL')],

2b. Cấu hình Session trong config/session.php:
  'secure' => env('SESSION_SECURE_COOKIE', true),  // chỉ HTTPS
  'same_site' => 'lax',
  'http_only' => true,

2c. Thêm middleware rate limiting trong routes/web.php:
  Route::middleware(['throttle:login'])->group(function () {
      // Route login
  });

  Trong AppServiceProvider@boot():
  RateLimiter::for('login', function (Request $request) {
      return Limit::perMinute(5)->by($request->ip()); // 5 lần/phút
  });

2d. Kiểm tra tất cả form có @csrf token:
  Chạy grep -r "form" resources/views --include="*.blade.php" | grep -v "@csrf"
  → Đảm bảo không form nào thiếu @csrf

2e. Kiểm tra tất cả DELETE/PATCH form dùng @method:
  Chạy grep -r 'method="DELETE"\|method="PATCH"' resources/views
  → Đảm bảo có @method('DELETE') hoặc @method('PATCH')

=== BƯỚC 3: Tối ưu cho production ===

3a. Tạo file .env production với APP_ENV=production, APP_DEBUG=false

3b. Chạy các lệnh optimize:
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  php artisan optimize

3c. Build assets cho production:
  npm run build

3d. Tạo symbolic link storage:
  php artisan storage:link

3e. Set permissions:
  chmod -R 755 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache

=== BƯỚC 4: Database production ===

4a. Chạy migration:
  php artisan migrate --force

4b. Chạy seeder (chỉ data cần thiết, KHÔNG seed data test):
  Tạo ProductionSeeder chỉ gồm:
  - RolePermissionSeeder (bắt buộc)
  - TieuChiDanhGiaSeeder (tiêu chí mặc định)
  - AdminUserSeeder (1 tài khoản admin đầu tiên)
  php artisan db:seed --class=ProductionSeeder --force

4c. Tạo ProductionSeeder:
  Tạo file database/seeders/ProductionSeeder.php:
  - Gọi RolePermissionSeeder
  - Tạo 1 tài khoản admin:
      name: 'Super Admin',
      email: env('ADMIN_EMAIL', 'admin@academy.com'),
      password: Hash::make(env('ADMIN_PASSWORD', 'ChangeMe@2025!')),
  - Gán role admin
  - Tạo 5 tiêu chí đánh giá mặc định cho từng loại

=== BƯỚC 5: Web server configuration ===

5a. Nếu dùng Apache — tạo/kiểm tra .htaccess trong public/:
  Options -MultiViews -Indexes
  RewriteEngine On
  RewriteCond %{HTTP:Authorization} .
  RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^ index.php [L]

5b. Nếu dùng Nginx — thêm config:
  location / {
      try_files $uri $uri/ /index.php?$query_string;
  }
  location ~ \.php$ {
      fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
      fastcgi_index index.php;
      include fastcgi_params;
      fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
  }
  location ~ /\.(?!well-known).* {
      deny all;
  }

=== BƯỚC 6: Cài đặt crontab cho scheduler ===
  crontab -e
  Thêm dòng:
  * * * * * cd /var/www/html/academy && php artisan schedule:run >> /dev/null 2>&1
```

---

## BƯỚC D2 — Testing Toàn Diện Trước Deploy

```
Chạy test toàn diện, báo cáo từng mục:

=== Tạo Feature Tests ===
php artisan make:test AuthTest
php artisan make:test AdminUserManagementTest
php artisan make:test GiangVienTest
php artisan make:test HocVienTest

=== AuthTest.php ===
test cases:
  ✅ login với admin đúng → redirect /admin/dashboard
  ✅ login với giang_vien đúng → redirect /giang-vien/dashboard
  ✅ login với hoc_vien đúng → redirect /hoc-vien/dashboard
  ✅ login sai password → có lỗi validation
  ✅ hoc_vien truy cập /admin/dashboard → redirect về home
  ✅ giang_vien truy cập /admin/dashboard → redirect về home
  ✅ khách (chưa login) truy cập /admin/dashboard → redirect login
  ✅ rate limit: login sai 6 lần/phút → 429

=== AdminUserManagementTest.php ===
  ✅ admin tạo giảng viên mới → user + profile tạo thành công
  ✅ admin tạo HV email trùng → validation error
  ✅ admin xóa GV chưa có lớp → is_active = false
  ✅ admin reset password → có thể login bằng password123

=== GiangVienTest.php ===
  ✅ GV xem lịch dạy → chỉ thấy lịch của mình
  ✅ GV điểm danh buổi học → diem_danh records tạo đúng
  ✅ GV nhập điểm → tính điểm TB đúng công thức
  ✅ GV gửi yêu cầu đổi lịch → trang_thai = cho_duyet
  ✅ GV không thể xem lịch lớp của GV khác → 403

=== HocVienTest.php ===
  ✅ HV xem lịch học → chỉ thấy lịch lớp đang tham gia
  ✅ HV xem điểm → chỉ thấy điểm của mình
  ✅ HV đánh giá khóa học → bản ghi tạo đúng
  ✅ HV đánh giá 2 lần cùng lớp → lỗi unique

Chạy: php artisan test --coverage
Mục tiêu: coverage >= 70%

=== Kiểm tra thủ công cuối cùng ===
Checklist đăng nhập từng role và thử đủ luồng nghiệp vụ:
□ Admin: Thêm GV → Tạo lớp → Xếp lịch → Xem báo cáo → Gửi TB
□ GV: Xem lịch → Điểm danh → Nhập điểm → Đánh giá HV → Gửi đổi lịch
□ HV: Xem lịch → Xem điểm → Xem điểm danh → Đánh giá KH → Cập nhật hồ sơ
□ Admin duyệt yêu cầu đổi lịch → GV nhận thông báo
□ GV cập nhật điểm → HV nhận thông báo
□ Polling TB mỗi 60s hoạt động (kiểm tra Network tab)
□ In PDF bảng điểm → file đúng định dạng
□ Xuất Excel → file đúng cột dữ liệu
□ Mobile responsive: sidebar collapse đúng
```

---

## BƯỚC D3 — Routes Báo Cáo & File .env.example

```
Thêm vào routes/web.php trong group admin:

// Module Báo cáo
Route::prefix('bao-cao')->name('admin.bao_cao.')->group(function() {
    Route::get('/',                         [Admin\BaoCaoController::class, 'index'])->name('index');
    Route::get('/giang-vien',               [Admin\BaoCaoController::class, 'giangVien'])->name('giang_vien');
    Route::get('/hoc-vien',                 [Admin\BaoCaoController::class, 'hocVien'])->name('hoc_vien');
    Route::get('/khoa-hoc',                 [Admin\BaoCaoController::class, 'khoaHoc'])->name('khoa_hoc');
    Route::get('/diem-danh',                [Admin\BaoCaoController::class, 'diemDanh'])->name('diem_danh');
    Route::get('/xuat',                     [Admin\BaoCaoController::class, 'xuatBaoCaoTongHop'])->name('xuat');
    Route::get('/chart-data',               [Admin\BaoCaoController::class, 'chartData'])->name('chart_data');
});

// Module Thông báo Admin
Route::prefix('thong-bao')->name('admin.thong_bao.')->group(function() {
    Route::get('/',        [Admin\ThongBaoController::class, 'index'])->name('index');
    Route::get('/create',  [Admin\ThongBaoController::class, 'create'])->name('create');
    Route::post('/',       [Admin\ThongBaoController::class, 'store'])->name('store');
});

Tạo file .env.example đầy đủ với tất cả các key cần thiết.
Tạo file README.md hướng dẫn cài đặt:
  1. Clone project
  2. composer install
  3. cp .env.example .env → chỉnh sửa
  4. php artisan key:generate
  5. php artisan migrate
  6. php artisan db:seed --class=ProductionSeeder
  7. php artisan storage:link
  8. npm install && npm run build
  9. Cấu hình web server
  10. Cấu hình crontab
```

---

## BƯỚC D4 — Kiểm Tra Cuối & Báo Cáo Hoàn Thành

```
Chạy lần lượt và báo cáo kết quả:

=== 1. Chạy tất cả tests ===
php artisan test
→ Kỳ vọng: 0 failures

=== 2. Kiểm tra routes ===
php artisan route:list | wc -l
→ Liệt kê tổng số routes, đảm bảo không có route conflict

=== 3. Kiểm tra không có lỗi syntax ===
php -l app/Http/Controllers/Admin/*.php
php -l app/Http/Controllers/GiangVien/*.php
php -l app/Http/Controllers/HocVien/*.php
php -l app/Models/*.php
php -l app/Services/*.php

=== 4. Chạy optimize ===
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

=== 5. Kiểm tra storage link ===
ls -la public/storage
→ Phải là symlink đến ../storage/app/public

=== 6. Seed data production ===
php artisan db:seed --class=ProductionSeeder

=== 7. Test đăng nhập production ===
- Đăng nhập admin@academy.com → dashboard OK
- Kiểm tra thông báo chuông hiện số đúng
- Kiểm tra chart trong báo cáo load đúng

=== 8. Báo cáo tổng kết toàn dự án ===
Sau khi hoàn thành, hãy tạo file SUMMARY.md liệt kê:
  - Tổng số file đã tạo
  - Tổng số migration
  - Tổng số routes
  - Tổng số views
  - Packages đã sử dụng
  - Hướng dẫn đổi thông tin học viện (tên, logo, màu sắc)
  - Known limitations & đề xuất cải thiện tiếp theo
```

---

## 📁 CẤU TRÚC FILE SAU PHASE 5

```
app/
├── Console/Commands/
│   ├── NhacDiemDanh.php             ← MỚI
│   ├── NhacDanhGiaKhoaHoc.php       ← MỚI
│   └── DonDep.php                   ← MỚI
├── Http/Controllers/
│   ├── ThongBaoController.php       ← MỚI (dùng chung)
│   └── Admin/
│       ├── BaoCaoController.php     ← MỚI
│       └── ThongBaoController.php   ← MỚI
├── Models/
│   ├── ThongBao.php                 ← MỚI
│   └── ThongBaoUser.php             ← MỚI
└── Services/
    └── ThongBaoService.php          ← MỚI
database/
├── migrations/
│   └── xxxx_add_indexes_for_performance.php
└── seeders/
    └── ProductionSeeder.php         ← MỚI
resources/views/
├── thong_bao/
│   └── index.blade.php              ← MỚI
└── admin/
    └── bao_cao/
        ├── index.blade.php          ← MỚI
        ├── giang_vien.blade.php     ← MỚI
        ├── hoc_vien.blade.php       ← MỚI
        ├── khoa_hoc.blade.php       ← MỚI
        └── diem_danh.blade.php      ← MỚI
tests/Feature/
├── AuthTest.php                     ← MỚI
├── AdminUserManagementTest.php      ← MỚI
├── GiangVienTest.php                ← MỚI
└── HocVienTest.php                  ← MỚI
.env.example                         (cập nhật đầy đủ)
README.md                            ← MỚI
SUMMARY.md                           ← MỚI (tạo cuối cùng)
```

---

## ✅ TIÊU CHÍ HOÀN THÀNH PHASE 5 & TOÀN DỰ ÁN

**Phase 5:**
- [ ] Hệ thống thông báo: gửi/nhận/đánh dấu đọc hoạt động
- [ ] Dropdown chuông polling 60s cập nhật badge
- [ ] 6 loại thông báo tự động được tích hợp
- [ ] 2 Artisan command nhắc nhở chạy đúng
- [ ] Dashboard báo cáo Admin: 7 KPI + 4 chart
- [ ] Báo cáo GV/HV/KH/DD với xuất Excel + PDF
- [ ] Database indexes tối ưu được thêm
- [ ] N+1 query đã được fix với eager loading
- [ ] Cache cho query nặng hoạt động
- [ ] Feature tests: 0 failure
- [ ] ProductionSeeder chạy được
- [ ] Deploy checklist hoàn thành
- [ ] README.md + SUMMARY.md có đầy đủ thông tin

**Toàn dự án (Phase 1–5):**
- [ ] 3 vai trò hoạt động độc lập, phân quyền đúng
- [ ] Luồng admin → duyệt → GV nhận thông báo
- [ ] Luồng GV cập nhật điểm → HV nhận thông báo
- [ ] Luồng HV đánh giá → admin tổng hợp báo cáo
- [ ] Xuất PDF + Excel ở tất cả chỗ cần
- [ ] Chart.js hiển thị đúng ở tất cả trang báo cáo
- [ ] FullCalendar hoạt động ở lịch GV và HV
- [ ] Mobile responsive cơ bản

---

## 🎉 TỔNG KẾT 5 PHASE

```
Phase 1: Auth + Phân quyền + Dashboard          ← Nền tảng
Phase 2: Admin (Users + TKB + Đánh giá)         ← Quản trị
Phase 3: Giảng viên (6 module chức năng)        ← Nghiệp vụ GV
Phase 4: Học viên (7 module chức năng)          ← Nghiệp vụ HV
Phase 5: Thông báo + Báo cáo + Tối ưu + Deploy ← Hoàn thiện
```
