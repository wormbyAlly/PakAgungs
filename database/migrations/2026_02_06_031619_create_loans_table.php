<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained()
                ->cascadeOnDelete();

            // Data peminjaman
            $table->integer('quantity')->default(1);
            $table->string('location'); // lokasi peminjaman
            $table->date('loan_date');
            $table->date('return_date')->nullable();

            // Status
            $table->enum('status', ['borrowed', 'returned'])
                ->default('borrowed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
