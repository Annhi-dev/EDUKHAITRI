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
        Schema::create('diem_danhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lich_hoc_id')->constrained('lich_hocs')->onDelete('cascade');
            $table->foreignId('hoc_vien_id')->constrained('users');
            $table->foreignId('giang_vien_id')->constrained('users');  // GV thực hiện điểm danh
            $table->enum('trang_thai', ['co_mat','vang_co_phep','vang_khong_phep','di_muon','ve_som'])
                   ->default('co_mat');
            $table->time('gio_den')->nullable();          // Giờ học viên đến thực tế
            $table->text('ghi_chu')->nullable();
            $table->timestamp('thoi_gian_diem_danh')->nullable();
            $table->timestamps();
            $table->unique(['lich_hoc_id', 'hoc_vien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diem_danhs');
    }
};
