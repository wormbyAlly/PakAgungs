<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loan_returns', function (Blueprint $table) {
            $table->id();

            // relasi ke peminjaman
            $table->foreignId('loan_id')
                ->constrained('loans')
                ->cascadeOnDelete();

            // siapa yang mengembalikan (bisa beda user)
            $table->foreignId('returned_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // kondisi barang
            $table->enum('condition', ['baik', 'rusak'])
                ->default('baik');

            // wajib jika rusak
            $table->text('condition_note')->nullable();

            $table->timestamp('returned_at');

            $table->timestamps();

            // satu loan hanya boleh punya satu return
            $table->unique('loan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_returns');
    }
};
