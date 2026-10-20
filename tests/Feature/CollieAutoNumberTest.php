<?php

namespace Tests\Feature;

use App\Models\CollieSppb;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CollieAutoNumberTest extends TestCase
{
    use DatabaseTransactions;

    public function test_collie_sequential_numbering_from_qty1(): void
    {
        $user = User::first() ?? User::factory()->create();
        $noPl = 'TEST-AUTO-PL/' . uniqid();

        // 1. Input pertama: qty1 = 80 -> ad = 1, jucol = 80, no_col = "1-80"
        $resp1 = $this->actingAs($user)->postJson(route('collie.store'), [
            'no_pl' => $noPl,
            'code'  => 'INV095',
            'qty1'  => 80,
            'qty2'  => 21,
            'qtyr'  => 120,
        ]);

        $resp1->assertStatus(200)
              ->assertJson([
                  'success' => true,
                  'item' => [
                      'no_col' => '1-80',
                      'qty1'   => 80,
                      'qty2'   => 21,
                      'ad'     => 1,
                  ]
              ]);

        $item1 = CollieSppb::where('no_pl', $noPl)->first();
        $this->assertEquals(1, $item1->ad);
        $this->assertEquals(80, $item1->jucol);
        $this->assertEquals('1-80', $item1->no_col);

        // 2. Input kedua: qty1 = 1 -> ad = 81, jucol = 81, no_col = "81"
        $resp2 = $this->actingAs($user)->postJson(route('collie.store'), [
            'no_pl' => $noPl,
            'code'  => 'INV093',
            'qty1'  => 1,
            'qty2'  => 12,
            'qtyr'  => 10,
        ]);

        $resp2->assertStatus(200)
              ->assertJson([
                  'success' => true,
                  'item' => [
                      'no_col' => '81',
                      'qty1'   => 1,
                      'ad'     => 81,
                  ]
              ]);

        $item2 = CollieSppb::where('no_pl', $noPl)->orderByDesc('id')->first();
        $this->assertEquals(81, $item2->ad);
        $this->assertEquals(81, $item2->jucol);
        $this->assertEquals('81', $item2->no_col);

        // 3. Input ketiga: qty1 = 3 -> ad = 82, jucol = 84, no_col = "82-84"
        $resp3 = $this->actingAs($user)->postJson(route('collie.store'), [
            'no_pl' => $noPl,
            'code'  => 'INV093',
            'qty1'  => 3,
            'qty2'  => 10,
            'qtyr'  => 25,
        ]);

        $resp3->assertStatus(200)
              ->assertJson([
                  'success' => true,
                  'item' => [
                      'no_col' => '82-84',
                      'qty1'   => 3,
                      'ad'     => 82,
                  ]
              ]);

        $item3 = CollieSppb::where('no_pl', $noPl)->orderByDesc('id')->first();
        $this->assertEquals(82, $item3->ad);
        $this->assertEquals(84, $item3->jucol);
        $this->assertEquals('82-84', $item3->no_col);

        // 4. Verifikasi API getPlCollies mengembalikan last_jucol = 84
        $apiResp = $this->actingAs($user)->getJson(route('api.pl-collies', ['no_pl' => $noPl]));
        $apiResp->assertStatus(200)
                ->assertJson([
                    'success'    => true,
                    'no_pl'      => $noPl,
                    'last_jucol' => 84,
                ]);
    }
}
