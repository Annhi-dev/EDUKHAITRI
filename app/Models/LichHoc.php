<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichHoc extends Model
{
    protected $fillable = [
        'lop_hoc_id',
        'ngay_hoc',
        'thu_trong_tuan',
        'gio_bat_dau',
        'gio_ket_thuc',
        'phong_hoc',
        'trang_thai',
        'ghi_chu',
    ];

    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class);
    }

    public function yeuCauDoiLichs()
    {
        return $this->hasMany(YeuCauDoiLich::class);
    }

    public function diemDanhs()
    {
        return $this->hasMany(DiemDanh::class);
    }

    public function daDiemDanh()
    {
        return $this->diemDanhs()->exists();
    }
}
