<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHoc;
use App\Models\DanhGiaKhoaHoc;
use App\Services\ThongBaoService;
use Carbon\Carbon;

class NhacDanhGiaKhoaHoc extends Command
{
    protected $signature = 'nhac:danh-gia';
    protected $description = 'Nhắc học viên đánh giá khóa học sau khi kết thúc';

    public function handle(ThongBaoService $thongBaoService)
    {
        $homNay = today();
        // Tìm các lớp kết thúc trong 7 ngày qua
        $lopDaKetThuc = LopHoc::where('trang_thai', 'da_ket_thuc')
            ->whereBetween('ngay_ket_thuc', [$homNay->copy()->subDays(7), $homNay])
            ->with('hocViens')
            ->get();

        $count = 0;
        foreach ($lopDaKetThuc as $lop) {
            foreach ($lop->hocViens as $hv) {
                // Kiểm tra học viên đã đánh giá chưa
                $daDanhGia = DanhGiaKhoaHoc::where('hoc_vien_id', $hv->id)
                    ->where('lop_hoc_id', $lop->id)
                    ->exists();

                if (!$daDanhGia) {
                    $thongBaoService->gui([
                        'tieu_de'  => 'Đánh giá khóa học của bạn',
                        'noi_dung' => 'Khóa học ' . $lop->khoaHoc->ten_khoa_hoc . ' đã kết thúc. Hãy dành 2 phút đánh giá để giúp chúng tôi cải thiện chất lượng nhé!',
                        'loai'     => 'danh_gia',
                        'muc_do'   => 'info',
                        'url'      => route('hv.danh_gia.create', $lop->id),
                        'icon'     => 'star',
                    ], $hv->id);
                    $count++;
                }
            }
        }

        $this->info("Đã gửi $count nhắc nhở đánh giá!");
    }
}
