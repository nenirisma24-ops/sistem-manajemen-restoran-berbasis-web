<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
       protected $fillable = [
        'kategori_menu',
        'deskripsi_menu',
        'nama_menu',
        'harga',
        'stok',
        ];
}
