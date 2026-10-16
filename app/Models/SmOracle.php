<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmOracle extends Model
{
    /**
     * View Oracle/Postgres Picklist Trx
     */
    protected $table = 'otto_collie_trx_picklist2_v';

    /**
     * Nonaktifkan timestamps bawaan Laravel
     */
    public $timestamps = false;

    /**
     * Disable guarded
     */
    protected $guarded = [];
}
