<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_baos', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->enum('loai', [
                'lich_hoc',         // thay đổi lịch học
                'diem_so',          // cập nhật điểm
                'diem_danh',        // nhắc điểm danh
                'yeu_cau_doi_lich', // yêu cầu đổi lịch
                'he_thong',         // thông báo hệ thống
                'danh_gia',         // nhắc đánh giá khóa học
                'chung'             // thông báo chung
            ])->default('chung');
            $table->enum('muc_do', ['info', 'success', 'warning', 'danger'])->default('info');
            $table->string('url')->nullable();           // Link đính kèm
            $table->string('icon')->nullable();          // Tên icon Heroicons
            $table->boolean('gui_tat_ca')->default(false); // Gửi toàn bộ user
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_baos');
    }
};
