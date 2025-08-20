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
        Schema::table('health_plans', function (Blueprint $table) {
            $table->uuid('created_by')->nullable(); // Adiciona a coluna created_by
    
            // Adiciona a chave estrangeira se necessário
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null'); // Referência à tabela users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_plans', function (Blueprint $table) {
            $table->dropForeign(['created_by']); // Remove a chave estrangeira
            $table->dropColumn('created_by'); // Remove a coluna created_by
        });
    }
};
