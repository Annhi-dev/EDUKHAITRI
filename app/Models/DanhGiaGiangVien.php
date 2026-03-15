<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGiaGiangVien extends Model
{
    protected $fillable = [
        'giang_vien_id',
        'ky_hoc',
        'nam_hoc',
        'diem_tb_tu_hoc_vien',
        'diem_chuyen_mon',
        'diem_chuyen_can',
        'diem_tong',
        'nhan_xet_admin',
        'xep_loai',
    ];

    public function giangVien() { return $this->belongsTo(User::class, 'giang_vien_id'); }
}
