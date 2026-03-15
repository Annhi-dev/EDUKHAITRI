<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\LichHoc;
use App\Models\HocVienLopHoc;
use App\Models\DiemDanh;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LichHocController extends Controller
{
    public function index(Request $request)
    {
        $hocVienId = Auth::id();
        
        // Lấy danh sách ID các lớp học viên đang tham gia
        $lopIds = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', 'dang_hoc')
            ->pluck('lop_hoc_id');

        $query = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien']);

        // Lọc theo lớp
        if ($request->filled('lop_id')) {
            $query->where('lop_hoc_id', $request->lop_id);
        }

        // Mặc định lấy theo tuần hiện tại nếu là view list
        $view = $request->get('view', 'list');
        if ($view === 'list') {
            $tuan = $request->get('tuan', now()->format('Y-\WW'));
            $year = substr($tuan, 0, 4);
            $week = substr($tuan, 6);
            
            $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            
            $query->whereBetween('ngay_hoc', [$startOfWeek, $endOfWeek]);
            $currentTuan = $tuan;
        }

        $lichHocs = $query->orderBy('ngay_hoc')->orderBy('gio_bat_dau')->get();
        
        // Gắn thông tin điểm danh của HV vào từng buổi học
        foreach ($lichHocs as $lich) {
            $lich->diem_danh_hv = DiemDanh::where('lich_hoc_id', $lich->id)
                ->where('hoc_vien_id', $hocVienId)
                ->first();
        }

        $lopHocs = LopHoc::whereIn('id', $lopIds)->get();

        return view('hoc_vien.lich_hoc.index', compact(
            'lichHocs', 
            'lopHocs', 
            'view', 
            'currentTuan' ?? null
        ));
    }

    public function getEvents(Request $request)
    {
        $hocVienId = Auth::id();
        $start = $request->get('start');
        $end = $request->get('end');

        $lopIds = HocVienLopHoc::where('hoc_vien_id', $hocVienId)->pluck('lop_hoc_id');

        $lichHocs = LichHoc::whereIn('lop_hoc_id', $lopIds)
            ->whereBetween('ngay_hoc', [$start, $end])
            ->with(['lopHoc.khoaHoc', 'lopHoc.giangVien'])
            ->get();

        $events = $lichHocs->map(function ($lich) use ($hocVienId) {
            $dd = DiemDanh::where('lich_hoc_id', $lich->id)
                ->where('hoc_vien_id', $hocVienId)
                ->first();

            $color = '#2563eb'; // Mặc định xanh dương
            if ($lich->trang_thai === 'huy') {
                $color = '#6b7280'; // Xám
            } elseif ($dd) {
                if ($dd->trang_thai === 'co_mat') $color = '#16a34a'; // Xanh lá
                elseif (in_array($dd->trang_thai, ['vang_co_phep', 'vang_khong_phep'])) $color = '#dc2626'; // Đỏ
                else $color = '#d97706'; // Vàng (muộn/về sớm)
            }

            return [
                'id' => $lich->id,
                'title' => $lich->lopHoc->ten_lop . ' - ' . $lich->phong_hoc,
                'start' => $lich->ngay_hoc . 'T' . $lich->gio_bat_dau,
                'end' => $lich->ngay_hoc . 'T' . $lich->gio_ket_thuc,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'lop' => $lich->lopHoc->ten_lop,
                    'phong' => $lich->phong_hoc,
                    'giang_vien' => $lich->lopHoc->giangVien->name,
                    'trang_thai_dd' => $dd ? $dd->trang_thai : 'chua_co'
                ]
            ];
        });

        return response()->json($events);
    }

    public function show($id)
    {
        $hocVienId = Auth::id();
        $lichHoc = LichHoc::with(['lopHoc.khoaHoc', 'lopHoc.giangVien.giangVienProfile'])->findOrFail($id);

        // Kiểm tra HV có trong lớp không
        $isInClass = HocVienLopHoc::where('hoc_vien_id', $hocVienId)
            ->where('lop_hoc_id', $lichHoc->lop_hoc_id)
            ->exists();

        if (!$isInClass) {
            abort(403, 'Bạn không thuộc lớp học này.');
        }

        $diemDanh = DiemDanh::where('lich_hoc_id', $id)
            ->where('hoc_vien_id', $hocVienId)
            ->first();

        return view('hoc_vien.lich_hoc.show', compact('lichHoc', 'diemDanh'));
    }
}
