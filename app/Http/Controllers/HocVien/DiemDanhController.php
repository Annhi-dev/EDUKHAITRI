<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\LichHoc;
use App\Models\DiemDanh;
use App\Models\HocVienLopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiemDanhController extends Controller
{
    public function index()
    {
        $hocVienId = Auth::id();
        
        $lopIds = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', 'dang_hoc')
            ->pluck('lop_hoc_id');

        $lopHocs = LopHoc::whereIn('id', $lopIds)
            ->with(['khoaHoc', 'giangVien'])
            ->get();

        $thongKes = [];
        foreach ($lopHocs as $lop) {
            $tongBuoiDaDạy = LichHoc::where('lop_hoc_id', $lop->id)
                ->where('trang_thai', 'hoan_thanh')
                ->count();

            $diemDanhs = DiemDanh::where('hoc_vien_id', $hocVienId)
                ->whereHas('lichHoc', fn($q) => $q->where('lop_hoc_id', $lop->id))
                ->get();

            $coMat = $diemDanhs->where('trang_thai', 'co_mat')->count();
            $muon = $diemDanhs->where('trang_thai', 'di_muon')->count();
            $veSom = $diemDanhs->where('trang_thai', 've_som')->count();
            $vangCP = $diemDanhs->where('trang_thai', 'vang_co_phep')->count();
            $vangKP = $diemDanhs->where('trang_thai', 'vang_khong_phep')->count();

            $tile = $tongBuoiDaDạy > 0 ? round((($coMat + $muon + $veSom) / $tongBuoiDaDạy) * 100) : 100;

            $thongKes[$lop->id] = [
                'tong_buoi' => $tongBuoiDaDạy,
                'co_mat' => $coMat,
                'muon' => $muon,
                've_som' => $veSom,
                'vang_cp' => $vangCP,
                'vang_kp' => $vangKP,
                'tile' => $tile,
                'con_lai' => LichHoc::where('lop_hoc_id', $lop->id)->where('trang_thai', 'da_len_lich')->count()
            ];
        }

        return view('hoc_vien.diem_danh.index', compact('lopHocs', 'thongKes'));
    }

    public function chiTiet($lopId)
    {
        $hocVienId = Auth::id();
        $lopHoc = LopHoc::with(['khoaHoc', 'giangVien'])->findOrFail($lopId);

        $lichHocs = LichHoc::where('lop_hoc_id', $lopId)
            ->orderBy('ngay_hoc', 'asc')
            ->get();

        $diemDanhs = DiemDanh::where('hoc_vien_id', $hocVienId)
            ->whereHas('lichHoc', fn($q) => $q->where('lop_hoc_id', $lopId))
            ->get()
            ->keyBy('lich_hoc_id');

        return view('hoc_vien.diem_danh.chi_tiet', compact('lopHoc', 'lichHocs', 'diemDanhs'));
    }
}
