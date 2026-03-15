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
        Schema::create('danh_gia_hoc_viens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('users');
            $table->foreignId('giang_vien_id')->constrained('users');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
            $table->integer('ky_hoc');                        // 1, 2, ...
            $table->integer('nam_hoc');                       // 2024, 2025
            $table->json('chi_tiet_danh_gia');                // [{tieu_chi_id, diem}]
            $table->decimal('diem_trung_binh', 4, 2);
            $table->text('nhan_xet')->nullable();
            $table->enum('xep_loai', ['xuat_sac','gioi','kha','trung_binh','yeu']);
            $table->timestamps();
            $table->unique(['hoc_vien_id','lop_hoc_id','ky_hoc','nam_hoc'], 'unique_hoc_vien_danh_gia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia_hoc_viens');
    }
};
