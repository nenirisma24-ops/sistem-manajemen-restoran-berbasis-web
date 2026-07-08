<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Menu;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class MenuForm extends Form
{
    public string $category_id = '';
    public string $name = '';
    public string $description = '';
    public $price = null;
    public $stock = null;
    public ?Menu $menu = null;
    public $image = null;

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('menus', 'name')->ignore($this->menu?->id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'stock' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            // Validasi fleksibel untuk mendeteksi apakah image berbentuk file upload baru
            'image' => [
                'nullable',
                request()->hasFile('image') || $this->image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? ['image', 'max:2048'] : ['nullable'],
            ],
        ];
    }

    // Tambahkan parameter $file agar bisa menerima umpan langsung dari create.blade.php
    public function store($file = null)
    {
        $this->validate();

        $imagePath = null;
        
        // Cek file dari parameter terlebih dahulu, jika kosong baru cek properti lokal
        $uploadFile = $file ?? $this->image;

        if ($uploadFile && $uploadFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $imagePath = $uploadFile->store('menus', 'public');
        }

        Menu::create([
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $imagePath,
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
        $this->category_id = $menu->category_id;
        $this->name = $menu->name;
        $this->description = $menu->description;
        $this->price = $menu->price;
        $this->stock = $menu->stock;
        $this->image = $menu->image;
    }

    // Tambahkan parameter $file agar bisa menerima umpan langsung dari edit.blade.php
    public function update($file = null)
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
        ];

        // Tentukan file mana yang akan diproses (dari parameter edit atau properti)
        $uploadFile = $file ?? $this->image;

        if ($uploadFile && $uploadFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            // Hapus gambar lama jika ada
            if ($this->menu->image) {
                Storage::disk('public')->delete($this->menu->image);
            }
            $data['image'] = $uploadFile->store('menus', 'public');
        }

        $this->menu->update($data);
    }
}