<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memperlebar kolom-kolom yang terlalu pendek untuk data legacy CSV.
     */
    public function up(): void
    {
        // Ubah tipe kolom menggunakan raw SQL untuk PostgreSQL
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN no_pl TYPE VARCHAR(100)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN no_sm TYPE VARCHAR(150)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN no_col TYPE VARCHAR(150)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN cabang TYPE VARCHAR(100)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN code TYPE VARCHAR(100)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN ekspedisi TYPE VARCHAR(200)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN no_sppb TYPE VARCHAR(150)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN supirnopol TYPE VARCHAR(250)');
        DB::statement('ALTER TABLE collie_sppbs ALTER COLUMN cab TYPE VARCHAR(100)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu memperkecil balik (bisa menyebabkan data terpotong)
    }
};
