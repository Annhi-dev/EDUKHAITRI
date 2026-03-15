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
        Schema::create('hoc_vien_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ma_hoc_vien')->unique();          // Mã HV: HV001
            $table->date('ngay_sinh')->nullable();
            $table->enum('gioi_tinh', ['nam','nu','khac'])->nullable();
            $table->string('so_cmnd')->nullable();
            $table->string('dia_chi')->nullable();
            $table->string('truong_tot_nghiep')->nullable();
            $table->date('ngay_nhap_hoc')->nullable();
            $table->enum('trang_thai', ['dang_hoc','bao_luu','da_tot_nghiep','da_nghi'])->default('dang_hoc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_vien_profiles');
    }
};
