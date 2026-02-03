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
        Schema::create('jenis_coas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nama');
            $table->unsignedBigInteger('induk_id')->nullable();
            $table->timestamps();
        });

        Schema::table('jenis_coas', function (Blueprint $table) {
            $table->foreign('induk_id')
                ->references('id')
                ->on('jenis_coas')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_coas');
    }
};
