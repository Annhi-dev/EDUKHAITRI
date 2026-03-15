<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LichHoc;
use App\Models\DiemDanh;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiemDanhController extends Controller
{
    public function index(Request $request)
    {
        $giangVienId = Auth::id();
        $query = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->with(['lopHoc', 'diemDanhs']);

        if ($request->filled('lop_hoc_id')) {
            $query->where('lop_hoc_id', $request->lop_hoc_id);
        }

        $lichHocs = $query->orderBy('ngay_hoc', 'desc')->paginate(15);
        $lopHocs = LopHoc::where('giang_vien_id', $giangVienId)->get();

        return view('giang_vien.diem_danh.index', compact('lichHocs', 'lopHocs'));
    }

    public function create(Request $request)
    {
        $lichHocId = $request->lich_hoc_id;
        $giangVienId = Auth::id();

        $lichHoc = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->with(['lopHoc.hocViens'])->findOrFail($lichHocId);

        $hocViens = $lichHoc->lopHoc->hocViens;
        $diemDanhCu = DiemDanh::where('lich_hoc_id', $lichHocId)->get()->keyBy('hoc_vien_id');

        return view('giang_vien.diem_danh.create', compact('lichHoc', 'hocViens', 'diemDanhCu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lich_hoc_id' => 'required|exists:lich_hocs,id',
            'diem_danhs' => 'required|array',
            'diem_danhs.*.hoc_vien_id' => 'required|exists:users,id',
            'diem_danhs.*.trang_thai' => 'required|in:co_mat,vang_co_phep,vang_khong_phep,di_muon,ve_som',
        ]);

        $lichHocId = $request->lich_hoc_id;
        $giangVienId = Auth::id();

        DB::transaction(function () use ($request, $lichHocId, $giangVienId) {
            foreach ($request->diem_danhs as $data) {
                DiemDanh::updateOrCreate(
                    ['lich_hoc_id' => $lichHocId, 'hoc_vien_id' => $data['hoc_vien_id']],
                    [
                        'trang_thai' => $data['trang_thai'],
                        'gio_den' => $data['gio_den'] ?? null,
                        'ghi_chu' => $data['ghi_chu'] ?? null,
                        'giang_vien_id' => $giangVienId,
                        'thoi_gian_diem_danh' => now(),
                    ]
                );
            }

            // Cập nhật trạng thái buổi học
            LichHoc::where('id', $lichHocId)->update(['trang_thai' => 'hoan_thanh']);
        });

        return redirect()->route('gv.diem_danh.index')->with('success', 'Điểm danh thành công!');
    }

    public function show($lichHocId)
    {
        $giangVienId = Auth::id();
        $lichHoc = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->with(['lopHoc', 'diemDanhs.hocVien'])->findOrFail($lichHocId);

        $diemDanhs = $lichHoc->diemDanhs;
        
        $thongKe = [
            'co_mat' => $diemDanhs->where('trang_thai', 'co_mat')->count(),
            'vang_co_phep' => $diemDanhs->where('trang_thai', 'vang_co_phep')->count(),
            'vang_khong_phep' => $diemDanhs->where('trang_thai', 'vang_khong_phep')->count(),
            'di_muon' => $diemDanhs->where('trang_thai', 'di_muon')->count(),
            've_som' => $diemDanhs->where('trang_thai', 've_som')->count(),
        ];

        return view('giang_vien.diem_danh.show', compact('lichHoc', 'diemDanhs', 'thongKe'));
    }

    public function edit($lichHocId)
    {
        $giangVienId = Auth::id();
        $lichHoc = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->with(['lopHoc.hocViens'])->findOrFail($lichHocId);

        // Cho phép chỉnh sửa trong vòng 24h sau khi buổi học bắt đầu? Hoặc chỉ đơn giản là cho sửa.
        // Theo plan: "trong vòng 24h"
        /*
        $ngayHoc = Carbon::parse($lichHoc->ngay_hoc . ' ' . $lichHoc->gio_bat_dau);
        if ($ngayHoc->diffInHours(now()) > 24) {
            return redirect()->back()->with('error', 'Đã quá 24h, không thể chỉnh sửa điểm danh.');
        }
        */

        $hocViens = $lichHoc->lopHoc->hocViens;
        $diemDanhCu = DiemDanh::where('lich_hoc_id', $lichHocId)->get()->keyBy('hoc_vien_id');

        return view('giang_vien.diem_danh.create', [
            'lichHoc' => $lichHoc,
            'hocViens' => $hocViens,
            'diemDanhCu' => $diemDanhCu,
            'isEdit' => true
        ]);
    }

    public function thongKe(Request $request)
    {
        $giangVienId = Auth::id();
        $lopIds = LopHoc::where('giang_vien_id', $giangVienId)->pluck('id');

        $query = LopHoc::where('giang_vien_id', $giangVienId);
        if ($request->filled('lop_hoc_id')) {
            $query->where('id', $request->lop_hoc_id);
        }
        $lopHocs = $query->with(['hocViens', 'lichHocs' => function($q) {
            $q->where('trang_thai', 'hoan_thanh')->orderBy('ngay_hoc');
        }, 'lichHocs.diemDanhs'])->get();

        return view('giang_vien.diem_danh.thong_ke', compact('lopHocs'));
    }
}
