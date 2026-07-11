<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detail_Pesanan extends Model
{
    protected $table = 'detail_pesanans';

    protected $fillable = [
        'pesanan_id',
        'menu_id',
        'jumlah',
        'subtotal',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}