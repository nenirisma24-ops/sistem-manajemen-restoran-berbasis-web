<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Detail_Pesanan;
use App\Models\Menu;

class Detail_PesananForm extends Form
{
    public $pesanan_id;
    public $menu_id;
    public $jumlah = 1;
    public $subtotal = 0;

    public ?Detail_Pesanan $detailPesanan = null;

    public function rules()
    {
        return [
            'pesanan_id' => ['required'],
            'menu_id' => ['required'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ];
    }

    public function save()
    {
        if ($this->detailPesanan) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store()
    {
        $this->validate();

        $menu = Menu::find($this->menu_id);

        Detail_Pesanan::create([
            'pesanan_id' => $this->pesanan_id,
            'menu_id' => $this->menu_id,
            'jumlah' => $this->jumlah,
            'subtotal' => $menu->harga * $this->jumlah,
        ]);

        $this->reset();
    }

    public function setDetailPesanan(Detail_Pesanan $detailPesanan)
    {
        $this->detailPesanan = $detailPesanan;

        $this->pesanan_id = $detailPesanan->pesanan_id;
        $this->menu_id = $detailPesanan->menu_id;
        $this->jumlah = $detailPesanan->jumlah;
        $this->subtotal = $detailPesanan->subtotal;
    }

    public function update()
    {
        $this->validate();

        $menu = Menu::find($this->menu_id);

        $this->detailPesanan->update([
            'pesanan_id' => $this->pesanan_id,
            'menu_id' => $this->menu_id,
            'jumlah' => $this->jumlah,
            'subtotal' => $menu->harga * $this->jumlah,
        ]);

        $this->reset();
    }
}