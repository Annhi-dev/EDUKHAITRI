<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\KhoaHoc;
use App\Models\User;
use App\Models\LichHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LopHocController extends Controller
{
    public function index(Request $request)
    {
        $query = LopHoc::with(['khoaHoc', 'giangVien']);

        if ($request->filled('khoa_hoc_id')) {
            $query->where('khoa_hoc_id', $request->khoa_hoc_id);
        }

        if ($request->filled('giang_vien_id')) {
            $query->where('giang_vien_id', $request->giang_vien_id);
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $lopHocs = $query->paginate(15);
        $khoaHocs = KhoaHoc::all();
        $giangViens = User::role('giang_vien')->get();

        return view('admin.lop_hoc.index', compact('lopHocs', 'khoaHocs', 'giangViens'));
    }

    public function create()
    {
        $khoaHocs = KhoaHoc::where('trang_thai', 'dang_mo')->get();
        $giangViens = User::role('giang_vien')->where('is_active', true)->get();
        return view('admin.lop_hoc.create', compact('khoaHocs', 'giangViens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_lop' => 'required|unique:lop_hocs',
            'ten_lop' => 'required|string|max:255',
            'khoa_hoc_id' => 'required|exists:khoa_hocs,id',
            'giang_vien_id' => 'required|exists:users,id',
            'si_so_toi_da' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'phong_hoc' => 'nullable|string',
            'thu_trong_tuan' => 'required|array',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required|after:gio_bat_dau',
        ]);

        DB::transaction(function () use ($request) {
            $lopHoc = LopHoc::create([
                'ma_lop' => $request->ma_lop,
                'ten_lop' => $request->ten_lop,
                'khoa_hoc_id' => $request->khoa_hoc_id,
                'giang_vien_id' => $request->giang_vien_id,
                'si_so_toi_da' => $request->si_so_toi_da,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'phong_hoc' => $request->phong_hoc,
                'trang_thai' => 'sap_khai_giang',
            ]);

            // Auto-generate LichHoc
            $startDate = Carbon::parse($request->ngay_bat_dau);
            $endDate = $request->ngay_ket_thuc ? Carbon::parse($request->ngay_ket_thuc) : $startDate->copy()->addMonths(3);
            $selectedDays = $request->thu_trong_tuan; // Array of [2, 3, 4, 5, 6, 7, CN]

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dayOfWeek = $currentDate->dayOfWeek; // 0 (Sun) to 6 (Sat)
                $dayMap = [
                    0 => 'CN',
                    1 => '2',
                    2 => '3',
                    3 => '4',
                    4 => '5',
                    5 => '6',
                    6 => '7',
                ];
                
                if (in_array($dayMap[$dayOfWeek], $selectedDays)) {
                    LichHoc::create([
                        'lop_hoc_id' => $lopHoc->id,
                        'ngay_hoc' => $currentDate->format('Y-m-d'),
                        'thu_trong_tuan' => $dayMap[$dayOfWeek],
                        'gio_bat_dau' => $request->gio_bat_dau,
                        'gio_ket_thuc' => $request->gio_ket_thuc,
                        'phong_hoc' => $request->phong_hoc,
                        'trang_thai' => 'da_len_lich',
                    ]);
                }
                $currentDate->addDay();
            }
        });

        return redirect()->route('admin.lop_hoc.index')->with('success', 'Tạo lớp học và lịch học thành công!');
    }

    public function show(LopHoc $lop_hoc)
    {
        $lop_hoc->load(['khoaHoc', 'giangVien', 'hocViens', 'lichHocs' => function($q) {
            $q->orderBy('ngay_hoc', 'asc');
        }]);
        return view('admin.lop_hoc.show', compact('lop_hoc'));
    }

    public function previewSchedule(Request $request)
    {
        $startDate = Carbon::parse($request->ngay_bat_dau);
        $endDate = $request->ngay_ket_thuc ? Carbon::parse($request->ngay_ket_thuc) : $startDate->copy()->addMonths(3);
        $selectedDays = $request->thu_trong_tuan ?? [];
        
        $preview = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $dayMap = [0 => 'CN', 1 => '2', 2 => '3', 3 => '4', 4 => '5', 5 => '6', 6 => '7'];
            
            if (in_array($dayMap[$dayOfWeek], $selectedDays)) {
                $preview[] = [
                    'ngay' => $currentDate->format('d/m/Y'),
                    'thu' => 'Thứ ' . $dayMap[$dayOfWeek],
                ];
            }
            $currentDate->addDay();
            if (count($preview) >= 50) break; // Limit preview
        }

        return response()->json($preview);
    }

    public function addHocVien(Request $request, $id)
    {
        $lopHoc = LopHoc::findOrFail($id);
        $request->validate([
            'hoc_vien_id' => 'required|exists:users,id',
        ]);

        if ($lopHoc->hocViens()->count() >= $lopHoc->si_so_toi_da) {
            return redirect()->back()->with('error', 'Lớp đã đầy sĩ số.');
        }

        if ($lopHoc->hocViens()->where('hoc_vien_id', $request->hoc_vien_id)->exists()) {
            return redirect()->back()->with('error', 'Học viên đã có trong lớp này.');
        }

        $lopHoc->hocViens()->attach($request->hoc_vien_id, [
            'ngay_tham_gia' => now(),
            'trang_thai' => 'dang_hoc',
        ]);

        return redirect()->back()->with('success', 'Thêm học viên vào lớp thành công!');
    }

    public function removeHocVien($lopId, $hocVienId)
    {
        $lopHoc = LopHoc::findOrFail($lopId);
        $lopHoc->hocViens()->detach($hocVienId);
        return redirect()->back()->with('success', 'Đã xóa học viên khỏi lớp.');
    }
}
