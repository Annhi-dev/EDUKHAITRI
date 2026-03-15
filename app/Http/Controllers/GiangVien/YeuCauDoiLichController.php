<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\YeuCauDoiLich;
use App\Models\LichHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YeuCauDoiLichController extends Controller
{
    public function index(Request $request)
    {
        $giangVienId = Auth::id();
        $query = YeuCauDoiLich::where('giang_vien_id', $giangVienId)->with(['lichHoc.lopHoc']);

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $yeuCaus = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('giang_vien.yeu_cau_doi_lich.index', compact('yeuCaus'));
    }

    public function create(Request $request)
    {
        $lichHocId = $request->lich_hoc_id;
        $giangVienId = Auth::id();

        $lichHoc = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->findOrFail($lichHocId);

        if ($lichHoc->trang_thai !== 'da_len_lich') {
            return redirect()->back()->with('error', 'Chỉ có thể đổi lịch cho buổi học chưa diễn ra.');
        }

        // Kiểm tra xem đã có yêu cầu nào đang chờ duyệt chưa
        $existingRequest = YeuCauDoiLich::where('lich_hoc_id', $lichHocId)
            ->where('trang_thai', 'cho_duyet')
            ->exists();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'Đã có một yêu cầu đổi lịch đang chờ duyệt cho buổi này.');
        }

        return view('giang_vien.yeu_cau_doi_lich.create', compact('lichHoc'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lich_hoc_id' => 'required|exists:lich_hocs,id',
            'ngay_muon_doi' => 'required|date|after:today',
            'gio_bat_dau_moi' => 'required',
            'gio_ket_thuc_moi' => 'required|after:gio_bat_dau_moi',
            'phong_hoc_moi' => 'nullable|string|max:50',
            'ly_do' => 'required|string|min:10|max:500',
        ]);

        $lichHoc = LichHoc::findOrFail($request->lich_hoc_id);

        YeuCauDoiLich::create([
            'lich_hoc_id' => $request->lich_hoc_id,
            'giang_vien_id' => Auth::id(),
            'ngay_muon_doi' => $request->ngay_muon_doi,
            'gio_bat_dau_moi' => $request->gio_bat_dau_moi,
            'gio_ket_thuc_moi' => $request->gio_ket_thuc_moi,
            'phong_hoc_moi' => $request->phong_hoc_moi,
            'ly_do' => $request->ly_do,
            'trang_thai' => 'cho_duyet',
        ]);

        // Đánh dấu lịch học là đang có yêu cầu đổi lịch
        $lichHoc->update(['trang_thai' => 'doi_lich']);

        return redirect()->route('gv.yeu_cau.index')->with('success', 'Đã gửi yêu cầu đổi lịch. Chờ admin duyệt!');
    }

    public function destroy($id)
    {
        $yeuCau = YeuCauDoiLich::where('giang_vien_id', Auth::id())->findOrFail($id);

        if ($yeuCau->trang_thai !== 'cho_duyet') {
            return redirect()->back()->with('error', 'Không thể hủy yêu cầu đã được xử lý.');
        }

        $yeuCau->update(['trang_thai' => 'tu_choi']); // Hoặc xóa hẳn, nhưng ở đây theo plan là tu_choi (tự hủy)
        
        // Khôi phục trạng thái lịch học
        $yeuCau->lichHoc->update(['trang_thai' => 'da_len_lich']);

        return redirect()->back()->with('success', 'Đã hủy yêu cầu đổi lịch!');
    }
}
