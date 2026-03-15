<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DanhSachHocVienExport implements FromCollection, WithHeadings, WithMapping
{
    protected $hocViens;

    public function __construct($hocViens)
    {
        $this->hocViens = $hocViens;
    }

    public function collection()
    {
        return $this->hocViens;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã học viên',
            'Họ và tên',
            'Ngày sinh',
            'Email',
            'Số điện thoại',
            'Chuyên cần (%)',
            'Điểm trung bình',
        ];
    }

    public function map($hv): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $hv->hocVienProfile->ma_hoc_vien ?? 'N/A',
            $hv->name,
            $hv->hocVienProfile->ngay_sinh ?? 'N/A',
            $hv->email,
            $hv->phone ?? 'N/A',
            '---', // Chuyên cần sẽ tính toán thêm nếu cần
            '---', // Điểm TB
        ];
    }
}
