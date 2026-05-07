<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'nama',
        'harga',
        'stok',
        'kategori_id',
        'gambar',
    ];

    /**
     * Produk milik satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    /**
     * Cek apakah stok rendah (< 5).
     */
    public function isLowStock(): bool
    {
        return $this->stok < 5;
    }
}
