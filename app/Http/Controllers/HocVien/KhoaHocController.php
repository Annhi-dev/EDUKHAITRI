<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\HocVienLopHoc;
use App\Models\LichHoc;
use App\Models\BangDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KhoaHocController extends Controller
{
    public function index()
    {
        $hocVienId = Auth::id();
        
        $lopHocs = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])
            ->get();

        // Tính tiến độ cho từng lớp
        foreach ($lopHocs as $item) {
            $tongBuoi = LichHoc::where('lop_hoc_id', $item->lop_hoc_id)->count();
            $daDay = LichHoc::where('lop_hoc_id', $item->lop_hoc_id)->where('trang_thai', 'hoan_thanh')->count();
            $item->tien_do = $tongBuoi > 0 ? round(($daDay / $tongBuoi) * 100) : 0;
            $item->so_buoi_da_day = $daDay;
            $item->tong_so_buoi = $tongBuoi;
        }

        return view('hoc_vien.khoa_hoc.index', compact('lopHocs'));
    }

    public function show($lopId)
    {
        $hocVienId = Auth::id();
        
        // Kiểm tra quyền truy cập
        $hocVienLop = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('lop_hoc_id', $lopId)
            ->firstOrFail();

        $lopHoc = LopHoc::with(['khoaHoc', 'giangVien.giangVienProfile'])->findOrFail($lopId);
        
        $bangDiem = BangDiem::where('hoc_vien_id', $hocVienId)
            ->where('lop_hoc_id', $lopId)
            ->first();

        $tongBuoi = LichHoc::where('lop_hoc_id', $lopId)->count();
        $daDay = LichHoc::where('lop_hoc_id', $lopId)->where('trang_thai', 'hoan_thanh')->count();
        $tienDo = $tongBuoi > 0 ? round(($daDay / $tongBuoi) * 100) : 0;

        return view('hoc_vien.khoa_hoc.show', compact('lopHoc', 'bangDiem', 'tienDo', 'daDay', 'tongBuoi'));
    }
}
