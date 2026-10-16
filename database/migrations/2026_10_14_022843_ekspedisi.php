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
        Schema::create('ekspedisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
        });

        \Illuminate\Support\Facades\DB::table('ekspedisi')->insert([
            ['id' => 1, 'nama' => 'Herona Express'],
            ['id' => 2, 'nama' => 'Otto'],
            ['id' => 3, 'nama' => 'Manggala Kiat Ananda'],
            ['id' => 4, 'nama' => 'Tunggal Jaya Expressindo'],
            ['id' => 5, 'nama' => 'Others'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekspedisi');
    }
};
