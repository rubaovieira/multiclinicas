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
        Schema::table('service_medicines', function (Blueprint $table) {
            // Adicionando a coluna 'deleted_at' para registros de exclusão suave (soft delete)
            $table->timestamp('deleted_at')->nullable();

            // Adicionando a coluna 'deleted_by' que faz referência à tabela 'users'
            $table->uuid('deleted_by')->nullable();

            // Definindo a chave estrangeira para 'deleted_by', que referencia 'id' na tabela 'users'
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('service_medicines', function (Blueprint $table) {
            // Removendo a chave estrangeira e as colunas criadas
            $table->dropForeign(['deleted_by']); // Remover a chave estrangeira
            $table->dropColumn(['deleted_at', 'deleted_by']);
        });
    }
};
