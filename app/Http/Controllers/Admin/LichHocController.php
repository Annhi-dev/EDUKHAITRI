<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichHoc;
use App\Models\LopHoc;
use App\Services\ThongBaoService;
use Illuminate\Http\Request;

class LichHocController extends Controller
{
    protected $thongBaoService;

    public function __construct(ThongBaoService $thongBaoService)
    {
        $this->thongBaoService = $thongBaoService;
    }
    public function index(Request $request)
    {
        $query = LichHoc::with(['lopHoc.khoaHoc', 'lopHoc.giangVien']);

        if ($request->filled('lop_hoc_id')) {
            $query->where('lop_hoc_id', $request->lop_hoc_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('ngay_hoc', [$request->start_date, $request->end_date]);
        }

        $lichHocs = $query->orderBy('ngay_hoc', 'asc')->orderBy('gio_bat_dau', 'asc')->paginate(20);
        $lopHocs = LopHoc::all();

        return view('admin.lich_hoc.index', compact('lichHocs', 'lopHocs'));
    }

    public function getEvents(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $lichHocs = LichHoc::with(['lopHoc.giangVien'])
            ->whereBetween('ngay_hoc', [substr($start, 0, 10), substr($end, 0, 10)])
            ->get();

        $events = $lichHocs->map(function($item) {
            $color = '#4F46E5'; // Default indigo
            if ($item->trang_thai == 'hoan_thanh') $color = '#10B981'; // green
            if ($item->trang_thai == 'huy') $color = '#EF4444'; // red
            if ($item->trang_thai == 'doi_lich') $color = '#F59E0B'; // yellow

            return [
                'id' => $item->id,
                'title' => $item->lopHoc->ma_lop . ' - ' . ($item->lopHoc->giangVien->name ?? 'N/A'),
                'start' => $item->ngay_hoc . 'T' . $item->gio_bat_dau,
                'end' => $item->ngay_hoc . 'T' . $item->gio_ket_thuc,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'phong_hoc' => $item->phong_hoc,
                    'trang_thai' => $item->trang_thai,
                ]
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'ngay_hoc' => 'required|date',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required|after:gio_bat_dau',
            'phong_hoc' => 'nullable|string',
        ]);

        $data['thu_trong_tuan'] = \Carbon\Carbon::parse($request->ngay_hoc)->dayOfWeek;
        $dayMap = [0 => 'CN', 1 => '2', 2 => '3', 3 => '4', 4 => '5', 5 => '6', 6 => '7'];
        $data['thu_trong_tuan'] = $dayMap[$data['thu_trong_tuan']];
        $data['trang_thai'] = 'da_len_lich';

        LichHoc::create($data);

        return redirect()->back()->with('success', 'Thêm buổi học thành công!');
    }

    public function update(Request $request, LichHoc $lich_hoc)
    {
        $data = $request->validate([
            'ngay_hoc' => 'required|date',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required|after:gio_bat_dau',
            'phong_hoc' => 'nullable|string',
            'trang_thai' => 'required|in:da_len_lich,hoan_thanh,huy,doi_lich',
        ]);

        $lich_hoc->update($data);

        // Gửi thông báo cho GV và HV
        $lopHoc = $lich_hoc->lopHoc;
        $giangVienId = $lopHoc->giang_vien_id;
        $hocVienIds = \App\Models\HocVienLopHoc::where('lop_hoc_id', $lopHoc->id)->pluck('hoc_vien_id')->toArray();
        
        $this->thongBaoService->gui([
            'tieu_de'  => 'Lịch học có thay đổi',
            'noi_dung' => 'Buổi học ngày ' . \Carbon\Carbon::parse($lich_hoc->ngay_hoc)->format('d/m/Y') . ' của lớp ' . $lopHoc->ten_lop . ' đã được cập nhật thông tin mới.',
            'loai'     => 'lich_hoc',
            'muc_do'   => 'warning',
            'url'      => route('home'), // Hoặc link đến lịch học cụ thể
            'icon'     => 'calendar',
        ], array_merge([$giangVienId], $hocVienIds));

        return redirect()->back()->with('success', 'Cập nhật buổi học thành công!');
    }

    public function destroy(LichHoc $lich_hoc)
    {
        $lich_hoc->update(['trang_thai' => 'huy']);

        // Gửi thông báo hủy buổi học
        $lopHoc = $lich_hoc->lopHoc;
        $giangVienId = $lopHoc->giang_vien_id;
        $hocVienIds = \App\Models\HocVienLopHoc::where('lop_hoc_id', $lopHoc->id)->pluck('hoc_vien_id')->toArray();

        $this->thongBaoService->gui([
            'tieu_de'  => 'Buổi học đã bị hủy',
            'noi_dung' => 'Buổi học ngày ' . \Carbon\Carbon::parse($lich_hoc->ngay_hoc)->format('d/m/Y') . ' của lớp ' . $lopHoc->ten_lop . ' đã bị hủy.',
            'loai'     => 'lich_hoc',
            'muc_do'   => 'danger',
            'icon'     => 'x-circle',
        ], array_merge([$giangVienId], $hocVienIds));

        return redirect()->back()->with('success', 'Đã hủy buổi học.');
    }
}
