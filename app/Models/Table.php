<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'id',
        'number_table',
        'status',
    ];

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }
}
