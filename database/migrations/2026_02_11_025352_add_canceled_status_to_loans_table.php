<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE loans 
            MODIFY status 
            ENUM('borrowed','returned','canceled') 
            DEFAULT 'borrowed'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE loans 
            MODIFY status 
            ENUM('borrowed','returned') 
            DEFAULT 'borrowed'
        ");
    }
};
