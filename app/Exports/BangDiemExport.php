<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BangDiemExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bangDiems;

    public function __construct($bangDiems)
    {
        $this->bangDiems = $bangDiems;
    }

    public function collection()
    {
        return $this->bangDiems;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã học viên',
            'Họ và tên',
            'Chuyên cần',
            'KT1',
            'KT2',
            'Giữa kỳ',
            'Cuối kỳ',
            'Điểm TB',
            'Xếp loại',
        ];
    }

    public function map($bd): array
    {
        static $stt = 0;
        $stt++;

        return [
            $stt,
            $bd->hocVien->hocVienProfile->ma_hoc_vien ?? 'N/A',
            $bd->hocVien->name,
            $bd->diem_chuyen_can,
            $bd->diem_kiem_tra_1,
            $bd->diem_kiem_tra_2,
            $bd->diem_giua_ky,
            $bd->diem_cuoi_ky,
            $bd->diem_trung_binh,
            ucfirst(str_replace('_', ' ', $bd->xep_loai)),
        ];
    }
}
