<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YeuCauDoiLich;
use App\Models\LichHoc;
use App\Services\ThongBaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YeuCauDoiLichController extends Controller
{
    protected $thongBaoService;

    public function __construct(ThongBaoService $thongBaoService)
    {
        $this->thongBaoService = $thongBaoService;
    }
    public function index(Request $request)
    {
        $query = YeuCauDoiLich::with(['lichHoc.lopHoc', 'giangVien']);

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        } else {
            $query->where('trang_thai', 'cho_duyet');
        }

        $yeuCaus = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.yeu_cau_doi_lich.index', compact('yeuCaus'));
    }

    public function duyet($id)
    {
        $yeuCau = YeuCauDoiLich::findOrFail($id);

        DB::transaction(function () use ($yeuCau) {
            $lichHoc = $yeuCau->lichHoc;
            
            // Cập nhật lịch học gốc
            $lichHoc->update([
                'ngay_hoc' => $yeuCau->ngay_muon_doi,
                'gio_bat_dau' => $yeuCau->gio_bat_dau_moi,
                'gio_ket_thuc' => $yeuCau->gio_ket_thuc_moi,
                'phong_hoc' => $yeuCau->phong_hoc_moi,
                'trang_thai' => 'doi_lich',
                'ghi_chu' => 'Đã đổi từ lịch cũ. Lý do: ' . $yeuCau->ly_do,
            ]);

            // Cập nhật trạng thái yêu cầu
            $yeuCau->update(['trang_thai' => 'da_duyet']);

            // Gửi thông báo cho giảng viên
            $this->thongBaoService->gui([
                'tieu_de'  => 'Yêu cầu đổi lịch đã được duyệt',
                'noi_dung' => 'Yêu cầu đổi lịch lớp ' . $yeuCau->lichHoc->lopHoc->ten_lop . ' sang ngày ' . \Carbon\Carbon::parse($yeuCau->ngay_muon_doi)->format('d/m/Y') . ' đã được Admin duyệt.',
                'loai'     => 'yeu_cau_doi_lich',
                'muc_do'   => 'success',
                'url'      => route('gv.lich_day.index'),
                'icon'     => 'calendar-check',
            ], $yeuCau->giang_vien_id);
        });

        return redirect()->back()->with('success', 'Đã duyệt yêu cầu đổi lịch.');
    }

    public function tuChoi(Request $request, $id)
    {
        $yeuCau = YeuCauDoiLich::with('lichHoc.lopHoc')->findOrFail($id);
        
        $yeuCau->update([
            'trang_thai' => 'tu_choi',
            'ghi_chu_admin' => $request->ghi_chu_admin,
        ]);

        // Gửi thông báo cho giảng viên
        $this->thongBaoService->gui([
            'tieu_de'  => 'Yêu cầu đổi lịch bị từ chối',
            'noi_dung' => 'Yêu cầu đổi lịch lớp ' . $yeuCau->lichHoc->lopHoc->ten_lop . ' bị từ chối. Lý do: ' . $request->ghi_chu_admin,
            'loai'     => 'yeu_cau_doi_lich',
            'muc_do'   => 'danger',
            'url'      => route('gv.yeu_cau.index'),
            'icon'     => 'x-circle',
        ], $yeuCau->giang_vien_id);

        return redirect()->back()->with('success', 'Đã từ chối yêu cầu đổi lịch.');
    }
}
