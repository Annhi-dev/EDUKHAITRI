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
        Schema::create('lop_hocs', function (Blueprint $table) {
            $table->id();
            $table->string('ma_lop')->unique();               // L001
            $table->string('ten_lop');
            $table->foreignId('khoa_hoc_id')->constrained('khoa_hocs');
            $table->foreignId('giang_vien_id')->constrained('users'); // user có role giang_vien
            $table->integer('si_so_toi_da')->default(30);
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc')->nullable();
            $table->enum('trang_thai', ['dang_hoc','sap_khai_giang','da_ket_thuc'])->default('sap_khai_giang');
            $table->string('phong_hoc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hocs');
    }
};
