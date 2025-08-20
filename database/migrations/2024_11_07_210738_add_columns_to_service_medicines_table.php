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
            $table->text('start_time')->nullable();
            $table->text('posology')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_medicines', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('posology');
        });
    }
};
