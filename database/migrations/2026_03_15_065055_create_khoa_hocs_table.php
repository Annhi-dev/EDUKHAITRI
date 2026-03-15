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
        Schema::create('khoa_hocs', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khoa_hoc')->unique();          // KH001
            $table->string('ten_khoa_hoc');
            $table->text('mo_ta')->nullable();
            $table->integer('so_buoi')->default(0);           // Tổng số buổi
            $table->integer('so_tiet_moi_buoi')->default(2);
            $table->decimal('hoc_phi', 12, 0)->default(0);
            $table->enum('trang_thai', ['dang_mo','da_ket_thuc','tam_dung'])->default('dang_mo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khoa_hocs');
    }
};
