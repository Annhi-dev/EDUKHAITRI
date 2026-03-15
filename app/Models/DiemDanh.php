<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiemDanh extends Model
{
    protected $fillable = [
        'lich_hoc_id',
        'hoc_vien_id',
        'giang_vien_id',
        'trang_thai',
        'gio_den',
        'ghi_chu',
        'thoi_gian_diem_danh',
    ];

    public function lichHoc()
    {
        return $this->belongsTo(LichHoc::class);
    }

    public function hocVien()
    {
        return $this->belongsTo(User::class, 'hoc_vien_id');
    }

    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }
}
