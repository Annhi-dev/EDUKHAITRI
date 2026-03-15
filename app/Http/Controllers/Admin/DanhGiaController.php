<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TieuChiDanhGia;
use App\Models\DanhGiaGiangVien;
use App\Models\DanhGiaKhoaHoc;
use App\Models\DanhGiaHocVien;
use App\Models\User;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DanhGiaController extends Controller
{
    public function index()
    {
        $thongKe = [
            'diem_tb_khoa_hoc' => DanhGiaKhoaHoc::avg('diem_trung_binh') ?? 0,
            'tong_danh_gia_thang' => DanhGiaKhoaHoc::whereMonth('created_at', now()->month)->count(),
            'gv_xuat_sac' => DanhGiaGiangVien::with('giangVien')->where('xep_loai', 'xuat_sac')->limit(5)->get(),
            'ty_le_hv_kha' => 0 // Tính sau
        ];

        return view('admin.danh_gia.index', compact('thongKe'));
    }

    public function tieuChi()
    {
        $tieuChis = TieuChiDanhGia::all()->groupBy('loai');
        return view('admin.danh_gia.tieu_chi', compact('tieuChis'));
    }

    public function storeTieuChi(Request $request)
    {
        $data = $request->validate([
            'ten_tieu_chi' => 'required|string|max:255',
            'loai' => 'required|in:giang_vien,khoa_hoc,hoc_vien',
            'trong_so' => 'required|integer|min:1|max:5',
            'mo_ta' => 'nullable|string',
        ]);

        TieuChiDanhGia::create($data);
        return redirect()->back()->with('success', 'Thêm tiêu chí thành công!');
    }

    public function destroyTieuChi($id)
    {
        TieuChiDanhGia::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Đã xóa tiêu chí.');
    }

    public function danhGiaGiangVien(Request $request)
    {
        $kyHoc = $request->get('ky_hoc', 1);
        $namHoc = $request->get('nam_hoc', date('Y'));

        $giangViens = User::role('giang_vien')->with(['giangVienProfile'])->get();
        
        $danhGias = DanhGiaGiangVien::where('ky_hoc', $kyHoc)
            ->where('nam_hoc', $namHoc)
            ->get()
            ->keyBy('giang_vien_id');

        return view('admin.danh_gia.giang_vien', compact('giangViens', 'danhGias', 'kyHoc', 'namHoc'));
    }

    public function taoOrCapNhatDanhGiaGV(Request $request, $giangVienId)
    {
        $request->validate([
            'ky_hoc' => 'required',
            'nam_hoc' => 'required',
            'diem_chuyen_mon' => 'required|numeric|min:0|max:10',
            'nhan_xet_admin' => 'nullable|string',
        ]);

        // Mock calculation for now
        $diem_tb_tu_hv = DanhGiaKhoaHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->avg('diem_trung_binh') ?? 0;

        $diem_tong = ($diem_tb_tu_hv * 2 + $request->diem_chuyen_mon * 0.7 + 10 * 0.3); // Weighted mock
        
        $xepLoai = 'trung_binh';
        if ($diem_tong >= 9) $xepLoai = 'xuat_sac';
        elseif ($diem_tong >= 8) $xepLoai = 'gioi';
        elseif ($diem_tong >= 6.5) $xepLoai = 'kha';
        elseif ($diem_tong < 5) $xepLoai = 'yeu';

        DanhGiaGiangVien::updateOrCreate(
            ['giang_vien_id' => $giangVienId, 'ky_hoc' => $request->ky_hoc, 'nam_hoc' => $request->nam_hoc],
            [
                'diem_tb_tu_hoc_vien' => $diem_tb_tu_hv,
                'diem_chuyen_mon' => $request->diem_chuyen_mon,
                'diem_chuyen_can' => 10,
                'diem_tong' => $diem_tong,
                'nhan_xet_admin' => $request->nhan_xet_admin,
                'xep_loai' => $xepLoai
            ]
        );

        return redirect()->back()->with('success', 'Đã lưu đánh giá giảng viên!');
    }

    public function danhGiaKhoaHoc()
    {
        $khoaHocs = KhoaHoc::withCount('lopHocs')->get();
        return view('admin.danh_gia.khoa_hoc', compact('khoaHocs'));
    }

    public function danhGiaHocVien()
    {
        $danhGias = DanhGiaHocVien::with(['hocVien', 'lopHoc', 'giangVien'])->paginate(15);
        return view('admin.danh_gia.hoc_vien', compact('danhGias'));
    }
}
