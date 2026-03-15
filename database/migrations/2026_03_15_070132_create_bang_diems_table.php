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
        Schema::create('bang_diems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('users');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
            $table->foreignId('giang_vien_id')->constrained('users');
            $table->decimal('diem_chuyen_can', 4, 2)->nullable();    // 10% - tính từ điểm danh
            $table->decimal('diem_kiem_tra_1', 4, 2)->nullable();    // 15%
            $table->decimal('diem_kiem_tra_2', 4, 2)->nullable();    // 15%
            $table->decimal('diem_giua_ky',    4, 2)->nullable();    // 20%
            $table->decimal('diem_cuoi_ky',    4, 2)->nullable();    // 40%
            $table->decimal('diem_trung_binh', 4, 2)->nullable();    // tự tính
            $table->enum('xep_loai', ['xuat_sac','gioi','kha','trung_binh','yeu','chua_xep_loai'])
                   ->default('chua_xep_loai');
            $table->boolean('da_khoa')->default(false);              // Khóa điểm sau khi nộp
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->unique(['hoc_vien_id', 'lop_hoc_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bang_diems');
    }
};
