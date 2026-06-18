<?php

namespace App\Livewire\Forms;
use Livewire\Form;
use App\Models\Menu;
use Illuminate\Validation\Rule;

class MenuForm extends Form
{
    public string $nama_menu = '';
    public string $deskripsi_menu = '';
    public $harga = null;
    public $stok = null;
    public ?Menu $menu = null;

    public function rules(): array
    {
        return [
            'nama_menu' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('menus', 'nama_menu')->ignore($this->menu?->id),
            ],
            'deskripsi_menu' => [
                'nullable',
                'string',
            ],
            'harga' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'stok' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function store()
    {
        $this->validate();
        Menu::create([
    'nama_menu' => $this->nama_menu,
    'deskripsi_menu' => $this->deskripsi_menu,
    'harga' => $this->harga,
    'stok' => $this->stok,
]);
        $this->reset();
    }

    public function save()
    {
        if ($this->menu) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function setMenu(Menu $menu): void
    {
        $this->menu = $menu;
        $this->nama_menu = $menu->nama_menu;
        $this->deskripsi_menu = $menu->deskripsi_menu;
        $this->harga = $menu->harga;
        $this->stok = $menu->stok;
    }
           //update menu
    public function update()
    {
        $this->validate();
        $this->menu->update($this->only(['nama_menu', 'deskripsi_menu', 'harga', 'stok']));
    }

}   
