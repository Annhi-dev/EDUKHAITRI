<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BangDiem extends Model
{
    protected $fillable = [
        'hoc_vien_id',
        'lop_hoc_id',
        'giang_vien_id',
        'diem_chuyen_can',
        'diem_kiem_tra_1',
        'diem_kiem_tra_2',
        'diem_giua_ky',
        'diem_cuoi_ky',
        'diem_trung_binh',
        'xep_loai',
        'da_khoa',
        'ghi_chu',
    ];

    public function hocVien()
    {
        return $this->belongsTo(User::class, 'hoc_vien_id');
    }

    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    // Tự động tính điểm trung bình khi có dữ liệu
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->diem_trung_binh = $model->calculateDiemTB();
            $model->xep_loai = $model->calculateXepLoai($model->diem_trung_binh);
        });
    }

    public function calculateDiemTB()
    {
        // Công thức: CC*10% + KT1*15% + KT2*15% + GK*20% + CK*40%
        $cc = $this->diem_chuyen_can ?? 0;
        $kt1 = $this->diem_kiem_tra_1 ?? 0;
        $kt2 = $this->diem_kiem_tra_2 ?? 0;
        $gk = $this->diem_giua_ky ?? 0;
        $ck = $this->diem_cuoi_ky ?? 0;

        return ($cc * 0.1) + ($kt1 * 0.15) + ($kt2 * 0.15) + ($gk * 0.2) + ($ck * 0.4);
    }

    public function calculateXepLoai($dtb)
    {
        if (!$this->diem_cuoi_ky && $dtb == 0) return 'chua_xep_loai';
        
        if ($dtb >= 9.0) return 'xuat_sac';
        if ($dtb >= 8.0) return 'gioi';
        if ($dtb >= 6.5) return 'kha';
        if ($dtb >= 5.0) return 'trung_binh';
        return 'yeu';
    }
}
