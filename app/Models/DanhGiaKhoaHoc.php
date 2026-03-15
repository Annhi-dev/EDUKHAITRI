<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGiaKhoaHoc extends Model
{
    protected $fillable = [
        'hoc_vien_id',
        'khoa_hoc_id',
        'lop_hoc_id',
        'chi_tiet_danh_gia',
        'diem_trung_binh',
        'diem_noi_dung',
        'diem_giang_vien',
        'diem_co_so_vat_chat',
        'gop_y',
        'an_danh',
    ];

    protected $casts = [
        'chi_tiet_danh_gia' => 'array',
    ];

    public function hocVien() { return $this->belongsTo(User::class, 'hoc_vien_id'); }
    public function khoaHoc() { return $this->belongsTo(KhoaHoc::class); }
    public function lopHoc() { return $this->belongsTo(LopHoc::class); }
}
