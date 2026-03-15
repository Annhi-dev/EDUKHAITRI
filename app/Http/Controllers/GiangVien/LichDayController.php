<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LichHoc;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LichDayController extends Controller
{
    public function index(Request $request)
    {
        $giangVienId = Auth::id();
        $query = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->with(['lopHoc.khoaHoc']);

        // Filters
        if ($request->filled('lop_hoc_id')) {
            $query->where('lop_hoc_id', $request->lop_hoc_id);
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Mặc định xem theo tuần nếu không có filter tháng
        if ($request->filled('thang')) {
            $thang = $request->thang;
            $nam = $request->get('nam', date('Y'));
            $query->whereMonth('ngay_hoc', $thang)->whereYear('ngay_hoc', $nam);
        } else {
            // Xem theo tuần
            $tuanStart = $request->filled('tuan') ? Carbon::parse($request->tuan)->startOfWeek() : now()->startOfWeek();
            $tuanEnd = $tuanStart->copy()->endOfWeek();
            $query->whereBetween('ngay_hoc', [$tuanStart, $tuanEnd]);
        }

        $lichHocs = $query->orderBy('ngay_hoc')->orderBy('gio_bat_dau')->get();
        
        $lopHocs = LopHoc::where('giang_vien_id', $giangVienId)->get();

        // Thống kê banner
        $today = now()->format('Y-m-d');
        $lichHomNay = LichHoc::whereHas('lopHoc', fn($q) => $q->where('giang_vien_id', $giangVienId))
            ->whereDate('ngay_hoc', $today)->orderBy('gio_bat_dau')->get();
            
        $lichTuanNayCount = LichHoc::whereHas('lopHoc', fn($q) => $q->where('giang_vien_id', $giangVienId))
            ->whereBetween('ngay_hoc', [now()->startOfWeek(), now()->endOfWeek()])->count();
            
        $tongBuoiThangCount = LichHoc::whereHas('lopHoc', fn($q) => $q->where('giang_vien_id', $giangVienId))
            ->whereMonth('ngay_hoc', now()->month)->whereYear('ngay_hoc', now()->year)->count();

        return view('giang_vien.lich_day.index', compact(
            'lichHocs', 'lopHocs', 'lichHomNay', 'lichTuanNayCount', 'tongBuoiThangCount'
        ));
    }

    public function getEvents(Request $request)
    {
        $giangVienId = Auth::id();
        $start = $request->query('start');
        $end = $request->query('end');

        $lichHocs = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
                $q->where('giang_vien_id', $giangVienId);
            })
            ->with(['lopHoc.khoaHoc'])
            ->whereBetween('ngay_hoc', [substr($start, 0, 10), substr($end, 0, 10)])
            ->get();

        $events = $lichHocs->map(function($item) {
            $color = '#16a34a'; // da_len_lich - green
            if ($item->trang_thai == 'hoan_thanh') $color = '#6b7280'; // gray
            if ($item->trang_thai == 'huy') $color = '#ef4444'; // red
            if ($item->trang_thai == 'doi_lich') $color = '#f59e0b'; // amber

            return [
                'id' => $item->id,
                'title' => $item->lopHoc->ma_lop . ' - ' . $item->phong_hoc,
                'start' => $item->ngay_hoc . 'T' . $item->gio_bat_dau,
                'end' => $item->ngay_hoc . 'T' . $item->gio_ket_thuc,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'lop' => $item->lopHoc->ten_lop,
                    'khoa' => $item->lopHoc->khoaHoc->ten_khoa_hoc,
                    'phong' => $item->phong_hoc,
                    'trang_thai' => $item->trang_thai,
                ]
            ];
        });

        return response()->json($events);
    }

    public function show($id)
    {
        $giangVienId = Auth::id();
        $lichHoc = LichHoc::whereHas('lopHoc', function($q) use ($giangVienId) {
            $q->where('giang_vien_id', $giangVienId);
        })->with(['lopHoc.khoaHoc', 'lopHoc.hocViens'])->findOrFail($id);

        $hocViens = $lichHoc->lopHoc->hocViens;

        return view('giang_vien.lich_day.show', compact('lichHoc', 'hocViens'));
    }
}
