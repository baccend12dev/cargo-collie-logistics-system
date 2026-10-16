<?php

namespace Tests\Feature;

use Tests\TestCase;

class ItemStockTest extends TestCase
{
    /**
     * Test lookup data barang dari master stock view berdasarkan kode barang
     */
    public function test_can_lookup_item_by_code(): void
    {
        $response = $this->getJson('/api/item-stock/INV095');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'item' => [
                         'item_code' => 'INV095',
                         'item_desc' => 'PANTERA SERBUK INJEKSI STERIL 40 MG VIAL 10 ML TIN',
                     ]
                 ]);
    }

    /**
     * Test response jika kode barang tidak ditemukan
     */
    public function test_returns_404_when_item_not_found(): void
    {
        $response = $this->getJson('/api/item-stock/KODETIDAKADA999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * Test data koli difilter berdasarkan no_pl dan tanggal tgl_col
     */
    public function test_pl_collies_returns_empty_when_no_items_on_selected_date(): void
    {
        // Tanggal yang tidak memiliki data untuk 001/PL harus mengembalikan array kosong
        $response = $this->getJson('/api/pl-collies?no_pl=001/PL&tgl_col=2026-10-12');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'no_pl'   => '001/PL',
                     'items'   => []
                 ]);
    }

    /**
     * Test data koli ditemukan saat tanggal dan no_pl cocok
     */
    public function test_pl_collies_returns_items_when_matching_date(): void
    {
        // 001/PL ada pada tanggal 2026-10-06
        $response = $this->getJson('/api/pl-collies?no_pl=001/PL&tgl_col=2026-10-06');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'no_pl'   => '001/PL',
                 ]);

        $this->assertNotEmpty($response->json('items'));
    }

}
