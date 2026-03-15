<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\GiangVien\DashboardController as GiangVienDashboard;
use App\Http\Controllers\HocVien\DashboardController as HocVienDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ===== ADMIN ROUTES =====
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Quản lý Giảng viên
        Route::resource('giang-vien', \App\Http\Controllers\Admin\GiangVienController::class)->names([
            'index'   => 'giang_vien.index',
            'create'  => 'giang_vien.create',
            'store'   => 'giang_vien.store',
            'show'    => 'giang_vien.show',
            'edit'    => 'giang_vien.edit',
            'update'  => 'giang_vien.update',
            'destroy' => 'giang_vien.destroy',
        ]);
        Route::patch('giang-vien/{id}/toggle-active',  [\App\Http\Controllers\Admin\GiangVienController::class, 'toggleActive'])->name('giang_vien.toggle');
        Route::patch('giang-vien/{id}/reset-password', [\App\Http\Controllers\Admin\GiangVienController::class, 'resetPassword'])->name('giang_vien.reset_password');

        // Quản lý Học viên
        Route::resource('hoc-vien', \App\Http\Controllers\Admin\HocVienController::class)->names([
            'index'   => 'hoc_vien.index',
            'create'  => 'hoc_vien.create',
            'store'   => 'hoc_vien.store',
            'show'    => 'hoc_vien.show',
            'edit'    => 'hoc_vien.edit',
            'update'  => 'hoc_vien.update',
            'destroy' => 'hoc_vien.destroy',
        ]);
        Route::patch('hoc-vien/{id}/toggle-active',  [\App\Http\Controllers\Admin\HocVienController::class, 'toggleActive'])->name('hoc_vien.toggle');
        Route::patch('hoc-vien/{id}/reset-password', [\App\Http\Controllers\Admin\HocVienController::class, 'resetPassword'])->name('hoc_vien.reset_password');

        // Quản lý Khóa học & Lớp học
        Route::resource('khoa-hoc', \App\Http\Controllers\Admin\KhoaHocController::class)->names('khoa_hoc');
        Route::resource('lop-hoc', \App\Http\Controllers\Admin\LopHocController::class)->names('lop_hoc');
        Route::post('lop-hoc/preview-schedule', [\App\Http\Controllers\Admin\LopHocController::class, 'previewSchedule'])->name('lop_hoc.preview');
        Route::post('lop-hoc/{id}/add-hoc-vien', [\App\Http\Controllers\Admin\LopHocController::class, 'addHocVien'])->name('lop_hoc.add_hv');
        Route::delete('lop-hoc/{lopId}/remove-hoc-vien/{hvId}', [\App\Http\Controllers\Admin\LopHocController::class, 'removeHocVien'])->name('lop_hoc.remove_hv');

        // Quản lý Lịch học & Yêu cầu
        Route::get('lich-hoc', [\App\Http\Controllers\Admin\LichHocController::class, 'index'])->name('lich_hoc.index');
        Route::get('lich-hoc/events', [\App\Http\Controllers\Admin\LichHocController::class, 'getEvents'])->name('lich_hoc.events');
        Route::get('yeu-cau-doi-lich', [\App\Http\Controllers\Admin\YeuCauDoiLichController::class, 'index'])->name('yeu_cau.index');
        Route::patch('yeu-cau-doi-lich/{id}/duyet', [\App\Http\Controllers\Admin\YeuCauDoiLichController::class, 'duyet'])->name('yeu_cau.duyet');
        Route::post('yeu-cau-doi-lich/{id}/tu-choi', [\App\Http\Controllers\Admin\YeuCauDoiLichController::class, 'tuChoi'])->name('yeu_cau_doi_lich.tu_choi');

        // Module Báo cáo
        Route::prefix('bao-cao')->name('bao_cao.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Admin\BaoCaoController::class, 'index'])->name('index');
            Route::get('/giang-vien', [\App\Http\Controllers\Admin\BaoCaoController::class, 'giangVien'])->name('giang_vien');
            Route::get('/hoc-vien', [\App\Http\Controllers\Admin\BaoCaoController::class, 'hocVien'])->name('hoc_vien');
        });

        // Module Thông báo Admin
        Route::prefix('thong-bao')->name('thong_bao.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Admin\ThongBaoController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ThongBaoController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ThongBaoController::class, 'store'])->name('store');
        });

        // Đánh giá chất lượng
        Route::prefix('danh-gia')->name('danh_gia.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Admin\DanhGiaController::class, 'index'])->name('index');
            Route::get('/tieu-chi', [\App\Http\Controllers\Admin\DanhGiaController::class, 'tieuChi'])->name('tieu_chi');
            Route::post('/tieu-chi', [\App\Http\Controllers\Admin\DanhGiaController::class, 'storeTieuChi'])->name('tieu_chi.store');
            Route::delete('/tieu-chi/{id}', [\App\Http\Controllers\Admin\DanhGiaController::class, 'destroyTieuChi'])->name('tieu_chi.destroy');
            Route::get('/giang-vien', [\App\Http\Controllers\Admin\DanhGiaController::class, 'danhGiaGiangVien'])->name('giang_vien');
            Route::post('/giang-vien/{id}', [\App\Http\Controllers\Admin\DanhGiaController::class, 'taoOrCapNhatDanhGiaGV'])->name('giang_vien.store');
            Route::get('/hoc-vien', [\App\Http\Controllers\Admin\DanhGiaController::class, 'danhGiaHocVien'])->name('hoc_vien');
            Route::get('/khoa-hoc', [\App\Http\Controllers\Admin\DanhGiaController::class, 'danhGiaKhoaHoc'])->name('khoa_hoc');
        });
    });

    // ===== THÔNG BÁO ROUTES (Dùng chung) =====
    Route::middleware('auth')->prefix('thong-bao')->name('thong_bao.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ThongBaoController::class, 'index'])->name('index');
        Route::get('/lay-moi', [\App\Http\Controllers\ThongBaoController::class, 'layMoi'])->name('lay_moi');
        Route::patch('/{id}/doc', [\App\Http\Controllers\ThongBaoController::class, 'daDoc'])->name('doc');
        Route::patch('/doc-tat-ca', [\App\Http\Controllers\ThongBaoController::class, 'docTatCa'])->name('doc_tat_ca');
    });

    // ===== GIẢNG VIÊN ROUTES =====
    Route::middleware('giang_vien')->prefix('giang-vien')->name('gv.')->group(function () {
        Route::get('/dashboard', [GiangVienDashboard::class, 'index'])->name('dashboard');

        // Module A — Lịch dạy
        Route::get('lich-day',           [\App\Http\Controllers\GiangVien\LichDayController::class, 'index'])->name('lich_day.index');
        Route::get('lich-day/events',    [\App\Http\Controllers\GiangVien\LichDayController::class, 'getEvents'])->name('lich_day.events');
        Route::get('lich-day/{id}',      [\App\Http\Controllers\GiangVien\LichDayController::class, 'show'])->name('lich_day.show');

        // Module A — Yêu cầu đổi lịch
        Route::get('yeu-cau-doi-lich',              [\App\Http\Controllers\GiangVien\YeuCauDoiLichController::class, 'index'])->name('yeu_cau.index');
        Route::get('yeu-cau-doi-lich/create',       [\App\Http\Controllers\GiangVien\YeuCauDoiLichController::class, 'create'])->name('yeu_cau.create');
        Route::post('yeu-cau-doi-lich',             [\App\Http\Controllers\GiangVien\YeuCauDoiLichController::class, 'store'])->name('yeu_cau.store');
        Route::delete('yeu-cau-doi-lich/{id}',      [\App\Http\Controllers\GiangVien\YeuCauDoiLichController::class, 'destroy'])->name('yeu_cau.destroy');

        // Module B — Quản lý lớp
        Route::get('lop-hoc',                       [\App\Http\Controllers\GiangVien\LopHocController::class, 'index'])->name('lop_hoc.index');
        Route::get('lop-hoc/{id}',                  [\App\Http\Controllers\GiangVien\LopHocController::class, 'show'])->name('lop_hoc.show');
        Route::get('lop-hoc/{id}/hoc-vien',         [\App\Http\Controllers\GiangVien\LopHocController::class, 'danhSachHocVien'])->name('lop_hoc.hoc_vien');
        Route::get('lop-hoc/{id}/xuat-danh-sach',   [\App\Http\Controllers\GiangVien\LopHocController::class, 'xuatDanhSach'])->name('lop_hoc.xuat');

        // Module C — Điểm danh
        Route::get('diem-danh',                     [\App\Http\Controllers\GiangVien\DiemDanhController::class, 'index'])->name('diem_danh.index');
        Route::get('diem-danh/create',              [\App\Http\Controllers\GiangVien\DiemDanhController::class, 'create'])->name('diem_danh.create');
        Route::post('diem-danh',                    [\App\Http\Controllers\GiangVien\DiemDanhController::class, 'store'])->name('diem_danh.store');
        Route::get('diem-danh/{lichHocId}',         [\App\Http\Controllers\GiangVien\DiemDanhController::class, 'show'])->name('diem_danh.show');
        Route::get('diem-danh/{lichHocId}/edit',    [\App\Http\Controllers\GiangVien\DiemDanhController::class, 'edit'])->name('diem_danh.edit');
        Route::put('diem-danh/{lichHocId}',         [\App\Http\Controllers\GiangVien\DiemDanhController::class, 'store'])->name('diem_danh.update');
        Route::get('diem-danh/thong-ke',            [\App\Http\Controllers\GiangVien\DiemDanhController::class, 'thongKe'])->name('diem_danh.thong_ke');

        // Module D — Quản lý điểm
        Route::get('diem',                          [\App\Http\Controllers\GiangVien\DiemController::class, 'index'])->name('diem.index');
        Route::get('diem/{lopId}/bang-diem',        [\App\Http\Controllers\GiangVien\DiemController::class, 'bangDiem'])->name('diem.bang_diem');
        Route::post('diem/{lopId}/nhap',            [\App\Http\Controllers\GiangVien\DiemController::class, 'nhapDiem'])->name('diem.nhap');
        Route::post('diem/ajax',                    [\App\Http\Controllers\GiangVien\DiemController::class, 'nhapDiemAjax'])->name('diem.ajax');
        Route::patch('diem/{lopId}/khoa',           [\App\Http\Controllers\GiangVien\DiemController::class, 'khoaDiem'])->name('diem.khoa');
        Route::patch('diem/{lopId}/mo-khoa',        [\App\Http\Controllers\GiangVien\DiemController::class, 'moKhoaDiem'])->name('diem.mo_khoa');
        Route::get('diem/{lopId}/xuat',             [\App\Http\Controllers\GiangVien\DiemController::class, 'xuatBangDiem'])->name('diem.xuat');

        // Module E — Đánh giá học viên
        Route::get('danh-gia',                      [\App\Http\Controllers\GiangVien\DanhGiaHocVienController::class, 'index'])->name('danh_gia.index');
        Route::get('danh-gia/{lopId}/create',       [\App\Http\Controllers\GiangVien\DanhGiaHocVienController::class, 'create'])->name('danh_gia.create');
        Route::post('danh-gia',                     [\App\Http\Controllers\GiangVien\DanhGiaHocVienController::class, 'store'])->name('danh_gia.store');
        Route::get('danh-gia/{id}',                 [\App\Http\Controllers\GiangVien\DanhGiaHocVienController::class, 'show'])->name('danh_gia.show');
        Route::get('danh-gia/{id}/edit',            [\App\Http\Controllers\GiangVien\DanhGiaHocVienController::class, 'edit'])->name('danh_gia.edit');
        Route::put('danh-gia/{id}',                 [\App\Http\Controllers\GiangVien\DanhGiaHocVienController::class, 'update'])->name('danh_gia.update');

        // Module F — Hồ sơ cá nhân
        Route::get('ho-so',                         [\App\Http\Controllers\ProfileController::class, 'showGiangVien'])->name('profile.show');
        Route::patch('ho-so',                       [\App\Http\Controllers\ProfileController::class, 'updateGiangVien'])->name('profile.update_gv');
        Route::patch('ho-so/mat-khau',              [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // ===== HỌC VIÊN ROUTES =====
    Route::middleware('hoc_vien')->prefix('hoc-vien')->name('hv.')->group(function () {
        Route::get('/dashboard', [HocVienDashboard::class, 'index'])->name('dashboard');

        // Module B — Lịch học
        Route::get('lich-hoc',                [\App\Http\Controllers\HocVien\LichHocController::class, 'index'])->name('lich_hoc.index');
        Route::get('lich-hoc/events',         [\App\Http\Controllers\HocVien\LichHocController::class, 'getEvents'])->name('lich_hoc.events');
        Route::get('lich-hoc/{id}',           [\App\Http\Controllers\HocVien\LichHocController::class, 'show'])->name('lich_hoc.show');

        // Module C — Kết quả học tập
        Route::get('ket-qua',                 [\App\Http\Controllers\HocVien\KetQuaController::class, 'index'])->name('ket_qua.index');
        Route::get('ket-qua/{lopId}',         [\App\Http\Controllers\HocVien\KetQuaController::class, 'chiTiet'])->name('ket_qua.chi_tiet');
        Route::get('ket-qua/{lopId}/in-pdf',  [\App\Http\Controllers\HocVien\KetQuaController::class, 'inBangDiem'])->name('ket_qua.pdf');

        // Module D — Điểm danh
        Route::get('diem-danh',               [\App\Http\Controllers\HocVien\DiemDanhController::class, 'index'])->name('diem_danh.index');
        Route::get('diem-danh/{lopId}',       [\App\Http\Controllers\HocVien\DiemDanhController::class, 'chiTiet'])->name('diem_danh.chi_tiet');

        // Module E — Đánh giá khóa học
        Route::get('danh-gia',                [\App\Http\Controllers\HocVien\DanhGiaController::class, 'index'])->name('danh_gia.index');
        Route::get('danh-gia/{lopId}/create', [\App\Http\Controllers\HocVien\DanhGiaController::class, 'create'])->name('danh_gia.create');
        Route::post('danh-gia',               [\App\Http\Controllers\HocVien\DanhGiaController::class, 'store'])->name('danh_gia.store');
        Route::get('danh-gia/{id}',           [\App\Http\Controllers\HocVien\DanhGiaController::class, 'show'])->name('danh_gia.show');

        // Module F — Khóa học của tôi
        Route::get('khoa-hoc',                [\App\Http\Controllers\HocVien\KhoaHocController::class, 'index'])->name('khoa_hoc.index');
        Route::get('khoa-hoc/{lopId}',        [\App\Http\Controllers\HocVien\KhoaHocController::class, 'show'])->name('khoa_hoc.show');

        // Module G — Hồ sơ cá nhân
        Route::get('ho-so',                   [\App\Http\Controllers\ProfileController::class, 'showHocVien'])->name('profile.show');
        Route::patch('ho-so',                 [\App\Http\Controllers\ProfileController::class, 'updateHocVien'])->name('profile.update_hv');
        Route::patch('ho-so/mat-khau',        [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // ===== PROFILE ROUTES =====
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

