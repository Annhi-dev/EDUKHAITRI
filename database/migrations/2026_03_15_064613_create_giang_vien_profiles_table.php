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
        Schema::create('giang_vien_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ma_giang_vien')->unique();       // Mã GV: GV001
            $table->string('chuyen_mon')->nullable();         // Chuyên môn: Toán, Lý...
            $table->string('hoc_vi')->nullable();             // Học vị: Thạc sĩ, Tiến sĩ
            $table->string('so_cmnd')->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->enum('gioi_tinh', ['nam','nu','khac'])->nullable();
            $table->string('dia_chi')->nullable();
            $table->date('ngay_vao_lam')->nullable();
            $table->enum('trang_thai', ['dang_day','nghi_phep','da_nghi'])->default('dang_day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giang_vien_profiles');
    }
};
