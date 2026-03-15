<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\LichHoc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LopHocController extends Controller
{
    public function index()
    {
        $giangVienId = Auth::id();
        $lopHocs = LopHoc::where('giang_vien_id', $giangVienId)
            ->with(['khoaHoc', 'hocViens'])
            ->withCount(['lichHocs as so_buoi_da_day' => function($q) {
                $q->where('trang_thai', 'hoan_thanh');
            }])
            ->withCount(['lichHocs as so_buoi_con_lai' => function($q) {
                $q->where('trang_thai', 'da_len_lich');
            }])
            ->withCount(['lichHocs as tong_so_buoi'])
            ->paginate(10);

        return view('giang_vien.lop_hoc.index', compact('lopHocs'));
    }

    public function show($id)
    {
        $giangVienId = Auth::id();
        $lopHoc = LopHoc::where('giang_vien_id', $giangVienId)
            ->with(['khoaHoc', 'giangVien', 'hocViens.hocVienProfile'])
            ->findOrFail($id);

        $lichHocsSapToi = LichHoc::where('lop_hoc_id', $id)
            ->where('ngay_hoc', '>=', now())
            ->orderBy('ngay_hoc', 'asc')
            ->limit(5)
            ->get();

        $lichHocsDaQua = LichHoc::where('lop_hoc_id', $id)
            ->where('ngay_hoc', '<', now())
            ->orderBy('ngay_hoc', 'desc')
            ->limit(5)
            ->get();

        // Thống kê sơ bộ
        $thongKe = [
            'so_hv_dang_hoc' => $lopHoc->hocViens()->wherePivot('trang_thai', 'dang_hoc')->count(),
            'so_buoi_hoan_thanh' => LichHoc::where('lop_hoc_id', $id)->where('trang_thai', 'hoan_thanh')->count(),
            'tong_so_buoi' => LichHoc::where('lop_hoc_id', $id)->count(),
        ];

        return view('giang_vien.lop_hoc.show', compact('lopHoc', 'lichHocsSapToi', 'lichHocsDaQua', 'thongKe'));
    }

    public function danhSachHocVien($id)
    {
        $giangVienId = Auth::id();
        $lopHoc = LopHoc::where('giang_vien_id', $giangVienId)->findOrFail($id);
        
        $hocViens = $lopHoc->hocViens()
            ->with(['hocVienProfile'])
            ->get();

        return view('giang_vien.lop_hoc.danh_sach_hv', compact('lopHoc', 'hocViens'));
    }
}
