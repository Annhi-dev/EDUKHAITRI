<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGiangVien = User::role('giang_vien')->count();
        $totalHocVien   = User::role('hoc_vien')->count();
        $totalLopHoc    = \App\Models\LopHoc::where('trang_thai', 'dang_hoc')->count();
        $totalKhoaHoc   = \App\Models\KhoaHoc::count();

        $recentRequests = \App\Models\YeuCauDoiLich::with(['giangVien', 'lichHoc.lopHoc'])
            ->where('trang_thai', 'cho_duyet')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('totalGiangVien', 'totalHocVien', 'totalLopHoc', 'totalKhoaHoc', 'recentRequests'));
    }
}
