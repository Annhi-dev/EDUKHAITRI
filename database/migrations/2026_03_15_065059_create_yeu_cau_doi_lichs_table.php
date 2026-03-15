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
        Schema::create('yeu_cau_doi_lichs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lich_hoc_id')->constrained('lich_hocs');
            $table->foreignId('giang_vien_id')->constrained('users');
            $table->date('ngay_muon_doi');
            $table->time('gio_bat_dau_moi');
            $table->time('gio_ket_thuc_moi');
            $table->string('phong_hoc_moi')->nullable();
            $table->text('ly_do');
            $table->enum('trang_thai', ['cho_duyet','da_duyet','tu_choi'])->default('cho_duyet');
            $table->text('ghi_chu_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_doi_lichs');
    }
};
