<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('danh_gia_giang_viens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giang_vien_id')->constrained('users');
            $table->integer('ky_hoc');
            $table->integer('nam_hoc');
            $table->decimal('diem_tb_tu_hoc_vien', 4, 2)->default(0);  // Từ danh_gia_khoa_hocs
            $table->decimal('diem_chuyen_mon', 4, 2)->default(0);       // Admin nhập
            $table->decimal('diem_chuyen_can', 4, 2)->default(0);       // Tính từ điểm danh
            $table->decimal('diem_tong', 4, 2)->default(0);
            $table->text('nhan_xet_admin')->nullable();
            $table->enum('xep_loai', ['xuat_sac','gioi','kha','trung_binh','yeu'])->nullable();
            $table->timestamps();
            $table->unique(['giang_vien_id','ky_hoc','nam_hoc'], 'unique_gv_danh_gia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia_giang_viens');
    }
};
