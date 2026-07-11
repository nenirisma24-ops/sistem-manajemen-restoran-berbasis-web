<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'order_date',
        'status',
        'total_price'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(Detail_Pesanan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}