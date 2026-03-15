<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhoaHoc extends Model
{
    protected $table = 'khoa_hocs';

    protected $fillable = [
        'ma_khoa_hoc',
        'ten_khoa_hoc',
        'mo_ta',
        'so_buoi',
        'so_tiet_moi_buoi',
        'hoc_phi',
        'trang_thai',
    ];

    public function lopHocs()
    {
        return $this->hasMany(LopHoc::class);
    }
}
