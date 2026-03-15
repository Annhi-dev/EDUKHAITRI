<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use App\Models\LopHoc;
use App\Models\DiemDanh;
use App\Models\DanhGiaHocVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class KetQuaController extends Controller
{
    public function index()
    {
        $hocVienId = Auth::id();
        
        $bangDiems = BangDiem::where('hoc_vien_id', $hocVienId)
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])
            ->get();

        $diemTBToanKhoa = $bangDiems->whereNotNull('diem_trung_binh')->avg('diem_trung_binh');
        
        $xepLoaiChung = 'Chưa xếp loại';
        if ($diemTBToanKhoa >= 9) $xepLoaiChung = 'Xuất sắc';
        elseif ($diemTBToanKhoa >= 8) $xepLoaiChung = 'Giỏi';
        elseif ($diemTBToanKhoa >= 6.5) $xepLoaiChung = 'Khá';
        elseif ($diemTBToanKhoa >= 5) $xepLoaiChung = 'Trung bình';
        elseif ($diemTBToanKhoa > 0) $xepLoaiChung = 'Yếu';

        $lopDaHoanThanh = $bangDiems->whereNotNull('diem_cuoi_ky')->count();
        $lopDangHoc = $bangDiems->whereNull('diem_cuoi_ky')->count();

        return view('hoc_vien.ket_qua.index', compact(
            'bangDiems',
            'diemTBToanKhoa',
            'xepLoaiChung',
            'lopDaHoanThanh',
            'lopDangHoc'
        ));
    }

    public function chiTiet($lopId)
    {
        $hocVienId = Auth::id();
        
        $bangDiem = BangDiem::where('hoc_vien_id', $hocVienId)
            ->where('lop_hoc_id', $lopId)
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien.giangVienProfile'])
            ->firstOrFail();

        $diemDanhs = DiemDanh::where('hoc_vien_id', $hocVienId)
            ->whereHas('lichHoc', fn($q) => $q->where('lop_hoc_id', $lopId))
            ->get();

        $tongBuoi = $diemDanhs->count();
        $coMat = $diemDanhs->whereIn('trang_thai', ['co_mat', 'di_muon', 've_som'])->count();
        $tileChuyenCan = $tongBuoi > 0 ? round(($coMat / $tongBuoi) * 100) : 0;

        $danhGia = DanhGiaHocVien::where('hoc_vien_id', $hocVienId)
            ->where('lop_hoc_id', $lopId)
            ->first();

        return view('hoc_vien.ket_qua.chi_tiet', compact(
            'bangDiem',
            'tileChuyenCan',
            'diemDanhs',
            'danhGia'
        ));
    }

    public function inBangDiem($lopId)
    {
        $hocVienId = Auth::id();
        $bangDiem = BangDiem::where('hoc_vien_id', $hocVienId)
            ->where('lop_hoc_id', $lopId)
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien', 'hocVien.hocVienProfile'])
            ->firstOrFail();

        $pdf = Pdf::loadView('hoc_vien.ket_qua.pdf', compact('bangDiem'));
        
        $fileName = "BangDiem_" . ($bangDiem->hocVien->hocVienProfile->ma_hoc_vien ?? 'HV') . "_" . $bangDiem->lopHoc->ma_lop . ".pdf";
        return $pdf->download($fileName);
    }
}
