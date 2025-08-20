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
        Schema::create('service_files', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Chave primária do tipo UUID
            $table->uuid('service_id'); // Referência para a tabela services
            $table->string('file_name'); // Nome do arquivo
            $table->string('file_extension', 10); // Extensão do arquivo, limite de 10 caracteres
            $table->boolean('active')->default(true); // Status ativo com valor padrão true
            $table->timestamps(); // Colunas created_at e updated_at
            $table->uuid('created_by'); // ID do usuário que criou o registro

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_files');
    }
};
