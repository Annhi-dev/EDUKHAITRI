<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\BangDiem;
use App\Models\DiemDanh;
use App\Models\LichHoc;
use App\Services\ThongBaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiemController extends Controller
{
    protected $thongBaoService;

    public function __construct(ThongBaoService $thongBaoService)
    {
        $this->thongBaoService = $thongBaoService;
    }
    public function index()
    {
        $giangVienId = Auth::id();
        $lopHocs = LopHoc::where('giang_vien_id', $giangVienId)
            ->with(['khoaHoc', 'hocViens'])
            ->paginate(10);

        return view('giang_vien.diem.index', compact('lopHocs'));
    }

    public function bangDiem($lopId)
    {
        $giangVienId = Auth::id();
        $lopHoc = LopHoc::where('giang_vien_id', $giangVienId)->with('khoaHoc')->findOrFail($lopId);
        
        $hocViens = $lopHoc->hocViens;
        $bangDiems = BangDiem::where('lop_hoc_id', $lopId)->get()->keyBy('hoc_vien_id');

        // Tính điểm chuyên cần cho từng học viên nếu chưa có hoặc muốn cập nhật
        $tongBuoiDaDay = LichHoc::where('lop_hoc_id', $lopId)->where('trang_thai', 'hoan_thanh')->count();

        foreach ($hocViens as $hv) {
            if ($tongBuoiDaDay > 0) {
                $soBuoiCoMat = DiemDanh::where('hoc_vien_id', $hv->id)
                    ->whereHas('lichHoc', fn($q) => $q->where('lop_hoc_id', $lopId))
                    ->where('trang_thai', 'co_mat')
                    ->count();
                $hv->diem_cc_tu_dong = round(($soBuoiCoMat / $tongBuoiDaDay) * 10, 2);
            } else {
                $hv->diem_cc_tu_dong = 10;
            }
        }

        return view('giang_vien.diem.bang_diem', compact('lopHoc', 'hocViens', 'bangDiems'));
    }

    public function nhapDiem(Request $request, $lopId)
    {
        $giangVienId = Auth::id();
        $lopHoc = LopHoc::where('giang_vien_id', $giangVienId)->findOrFail($lopId);

        $request->validate([
            'diem_data' => 'required|array',
            'diem_data.*.hoc_vien_id' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($request, $lopId, $giangVienId, $lopHoc) {
            $hocVienIds = [];
            foreach ($request->diem_data as $data) {
                BangDiem::updateOrCreate(
                    ['lop_hoc_id' => $lopId, 'hoc_vien_id' => $data['hoc_vien_id']],
                    [
                        'giang_vien_id' => $giangVienId,
                        'diem_chuyen_can' => $data['diem_chuyen_can'] ?? null,
                        'diem_kiem_tra_1' => $data['diem_kiem_tra_1'] ?? null,
                        'diem_kiem_tra_2' => $data['diem_kiem_tra_2'] ?? null,
                        'diem_giua_ky' => $data['diem_giua_ky'] ?? null,
                        'diem_cuoi_ky' => $data['diem_cuoi_ky'] ?? null,
                        'ghi_chu' => $data['ghi_chu'] ?? null,
                    ]
                );
                $hocVienIds[] = $data['hoc_vien_id'];
            }

            // Gửi thông báo cho toàn bộ học viên trong lớp
            $this->thongBaoService->gui([
                'tieu_de'  => 'Điểm số đã được cập nhật',
                'noi_dung' => 'Giảng viên vừa cập nhật bảng điểm cho lớp ' . $lopHoc->ten_lop . '. Hãy kiểm tra kết quả học tập của bạn!',
                'loai'     => 'diem_so',
                'muc_do'   => 'info',
                'url'      => route('hv.ket_qua.chi_tiet', $lopId),
                'icon'     => 'academic-cap',
            ], $hocVienIds);
        });

        return redirect()->back()->with('success', 'Đã cập nhật bảng điểm!');
    }

    public function khoaDiem($lopId)
    {
        $giangVienId = Auth::id();
        $lopHoc = LopHoc::where('giang_vien_id', $giangVienId)->findOrFail($lopId);
        
        BangDiem::where('lop_hoc_id', $lopId)->update(['da_khoa' => true]);

        return redirect()->back()->with('success', 'Đã khóa bảng điểm!');
    }

    public function moKhoaDiem($lopId)
    {
        $giangVienId = Auth::id();
        $lopHoc = LopHoc::where('giang_vien_id', $giangVienId)->findOrFail($lopId);
        
        BangDiem::where('lop_hoc_id', $lopId)->update(['da_khoa' => false]);

        return redirect()->back()->with('success', 'Đã mở khóa bảng điểm!');
    }

    public function nhapDiemAjax(Request $request)
    {
        $giangVienId = Auth::id();
        
        $request->validate([
            'hoc_vien_id' => 'required|exists:users,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'loai_diem' => 'required|string',
            'gia_tri' => 'nullable|numeric|min:0|max:10',
        ]);

        $bangDiem = BangDiem::updateOrCreate(
            ['lop_hoc_id' => $request->lop_hoc_id, 'hoc_vien_id' => $request->hoc_vien_id],
            [
                'giang_vien_id' => $giangVienId,
                $request->loai_diem => $request->gia_tri
            ]
        );

        // Tính lại điểm trung bình và xếp loại (logic này nên ở Model hoặc Service)
        // Cho đơn giản ở đây:
        $dtb = $bangDiem->calculateDiemTB();
        $bangDiem->diem_trung_binh = $dtb;
        
        $xepLoai = $bangDiem->calculateXepLoai($dtb);
        $bangDiem->xep_loai = $xepLoai;
        $bangDiem->save();

        return response()->json([
            'diem_trung_binh' => round($dtb, 2),
            'xep_loai' => $xepLoai,
            'xep_loai_text' => ucfirst(str_replace('_', ' ', $xepLoai))
        ]);
    }
}
