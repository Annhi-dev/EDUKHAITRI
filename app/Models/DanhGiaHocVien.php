<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGiaHocVien extends Model
{
    protected $fillable = [
        'hoc_vien_id',
        'giang_vien_id',
        'lop_hoc_id',
        'ky_hoc',
        'nam_hoc',
        'chi_tiet_danh_gia',
        'diem_trung_binh',
        'nhan_xet',
        'xep_loai',
    ];

    protected $casts = [
        'chi_tiet_danh_gia' => 'array',
    ];

    public function hocVien() { return $this->belongsTo(User::class, 'hoc_vien_id'); }
    public function giangVien() { return $this->belongsTo(User::class, 'giang_vien_id'); }
    public function lopHoc() { return $this->belongsTo(LopHoc::class); }
}
