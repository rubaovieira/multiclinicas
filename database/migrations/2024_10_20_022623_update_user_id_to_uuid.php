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
        DB::table('users')->where('id', '1') // Use o ID atual que você deseja atualizar
        ->update(['id' => '6d6590d6-fd0f-4f65-aaa9-b8c1f4c7ecc6']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('id', '6d6590d6-fd0f-4f65-aaa9-b8c1f4c7ecc6') // O UUID que você inseriu
        ->update(['id' => '1']); // Use o ID original
    }
};
