<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TieuChiDanhGia extends Model
{
    protected $fillable = [
        'ten_tieu_chi',
        'loai',
        'trong_so',
        'mo_ta',
        'is_active',
    ];
}
