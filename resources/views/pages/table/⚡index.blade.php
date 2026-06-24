<?php

use Livewire\Component;
use livewire\WithPagination;
use App\Models\Table;

new class extends Component
{
    use WithPagination;
    #[Computed ]
    public function getTablesProperty()
    {
        return Table::paginate(10);
    }
    public function edit($id)
    {
        return redirect()->route('table.edit', $id);
    }
};
?>

<div>
    {{-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi --}}
</div>