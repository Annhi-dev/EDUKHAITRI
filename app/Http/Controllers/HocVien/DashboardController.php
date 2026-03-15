<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LichHoc;
use App\Models\HocVienLopHoc;
use App\Models\BangDiem;
use App\Models\DiemDanh;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hocVienId = Auth::id();
        $today = today();

        // Lấy danh sách ID các lớp học viên đang tham gia (trạng thái dang_hoc)
        $lopIds = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', 'dang_hoc')
            ->pluck('lop_hoc_id');

        // Lịch hôm nay
        $lichHomNay = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->whereDate('ngay_hoc', $today)
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])
            ->orderBy('gio_bat_dau')
            ->get();

        // Thống kê học tập
        $lopDangHocCount = $lopIds->count();
        
        $diemTrungBinh = BangDiem::where('hoc_vien_id', $hocVienId)
            ->whereNotNull('diem_trung_binh')
            ->avg('diem_trung_binh');

        // Tính tỷ lệ chuyên cần (Có mặt / Tổng buổi đã dạy của các lớp đang học)
        $tongBuoiDaDạy = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->where('trang_thai', 'hoan_thanh')
            ->count();
        
        $soBuoiCoMat = DiemDanh::where('hoc_vien_id', $hocVienId)
            ->whereIn('trang_thai', ['co_mat', 'di_muon', 've_som'])
            ->whereHas('lichHoc', fn($q) => $q->whereIn('lop_hoc_id', $lopIds))
            ->count();
            
        $tileChuyenCan = $tongBuoiDaDạy > 0 ? round(($soBuoiCoMat / $tongBuoiDaDạy) * 100) : 0;

        // Số lớp đã hoàn thành nhưng chưa đánh giá khóa học
        $chuaDanhGiaCount = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', 'da_hoan_thanh')
            ->whereNotExists(function ($query) use ($hocVienId) {
                $query->select(DB::raw(1))
                    ->from('danh_gia_khoa_hocs')
                    ->whereRaw('danh_gia_khoa_hocs.lop_hoc_id = hoc_vien_lop_hocs.lop_hoc_id')
                    ->where('danh_gia_khoa_hocs.hoc_vien_id', $hocVienId);
            })->count();

        // Lịch 7 ngày tới (Lịch tuần)
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $lichTuanNay = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->whereBetween('ngay_hoc', [$startOfWeek, $endOfWeek])
            ->with(['lopHoc.khoaHoc'])
            ->orderBy('ngay_hoc')
            ->orderBy('gio_bat_dau')
            ->get();

        // Kết quả học tập gần đây (5 lớp có điểm gần nhất)
        $ketQuaGanDay = BangDiem::where('hoc_vien_id', $hocVienId)
            ->with(['lopHoc.khoaHoc'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('hoc_vien.dashboard', compact(
            'lichHomNay',
            'lichTuanNay',
            'lopDangHocCount',
            'diemTrungBinh',
            'tileChuyenCan',
            'chuaDanhGiaCount',
            'ketQuaGanDay'
        ));
    }
}
