<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Table;
use Illuminate\Validation\Rule;

class TableForm extends Form
{
    public ?int $id = null;

    public string $number_table = '';

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
                Rule::unique('tables', 'number_table')->ignore($this->id),
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

        Table::create([
            'number_table' => $this->number_table,
            'status' => $this->status,
        ]);

        $this->reset();
    }

    public function setTable(Table $table): void
    {
        $this->table = $table;
        $this->id = $table->id;
        $this->number_table = $table->number_table;
        $this->status = $table->status;
    }

    public function update()
    {
        $this->validate();

        $this->table->update([
            'number_table' => $this->number_table,
            'status' => $this->status,
        ]);
    }
}