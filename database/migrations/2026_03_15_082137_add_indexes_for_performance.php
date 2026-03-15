<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_hocs', function (Blueprint $table) {
            $table->index(['lop_hoc_id', 'ngay_hoc']);
            $table->index(['ngay_hoc', 'trang_thai']);
        });

        Schema::table('diem_danhs', function (Blueprint $table) {
            $table->index(['lich_hoc_id', 'hoc_vien_id']);
            $table->index(['hoc_vien_id', 'trang_thai']);
        });

        Schema::table('bang_diems', function (Blueprint $table) {
            $table->index(['hoc_vien_id', 'lop_hoc_id']);
            $table->index('giang_vien_id');
        });

        Schema::table('hoc_vien_lop_hocs', function (Blueprint $table) {
            $table->index(['hoc_vien_id', 'trang_thai']);
            $table->index('lop_hoc_id');
        });

        Schema::table('thong_bao_users', function (Blueprint $table) {
            $table->index(['user_id', 'da_doc']);
        });

        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->index(['giang_vien_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::table('lich_hocs', function (Blueprint $table) {
            $table->dropIndex(['lop_hoc_id', 'ngay_hoc']);
            $table->dropIndex(['ngay_hoc', 'trang_thai']);
        });
        // Các bảng khác tương tự nếu cần rollback...
    }
};
