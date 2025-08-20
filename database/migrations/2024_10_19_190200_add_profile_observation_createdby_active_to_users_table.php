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
        Schema::table('users', function (Blueprint $table) {
            $table->string('perfil')->nullable();  
            $table->text('observation')->nullable(); 
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->boolean('active')->default(true); 
            
            // Se necessário, adicionar uma chave estrangeira para 'created_by'
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //$table->dropColumn('perfil');
            //$table->dropColumn('observation');
            //$table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->dropColumn('active');
        });
    }
};
