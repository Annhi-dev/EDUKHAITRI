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
        Schema::create('lich_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->date('ngay_hoc');
            $table->enum('thu_trong_tuan', ['2','3','4','5','6','7','CN']);
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            $table->string('phong_hoc')->nullable();
            $table->enum('trang_thai', ['da_len_lich','hoan_thanh','huy','doi_lich'])->default('da_len_lich');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_hocs');
    }
};
