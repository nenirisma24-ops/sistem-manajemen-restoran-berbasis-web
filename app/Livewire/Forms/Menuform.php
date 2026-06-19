<?php

namespace App\Livewire\Forms;
use Livewire\Form;
use App\Models\Menu;
use Illuminate\Validation\Rule;

class MenuForm extends Form
{
    public string $category_id = '';
    public string $name = '';
    public string $description = '';
    public $price = null;
    public $stock = null;
    public ?Menu $menu = null;

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
        ];
    }

    public function store()
    {
        dd($this->validate());

        $this->validate();
        Menu::create([
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
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
    }
           //update menu
    public function update()
    {
        $this->validate();
        $this->menu->update($this->only(['category_id', 'name', 'description', 'price', 'stock']));
    }

}   
