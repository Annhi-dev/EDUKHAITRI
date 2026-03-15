<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\LopHoc;
use App\Models\LichHoc;
use App\Models\YeuCauDoiLich;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $giangVienId = Auth::id();
        $today = today();

        // Lấy danh sách ID các lớp của giảng viên này
        $lopIds = LopHoc::where('giang_vien_id', $giangVienId)->pluck('id');

        // Lịch hôm nay
        $lichHomNay = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->whereDate('ngay_hoc', $today)
            ->with(['lopHoc.khoaHoc'])
            ->orderBy('gio_bat_dau')
            ->get();

        // Thống kê tổng quan
        $tongLop = LopHoc::where('giang_vien_id', $giangVienId)->where('trang_thai', 'dang_hoc')->count();
        
        $tongHocVien = DB::table('hoc_vien_lop_hocs')
            ->whereIn('lop_hoc_id', $lopIds)
            ->where('trang_thai', 'dang_hoc')
            ->count();

        $buoiChuaDiemDanh = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->where('trang_thai', 'da_len_lich')
            ->where('ngay_hoc', '<=', $today)
            ->count();

        $yeuCauChoDuyet = YeuCauDoiLich::where('giang_vien_id', $giangVienId)
            ->where('trang_thai', 'cho_duyet')
            ->count();

        // Lịch tuần này
        $lichTuanNay = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->whereBetween('ngay_hoc', [now()->startOfWeek(), now()->endOfWeek()])
            ->with(['lopHoc.khoaHoc'])
            ->orderBy('ngay_hoc')
            ->orderBy('gio_bat_dau')
            ->get();

        return view('giang_vien.dashboard', compact(
            'lichHomNay', 
            'tongLop', 
            'tongHocVien', 
            'buoiChuaDiemDanh', 
            'yeuCauChoDuyet', 
            'lichTuanNay'
        ));
    }
}
