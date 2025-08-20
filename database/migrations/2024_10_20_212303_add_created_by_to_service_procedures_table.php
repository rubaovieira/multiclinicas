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
        Schema::table('service_procedures', function (Blueprint $table) {
            // Adiciona a coluna created_by como UUID
            $table->uuid('created_by')->nullable()->after('id');

            // Cria a chave estrangeira, referenciando o campo id da tabela users
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_procedures', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
