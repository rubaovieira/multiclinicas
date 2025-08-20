<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('medico_id');
            $table->uuid('paciente_id');
            $table->timestamp('data_hora_inicio');
            $table->timestamp('data_hora_fim')->nullable();
            $table->string('tipo', 20);
            $table->string('status', 20);
            $table->text('link_telemedicina')->nullable();
            $table->text('nome_sala')->nullable();
            $table->timestamp('sala_expira_em')->nullable();
            $table->timestamps();

            $table->foreign('medico_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('paciente_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}; 