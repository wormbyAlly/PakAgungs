<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peminjaman_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peminjaman_id')
                  ->constrained('peminjaman')
                  ->cascadeOnDelete();

            $table->foreignId('barang_id')
                  ->constrained('barang')
                  ->restrictOnDelete();

            $table->unsignedInteger('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_detail');
    }
};