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
        Schema::table('collie_sppbs', function (Blueprint $table) {
            // Kolom untuk menyimpan ID asli dari data legacy (SQL Server / CSV)
            $table->integer('old_id')->nullable()->index()->after('id')
                  ->comment('ID asli dari data legacy VB6 / SQL Server');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collie_sppbs', function (Blueprint $table) {
            $table->dropColumn('old_id');
        });
    }
};
