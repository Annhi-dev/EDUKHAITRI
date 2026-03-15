<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LopHoc extends Model
{
    protected $fillable = [
        'ma_lop',
        'ten_lop',
        'khoa_hoc_id',
        'giang_vien_id',
        'si_so_toi_da',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
        'phong_hoc',
    ];

    public function khoaHoc()
    {
        return $this->belongsTo(KhoaHoc::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    public function lichHocs()
    {
        return $this->hasMany(LichHoc::class);
    }

    public function hocViens()
    {
        return $this->belongsToMany(User::class, 'hoc_vien_lop_hocs', 'lop_hoc_id', 'hoc_vien_id')
                    ->withPivot('ngay_tham_gia', 'trang_thai')
                    ->withTimestamps();
    }
}
