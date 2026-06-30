<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'order_date',
        'status',
        'total_price',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_price' => 'integer',
    ];

    /**
     * Relasi ke User (Customer)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Table (Meja)
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}