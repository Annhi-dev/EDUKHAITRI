<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\User;
use App\Models\TieuChiDanhGia;
use App\Models\DanhGiaHocVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DanhGiaHocVienController extends Controller
{
    public function index()
    {
        $giangVienId = Auth::id();
        $lopHocs = LopHoc::where('giang_vien_id', $giangVienId)
            ->with(['khoaHoc', 'hocViens'])
            ->paginate(10);

        return view('giang_vien.danh_gia.index', compact('lopHocs'));
    }

    public function create($lopId)
    {
        $giangVienId = Auth::id();
        $lopHoc = LopHoc::where('giang_vien_id', $giangVienId)->findOrFail($lopId);
        
        $hocViens = $lopHoc->hocViens;
        $tieuChis = TieuChiDanhGia::where('loai', 'hoc_vien')->where('is_active', true)->get();

        return view('giang_vien.danh_gia.create', compact('lopHoc', 'hocViens', 'tieuChis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'ky_hoc' => 'required|in:1,2',
            'nam_hoc' => 'required|integer',
            'danh_gias' => 'required|array',
        ]);

        $giangVienId = Auth::id();

        DB::transaction(function () use ($request, $giangVienId) {
            foreach ($request->danh_gias as $hvId => $data) {
                // Tính điểm trung bình dựa trên trọng số
                $tongDiem = 0;
                $tongTrongSo = 0;
                foreach ($data['chi_tiet'] as $tcId => $diem) {
                    $tc = TieuChiDanhGia::find($tcId);
                    $tongDiem += ($diem * $tc->trong_so);
                    $tongTrongSo += $tc->trong_so;
                }
                $dtb = $tongTrongSo > 0 ? ($tongDiem / $tongTrongSo) : 0;

                // Xếp loại
                $xepLoai = 'trung_binh';
                if ($dtb >= 9) $xepLoai = 'xuat_sac';
                elseif ($dtb >= 8) $xepLoai = 'gioi';
                elseif ($dtb >= 6.5) $xepLoai = 'kha';
                elseif ($dtb < 5) $xepLoai = 'yeu';

                DanhGiaHocVien::updateOrCreate(
                    [
                        'hoc_vien_id' => $hvId, 
                        'lop_hoc_id' => $request->lop_hoc_id, 
                        'ky_hoc' => $request->ky_hoc, 
                        'nam_hoc' => $request->nam_hoc
                    ],
                    [
                        'giang_vien_id' => $giangVienId,
                        'chi_tiet_danh_gia' => $data['chi_tiet'],
                        'diem_trung_binh' => $dtb,
                        'nhan_xet' => $data['nhan_xet'] ?? null,
                        'xep_loai' => $xepLoai,
                    ]
                );
            }
        });

        return redirect()->route('gv.danh_gia.index')->with('success', 'Đã lưu đánh giá học viên!');
    }

    public function show($id)
    {
        $danhGia = DanhGiaHocVien::with(['hocVien', 'lopHoc', 'giangVien'])->findOrFail($id);
        $tieuChis = TieuChiDanhGia::where('loai', 'hoc_vien')->where('is_active', true)->get();
        return view('giang_vien.danh_gia.show', compact('danhGia', 'tieuChis'));
    }

    public function edit($id)
    {
        $danhGia = DanhGiaHocVien::with(['hocVien', 'lopHoc'])->findOrFail($id);
        $tieuChis = TieuChiDanhGia::where('loai', 'hoc_vien')->where('is_active', true)->get();
        return view('giang_vien.danh_gia.edit', compact('danhGia', 'tieuChis'));
    }

    public function update(Request $request, $id)
    {
        $danhGia = DanhGiaHocVien::findOrFail($id);
        
        $request->validate([
            'chi_tiet' => 'required|array',
            'nhan_xet' => 'nullable|string|max:500',
        ]);

        // Tính lại điểm trung bình dựa trên trọng số
        $tongDiem = 0;
        $tongTrongSo = 0;
        foreach ($request->chi_tiet as $tcId => $diem) {
            $tc = TieuChiDanhGia::find($tcId);
            $tongDiem += ($diem * $tc->trong_so);
            $tongTrongSo += $tc->trong_so;
        }
        $dtb = $tongTrongSo > 0 ? ($tongDiem / $tongTrongSo) : 0;

        // Xếp loại
        $xepLoai = 'trung_binh';
        if ($dtb >= 9) $xepLoai = 'xuat_sac';
        elseif ($dtb >= 8) $xepLoai = 'gioi';
        elseif ($dtb >= 6.5) $xepLoai = 'kha';
        elseif ($dtb < 5) $xepLoai = 'yeu';

        $danhGia->update([
            'chi_tiet_danh_gia' => $request->chi_tiet,
            'diem_trung_binh' => $dtb,
            'nhan_xet' => $request->nhan_xet,
            'xep_loai' => $xepLoai,
        ]);

        return redirect()->route('gv.danh_gia.index')->with('success', 'Đã cập nhật đánh giá!');
    }
}
