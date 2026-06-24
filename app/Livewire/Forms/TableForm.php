<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Table;
use Iluminate\Validation\Rule;

class TableForm extends Form
{
    public string $nomer_table;
    public string $status = 'tersedia';
    public ?Table $table = null;
}
