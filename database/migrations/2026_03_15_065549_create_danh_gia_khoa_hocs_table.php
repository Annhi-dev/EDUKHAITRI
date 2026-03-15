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
        Schema::create('danh_gia_khoa_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('users');
            $table->foreignId('khoa_hoc_id')->constrained('khoa_hocs');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
            $table->json('chi_tiet_danh_gia')->nullable();                // [{tieu_chi_id, diem}]
            $table->decimal('diem_trung_binh', 4, 2);
            $table->integer('diem_noi_dung')->default(0);     // 1-5 sao
            $table->integer('diem_giang_vien')->default(0);   // 1-5 sao
            $table->integer('diem_co_so_vat_chat')->default(0);
            $table->text('gop_y')->nullable();
            $table->boolean('an_danh')->default(true);
            $table->timestamps();
            $table->unique(['hoc_vien_id','lop_hoc_id'], 'unique_hoc_vien_lop_danh_gia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia_khoa_hocs');
    }
};
