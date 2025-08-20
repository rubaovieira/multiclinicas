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
        Schema::create('procedures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Coluna para o nome
            $table->text('preparo'); // Coluna para o preparo
            $table->boolean('active')->default(true); // Coluna boolean para ativo, padrão é true
            $table->uuid('created_by'); // Coluna para referência ao usuário que criou, tipo UUID

            // Definindo a chave estrangeira para a tabela users
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps(); // Adiciona created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
