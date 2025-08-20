<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Remove o ON UPDATE CURRENT_TIMESTAMP
            DB::statement("ALTER TABLE appointments MODIFY data_hora_inicio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Restaura o comportamento anterior (se precisar voltar atrás)
            DB::statement("ALTER TABLE appointments MODIFY data_hora_inicio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;");
        });
    }
};
