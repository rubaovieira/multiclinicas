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
        Schema::create('outboxes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid()); // Chave primária com UUID
            $table->text('message');       // Conteúdo da mensagem
            $table->string('code');        // Código
            $table->string('from');        // Remetente
            $table->string('to');          // Destinatário
            $table->string('type');        // Tipo de mensagem
            $table->boolean('active')->default(true); // Status ativo
            $table->timestamps();          // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outboxes');
    }
};
