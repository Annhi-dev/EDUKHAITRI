<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LichHoc;
use App\Services\ThongBaoService;
use Carbon\Carbon;

class NhacDiemDanh extends Command
{
    protected $signature = 'nhac:diem-danh';
    protected $description = 'Nhắc giảng viên điểm danh buổi học hôm nay';

    public function handle(ThongBaoService $thongBaoService)
    {
        $today = today()->toDateString();
        $now = now()->toTimeString();

        // Tìm các buổi học hôm nay, đã bắt đầu nhưng chưa điểm danh
        $lichChuaDiemDanh = LichHoc::where('ngay_hoc', $today)
            ->where('trang_thai', 'da_len_lich')
            ->where('gio_bat_dau', '<=', $now)
            ->with('lopHoc')
            ->get();

        $count = 0;
        foreach ($lichChuaDiemDanh as $lich) {
            $giangVienId = $lich->lopHoc->giang_vien_id;
            
            // Kiểm tra thực tế xem đã có bản ghi điểm danh nào chưa (đề phòng trạng thái chưa cập nhật)
            if (!$lich->daDiemDanh()) {
                $thongBaoService->gui([
                    'tieu_de'  => 'Nhắc nhở điểm danh',
                    'noi_dung' => 'Buổi học lớp ' . $lich->lopHoc->ten_lop . ' bắt đầu lúc ' . substr($lich->gio_bat_dau, 0, 5) . ' vẫn chưa được điểm danh. Thầy/Cô vui lòng thực hiện điểm danh cho học viên nhé!',
                    'loai'     => 'diem_danh',
                    'muc_do'   => 'warning',
                    'url'      => route('gv.diem_danh.create', ['lich_hoc_id' => $lich->id]),
                    'icon'     => 'bell',
                ], $giangVienId);
                $count++;
            }
        }

        $this->info("Đã gửi $count nhắc nhở điểm danh cho giảng viên!");
    }
}
