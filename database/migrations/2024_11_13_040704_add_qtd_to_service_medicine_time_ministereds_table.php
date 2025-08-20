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
        Schema::table('service_medicine_time_ministereds', function (Blueprint $table) {
            $table->integer('qtd')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_medicine_time_ministereds', function (Blueprint $table) {
            $table->dropColumn('qtd');
        });
    }
};
