<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\ThongBaoService;
use App\Models\ThongBao;

class ThongBaoController extends Controller
{
    protected $thongBaoService;

    public function __construct(ThongBaoService $thongBaoService)
    {
        $this->thongBaoService = $thongBaoService;
    }

    public function index()
    {
        $user = Auth::user();
        $thongBaos = $user->thongBaos()
            ->orderByPivot('created_at', 'desc')
            ->paginate(20);
            
        $soChuaDoc = $user->soThongBaoChuaDoc();

        return view('thong_bao.index', compact('thongBaos', 'soChuaDoc'));
    }

    public function daDoc($id)
    {
        $user = Auth::user();
        $this->thongBaoService->danhDauDaDoc($id, $user->id);
        
        $thongBao = ThongBao::findOrFail($id);
        if ($thongBao->url) {
            return redirect($thongBao->url);
        }
        
        return redirect()->back();
    }

    public function docTatCa()
    {
        $user = Auth::user();
        $this->thongBaoService->danhDauTatCaDaDoc($user->id);
        
        return redirect()->back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }

    public function layMoi()
    {
        $user = Auth::user();
        $thongBaos = $user->thongBaos()
            ->wherePivot('da_doc', false)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'so_chua_doc' => $user->soThongBaoChuaDoc(),
            'danh_sach'   => $thongBaos->map(fn($tb) => [
                'id'       => $tb->id,
                'tieu_de'  => $tb->tieu_de,
                'noi_dung' => Str::limit($tb->noi_dung, 80),
                'loai'     => $tb->loai,
                'muc_do'   => $tb->muc_do,
                'url'      => route('thong_bao.doc', $tb->id),
                'icon'     => $tb->icon,
                'thoi_gian'=> $tb->pivot->created_at->diffForHumans(),
            ])->toArray()
        ]);
    }
}
