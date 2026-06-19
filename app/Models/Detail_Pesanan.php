<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;

class Detail_Pesanan extends Model
{
    protected $table = 'detail_pesanans';

    protected $fillable = [
        'pesanan_id',
        'name',
        'jumlah',
        'subtotal',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'name');
    }
}