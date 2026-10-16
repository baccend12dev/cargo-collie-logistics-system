<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollieSppb extends Model
{
    protected $table = 'collie_sppbs';

    protected $guarded = ['id'];

    protected $casts = [
        'tgl_col'       => 'date',
        'tgl_sppb'      => 'date',
        'creation_date' => 'datetime',
        'qtyr'          => 'decimal:2',
        'berat'         => 'decimal:2',
        'qty'           => 'integer',
        'qty1'          => 'integer',
        'qty2'          => 'integer',
        'ad'            => 'integer',
        'jucol'         => 'integer',
        'jumlah'        => 'integer',
    ];

    /**
     * Scope untuk data collie yang belum memiliki SPPB
     */
    public function scopeBelumSppb($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('no_sppb')->orWhere('no_sppb', '');
        });
    }

    /**
     * Scope untuk data collie yang sudah memiliki SPPB
     */
    public function scopeSudahSppb($query)
    {
        return $query->whereNotNull('no_sppb')->where('no_sppb', '!=', '');
    }

    /**
     * Relasi ke Master Stock (otto_master_stock_v)
     */
    public function masterStock()
    {
        return $this->hasOne(OttoMasterStock::class, 'item_code', 'code')
            ->where('jenis_bhn', 'FG');
    }

    /**
     * Accessor untuk nama barang (item_desc)
     */
    public function getItemDescAttribute()
    {
        if (empty($this->code)) {
            return '-';
        }
        if ($this->relationLoaded('masterStock') && $this->masterStock) {
            return $this->masterStock->item_desc;
        }
        if (isset($this->attributes['item_desc']) && $this->attributes['item_desc']) {
            return $this->attributes['item_desc'];
        }
        return OttoMasterStock::findByCode($this->code)?->item_desc ?? '-';
    }

    /**
     * Boot model untuk perhitungan otomatis nomor collie & QTY jika AD dan JUCOL tersedia
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            // Format no_col otomatis jika ad & jucol terisi
            if (!empty($item->ad) && !empty($item->jucol)) {
                if ($item->ad == $item->jucol) {
                    $item->no_col = (string) $item->ad;
                } else {
                    $item->no_col = $item->ad . '-' . $item->jucol;
                }

                // Kalkulasi otomatis QTY1 = jucol - ad + 1 jika belum diisi
                if (empty($item->qty1)) {
                    $item->qty1 = ($item->jucol >= $item->ad) ? ($item->jucol - $item->ad + 1) : 1;
                }
            }

            // Kalkulasi otomatis QTY = QTY1 * QTY2
            if (!empty($item->qty1) && !empty($item->qty2)) {
                $item->qty = $item->qty1 * $item->qty2;
            }
        });
    }
}
