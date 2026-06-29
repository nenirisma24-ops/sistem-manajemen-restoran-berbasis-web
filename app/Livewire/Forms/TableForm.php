<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Table;
use Illuminate\Validation\Rule;

class TableForm extends Form
{
    public string $number_table;
    public string $status = 'tersedia';
    public ?Table $table = null;

    public function rules(): array
    {
        return [
            'id' => [
                'nullable',
                'integer',
            ],

            'number_table' => [
                'required',
                'string',
                'min:1',
                'max:255',
                Rule::unique('tables', 'number_table')->ignore($this->table?->id),
            ],
            'status' => [
                'required',
                'string',
                'in:tersedia,tidak tersedia',
            ],
        ];
    }

    public function store()
    {
        $this->validate();
        Table::create($this->only(['number_table', 'status']));
        $this->reset();
    }

    public function setTable(Table $table): void
    {
        $this->table = $table;
        $this->number_table = $table->number_table;
        $this->status = $table->status;
    }

    // update
    public function update()
    {
        $this->validate();
        $this->table->update($this->only(['number_table', 'status']));
    }
}