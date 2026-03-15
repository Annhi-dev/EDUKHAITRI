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
        Schema::create('tieu_chi_danh_gias', function (Blueprint $table) {
            $table->id();
            $table->string('ten_tieu_chi');                   // VD: "Kiến thức chuyên môn"
            $table->enum('loai', ['giang_vien','khoa_hoc','hoc_vien']);
            $table->integer('trong_so')->default(1);          // Trọng số (1-5)
            $table->text('mo_ta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tieu_chi_danh_gias');
    }
};
