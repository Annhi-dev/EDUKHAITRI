<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ThongBaoService;
use App\Models\ThongBao;
use App\Models\User;

class ThongBaoController extends Controller
{
    protected $thongBaoService;

    public function __construct(ThongBaoService $thongBaoService)
    {
        $this->thongBaoService = $thongBaoService;
    }

    public function index()
    {
        $thongBaos = ThongBao::with('createdBy')->latest()->paginate(15);
        return view('admin.thong_bao.index', compact('thongBaos'));
    }

    public function create()
    {
        $giangViens = User::role('giang_vien')->get();
        return view('admin.thong_bao.create', compact('giangViens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de'  => 'required|string|max:200',
            'noi_dung' => 'required|string|max:1000',
            'loai'     => 'required|in:he_thong,chung,lich_hoc,diem_so',
            'muc_do'   => 'required|in:info,success,warning,danger',
            'url'      => 'nullable|string',
            'gui_den'  => 'required|in:tat_ca,giang_vien,hoc_vien,cu_the',
            'user_ids' => 'required_if:gui_den,cu_the|array'
        ]);

        if ($request->gui_den === 'tat_ca') {
            $this->thongBaoService->guiTatCa($request->only(['tieu_de', 'noi_dung', 'loai', 'muc_do', 'url']));
        } elseif ($request->gui_den === 'giang_vien') {
            $this->thongBaoService->guiTheoRole($request->only(['tieu_de', 'noi_dung', 'loai', 'muc_do', 'url']), 'giang_vien');
        } elseif ($request->gui_den === 'hoc_vien') {
            $this->thongBaoService->guiTheoRole($request->only(['tieu_de', 'noi_dung', 'loai', 'muc_do', 'url']), 'hoc_vien');
        } else {
            $this->thongBaoService->gui($request->only(['tieu_de', 'noi_dung', 'loai', 'muc_do', 'url']), $request->user_ids);
        }

        return redirect()->route('admin.thong_bao.index')->with('success', 'Đã gửi thông báo thành công!');
    }
}
