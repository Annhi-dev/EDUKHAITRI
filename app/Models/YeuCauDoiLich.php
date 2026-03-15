<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauDoiLich extends Model
{
    protected $fillable = [
        'lich_hoc_id',
        'giang_vien_id',
        'ngay_muon_doi',
        'gio_bat_dau_moi',
        'gio_ket_thuc_moi',
        'phong_hoc_moi',
        'ly_do',
        'trang_thai',
        'ghi_chu_admin',
    ];

    public function lichHoc()
    {
        return $this->belongsTo(LichHoc::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }
}
