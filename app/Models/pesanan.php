<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'order_date',
        'status',
        'total_price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
