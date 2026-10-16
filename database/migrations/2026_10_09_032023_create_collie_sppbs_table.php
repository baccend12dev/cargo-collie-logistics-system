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
        Schema::create('collie_sppbs', function (Blueprint $table) {
            $table->id();
            
            // Kolom Data Collie / Packing List
            $table->string('no_pl', 50)->nullable()->index();      // NO_PL (contoh: 001/PL)
            $table->string('no_sm', 100)->nullable()->index();     // NO_SM (contoh: 2601027/JKT/OE/00)
            $table->string('no_col', 50)->nullable();              // NO_COL (contoh: 1-299, 300)
            $table->date('tgl_col')->nullable();                   // TGL_COL
            $table->string('cabang', 50)->nullable();              // CABANG (contoh: JKT0)
            $table->string('code', 50)->nullable()->index();       // CODE produk (contoh: INV087)
            $table->integer('qty')->nullable();                    // QTY (total qty: qty1 * qty2)
            $table->integer('qty1')->nullable();                   // QTY1 (koli/box)
            $table->integer('qty2')->nullable();                   // QTY2 (isi per koli)
            $table->decimal('qtyr', 10, 2)->nullable();            // QTYR (berat kg baris)
            $table->integer('ad')->nullable();                     // AD (nomor collie awal)
            $table->integer('jucol')->nullable();                  // jucol (nomor collie akhir)

            // Kolom Data SPPB & Pengiriman (diupdate saat proses SPPB memilih NO_SM)
            $table->string('ekspedisi', 150)->nullable();          // ekspedisi
            $table->string('no_sppb', 100)->nullable()->index();   // no_sppb (contoh: 0323/10/2026)
            $table->date('tgl_sppb')->nullable();                  // tgl_sppb
            $table->string('customer_name', 200)->nullable();      // customer_name
            $table->text('customer_address')->nullable();          // customer_address
            $table->dateTime('creation_date')->nullable();         // creation_date asli dari legacy
            $table->string('supirnopol', 150)->nullable();         // supirnopol (contoh: DEDI/B 9870 PCL/7576)
            $table->text('cat')->nullable();                       // cat (catatan)
            $table->integer('jumlah')->nullable();                 // Jumlah (total koli per SPPB/PL)
            $table->decimal('berat', 10, 2)->nullable();           // Berat (total berat kg per SPPB/PL)
            $table->string('cab', 50)->nullable();                 // cab

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collie_sppbs');
    }
};
