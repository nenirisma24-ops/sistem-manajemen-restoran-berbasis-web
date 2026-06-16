<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryForm extends Form
{
    public string $name = '';
    public $harga = null;
    public ?Category $category = null;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->category?->id),
            ],
            'harga' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function store()
    {
        $this->validate();
        Category::create($this->only(['name', 'harga']));
        $this->reset();
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->harga = $category->harga ?? '';
    }

    // update
    public function update()
    {
        $this->validate();
        $this->category->update($this->only(['name', 'harga']));
    }
}