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
        Schema::create('hoc_vien_lop_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('users');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs');
            $table->date('ngay_tham_gia');
            $table->enum('trang_thai', ['dang_hoc','da_hoan_thanh','da_nghi'])->default('dang_hoc');
            $table->unique(['hoc_vien_id', 'lop_hoc_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_vien_lop_hocs');
    }
};
