<?php

namespace App\Services;

use App\Models\ThongBao;
use App\Models\ThongBaoUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ThongBaoService
{
    // Gửi TB đến 1 hoặc nhiều user cụ thể
    public function gui(array $data, array|int $userIds): ThongBao
    {
        $thongBao = ThongBao::create([
            'tieu_de'    => $data['tieu_de'],
            'noi_dung'   => $data['noi_dung'],
            'loai'       => $data['loai'] ?? 'chung',
            'muc_do'     => $data['muc_do'] ?? 'info',
            'url'        => $data['url'] ?? null,
            'icon'       => $data['icon'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $userIds = is_array($userIds) ? $userIds : [$userIds];
        $pivot = collect($userIds)->mapWithKeys(fn($id) => [
            $id => ['da_doc' => false]
        ])->all();
        
        $thongBao->users()->attach($pivot);

        return $thongBao;
    }

    // Gửi TB đến toàn bộ user theo role
    public function guiTheoRole(array $data, string|array $roles): ThongBao
    {
        $userIds = User::role($roles)->pluck('id')->toArray();
        return $this->gui($data, $userIds);
    }

    // Gửi TB đến tất cả user đang hoạt động
    public function guiTatCa(array $data): ThongBao
    {
        $thongBao = ThongBao::create(array_merge($data, ['gui_tat_ca' => true, 'created_by' => Auth::id()]));
        $userIds = User::where('is_active', true)->pluck('id')->toArray();
        $thongBao->users()->attach(array_fill_keys($userIds, ['da_doc' => false]));
        return $thongBao;
    }

    // Đánh dấu đã đọc
    public function danhDauDaDoc(int $thongBaoId, int $userId): void
    {
        ThongBaoUser::where('thong_bao_id', $thongBaoId)
                    ->where('user_id', $userId)
                    ->update(['da_doc' => true, 'doc_luc' => now()]);
    }

    // Đánh dấu tất cả đã đọc
    public function danhDauTatCaDaDoc(int $userId): void
    {
        ThongBaoUser::where('user_id', $userId)
                    ->where('da_doc', false)
                    ->update(['da_doc' => true, 'doc_luc' => now()]);
    }
}
