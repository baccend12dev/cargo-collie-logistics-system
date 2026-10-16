<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OttoMasterStock extends Model
{
    /**
     * Nama tabel / view database PostgreSQL
     */
    protected $table = 'otto_master_stock_v';

    /**
     * Primary key
     */
    protected $primaryKey = 'item_code';

    /**
     * Primary key bertipe string dan bukan auto-increment
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * View tidak menggunakan timestamps created_at / updated_at default Laravel
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi / diakses
     */
    protected $fillable = [
        'item_code',
        'ext',
        'item_desc',
        'uom',
        'anm',
        'jenis_bhn',
    ];

    /**
     * Scope untuk produk berstatus Active
     */
    public function scopeActive($query)
    {
        return $query->where('anm', 'Active');
    }

    /**
     * Scope untuk jenis bahan FG (Finished Goods / Barang Jadi)
     */
    public function scopeFinishedGoods($query)
    {
        return $query->where('jenis_bhn', 'FG');
    }

    /**
     * Helper untuk mencari barang berdasarkan item_code.
     * Bersifat case-insensitive, memprioritaskan jenis_bhn = 'FG' lalu status 'Active'.
     *
     * @param string|null $code
     * @return self|null
     */
    public static function findByCode(?string $code): ?self
    {
        $code = trim($code ?? '');
        if ($code === '') {
            return null;
        }

        return static::whereRaw('UPPER(TRIM(item_code)) = ?', [strtoupper($code)])
            ->orderByRaw("CASE WHEN jenis_bhn = 'FG' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE WHEN anm = 'Active' THEN 1 ELSE 2 END")
            ->first();
    }
}
