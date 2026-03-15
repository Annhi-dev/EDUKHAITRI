<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiangVienProfile extends Model
{
    protected $fillable = [
        'user_id',
        'ma_giang_vien',
        'chuyen_mon',
        'hoc_vi',
        'so_cmnd',
        'ngay_sinh',
        'gioi_tinh',
        'dia_chi',
        'ngay_vao_lam',
        'trang_thai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
