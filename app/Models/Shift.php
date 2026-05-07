<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'opening_cash',
        'closing_cash',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ShiftExpense::class);
    }

    /**
     * Cek apakah shift masih aktif (belum ditutup).
     */
    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }

    /**
     * Total pendapatan dalam shift.
     */
    public function getTotalRevenueAttribute(): int
    {
        return $this->transactions()->sum('total');
    }

    /**
     * Total transaksi dalam shift.
     */
    public function getTotalTransactionsAttribute(): int
    {
        return $this->transactions()->count();
    }
}
