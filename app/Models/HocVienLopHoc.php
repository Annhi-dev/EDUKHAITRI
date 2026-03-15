<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HocVienLopHoc extends Model
{
    protected $table = 'hoc_vien_lop_hocs';

    protected $fillable = [
        'hoc_vien_id',
        'lop_hoc_id',
        'ngay_tham_gia',
        'trang_thai',
    ];

    public function hocVien()
    {
        return $this->belongsTo(User::class, 'hoc_vien_id');
    }

    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }
}
