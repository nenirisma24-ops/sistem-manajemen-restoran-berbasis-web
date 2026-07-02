<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'payment_method',
        'payment_total',
        'payment_date',
        'payment_status',
    ];

    protected $casts = [
        'payment_total' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Relasi ke Pesanan
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}