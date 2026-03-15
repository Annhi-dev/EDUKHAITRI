<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LopHoc;
use App\Models\KhoaHoc;
use App\Models\BangDiem;
use App\Models\DiemDanh;
use App\Models\DanhGiaKhoaHoc;
use Illuminate\Support\Facades\DB;

class BaoCaoController extends Controller
{
    public function index(Request $request)
    {
        $namHoc = $request->nam_hoc ?? date('Y');
        $kyHoc = $request->ky_hoc ?? 1;

        $tongQuan = [
            'tong_giang_vien' => User::role('giang_vien')->where('is_active', true)->count(),
            'tong_hoc_vien'   => User::role('hoc_vien')->where('is_active', true)->count(),
            'tong_lop_hoc'    => LopHoc::where('trang_thai', 'dang_hoc')->count(),
            'tong_khoa_hoc'   => KhoaHoc::count(),
            'diem_tb_he_thong' => BangDiem::whereNotNull('diem_trung_binh')->avg('diem_trung_binh'),
            'tile_chuyen_can' => $this->calculateHeThongChuyenCan(),
            'so_danh_gia_thang_nay' => DanhGiaKhoaHoc::whereMonth('created_at', now()->month)->count(),
        ];

        // Dữ liệu biểu đồ (Mock hoặc Query thực tế)
        $bieuDoTheoThang = $this->getMonthlyStats($namHoc);
        $phanBoXepLoai = $this->getXepLoaiDistribution();

        return view('admin.bao_cao.index', compact('tongQuan', 'bieuDoTheoThang', 'phanBoXepLoai', 'namHoc', 'kyHoc'));
    }

    private function calculateHeThongChuyenCan()
    {
        $tongBuoi = DiemDanh::count();
        if ($tongBuoi == 0) return 0;
        
        $coMat = DiemDanh::whereIn('trang_thai', ['co_mat', 'di_muon', 've_som'])->count();
        return round(($coMat / $tongBuoi) * 100);
    }

    private function getMonthlyStats($year)
    {
        // Thống kê số lượng HV đăng ký theo từng tháng trong năm
        $stats = [];
        for ($m = 1; $m <= 12; $m++) {
            $stats[] = User::role('hoc_vien')->whereYear('created_at', $year)->whereMonth('created_at', $m)->count();
        }
        return $stats;
    }

    private function getXepLoaiDistribution()
    {
        return [
            'xuat_sac' => BangDiem::where('xep_loai', 'xuat_sac')->count(),
            'gioi' => BangDiem::where('xep_loai', 'gioi')->count(),
            'kha' => BangDiem::where('xep_loai', 'kha')->count(),
            'trung_binh' => BangDiem::where('xep_loai', 'trung_binh')->count(),
            'yeu' => BangDiem::where('xep_loai', 'yeu')->count(),
        ];
    }

    public function giangVien()
    {
        $giangViens = User::role('giang_vien')->with(['giangVienProfile'])->get();
        // Tính toán thêm point đánh giá...
        return view('admin.bao_cao.giang_vien', compact('giangViens'));
    }

    public function hocVien()
    {
        $hocViens = User::role('hoc_vien')->with(['hocVienProfile'])->paginate(20);
        return view('admin.bao_cao.hoc_vien', compact('hocViens'));
    }
}
