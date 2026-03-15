<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HocVienProfile extends Model
{
    protected $fillable = [
        'user_id',
        'ma_hoc_vien',
        'ngay_sinh',
        'gioi_tinh',
        'so_cmnd',
        'dia_chi',
        'truong_tot_nghiep',
        'ngay_nhap_hoc',
        'trang_thai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
