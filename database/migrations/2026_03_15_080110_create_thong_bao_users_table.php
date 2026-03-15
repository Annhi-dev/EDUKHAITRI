<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_bao_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thong_bao_id')->constrained('thong_baos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('da_doc')->default(false);
            $table->timestamp('doc_luc')->nullable();
            $table->timestamps();
            $table->unique(['thong_bao_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao_users');
    }
};
