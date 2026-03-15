<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\HocVienLopHoc;
use App\Models\DanhGiaKhoaHoc;
use App\Models\TieuChiDanhGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DanhGiaController extends Controller
{
    public function index()
    {
        $hocVienId = Auth::id();
        
        // Lớp đã hoàn thành nhưng chưa đánh giá
        $chuaDanhGia = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', 'da_hoan_thanh')
            ->whereNotExists(function ($query) use ($hocVienId) {
                $query->select(DB::raw(1))
                    ->from('danh_gia_khoa_hocs')
                    ->whereRaw('danh_gia_khoa_hocs.lop_hoc_id = hoc_vien_lop_hocs.lop_hoc_id')
                    ->where('danh_gia_khoa_hocs.hoc_vien_id', $hocVienId);
            })
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])
            ->get();

        // Đánh giá đã gửi
        $daDanhGia = DanhGiaKhoaHoc::where('hoc_vien_id', $hocVienId)
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hoc_vien.danh_gia.index', compact('chuaDanhGia', 'daDanhGia'));
    }

    public function create($lopId)
    {
        $hocVienId = Auth::id();
        
        // Kiểm tra xem đã đánh giá chưa
        $exists = DanhGiaKhoaHoc::where('hoc_vien_id', $hocVienId)->where('lop_hoc_id', $lopId)->exists();
        if ($exists) {
            return redirect()->route('hv.danh_gia.index')->with('error', 'Bạn đã đánh giá khóa học này rồi.');
        }

        $lopHoc = LopHoc::with(['khoaHoc', 'giangVien'])->findOrFail($lopId);
        $tieuChis = TieuChiDanhGia::where('loai', 'khoa_hoc')->where('is_active', true)->get();

        return view('hoc_vien.danh_gia.create', compact('lopHoc', 'tieuChis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'diem_noi_dung' => 'required|integer|min:1|max:5',
            'diem_giang_vien' => 'required|integer|min:1|max:5',
            'diem_co_so_vat_chat' => 'required|integer|min:1|max:5',
            'chi_tiet_danh_gia' => 'required|array',
            'gop_y' => 'nullable|string|max:1000',
        ]);

        $hocVienId = Auth::id();

        // Tính điểm trung bình từ chi tiết
        $tongDiem = array_sum($request->chi_tiet_danh_gia);
        $count = count($request->chi_tiet_danh_gia);
        $diemTB = $count > 0 ? ($tongDiem / $count) : 0;

        DanhGiaKhoaHoc::create([
            'hoc_vien_id' => $hocVienId,
            'lop_hoc_id' => $request->lop_hoc_id,
            'diem_noi_dung' => $request->diem_noi_dung,
            'diem_giang_vien' => $request->diem_giang_vien,
            'diem_co_so_vat_chat' => $request->diem_co_so_vat_chat,
            'chi_tiet_danh_gia' => $request->chi_tiet_danh_gia,
            'diem_trung_binh' => $diemTB,
            'nhan_xet' => $request->gop_y,
            'an_danh' => $request->has('an_danh'),
        ]);

        return redirect()->route('hv.danh_gia.index')->with('success', 'Cảm ơn bạn đã gửi đánh giá! Ý kiến của bạn rất quan trọng với chúng tôi.');
    }

    public function show($id)
    {
        $danhGia = DanhGiaKhoaHoc::with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])->findOrFail($id);
        $tieuChis = TieuChiDanhGia::where('loai', 'khoa_hoc')->where('is_active', true)->get();
        
        return view('hoc_vien.danh_gia.show', compact('danhGia', 'tieuChis'));
    }
}
