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
        Schema::create('user_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_config_schedule_id');
            $table->text('day');
            $table->text('start');
            $table->text('end');
            $table->text('turn');
            $table->boolean('active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('user_config_schedule_id')
                  ->references('id')
                  ->on('user_config_schedules')
                  ->onDelete('cascade');
                  
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_schedules');
    }
};
