<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;
use App\Models\Table;
use App\Livewire\Forms\TableForm;

new class extends Component
{
    public TableForm $form;

    #[On('edit-table')]
    public function editTable($id)
    {
        $table = Table::findOrFail($id);

        $this->form->setTable($table);

        Flux::modal('edit-table')->show();
    }

    public function updateTable()
    {
        $this->form->update();

        Flux::modal('edit-table')->close();

        session()->flash('success', 'Table updated successfully');

        return $this->redirectRoute('table.index', navigate: true);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->form->reset();
    }

    #[On('delete-table')]
    public function confirmDelete($id)
    {
        $table = Table::findOrFail($id);

        $this->form->setTable($table);

        Flux::modal('delete-table')->show();
    }

    public function deleteTable()
    {
        $this->form->table->delete();

        Flux::modal('delete-table')->close();

        session()->flash('success', 'Table deleted successfully');

        return $this->redirectRoute('table.index', navigate: true);
    }
};

?>

<div>

    {{-- Edit Modal --}}
    <flux:modal
        name="edit-table"
        class="md:w-[500px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="updateTable" class="space-y-6">

            <div>
                <flux:heading size="lg">
                    Edit Table
                </flux:heading>

                <flux:text>
                    Update table information.
                </flux:text>
            </div>

            <flux:input
                label="Table Number"
                placeholder="Enter table number"
                wire:model="form.number_table"
            />

            <div class="flex justify-end gap-2">

                <flux:modal.close>
                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="submit"
                    variant="primary"
                >
                    Update
                </flux:button>

            </div>

        </form>

    </flux:modal>


    {{-- Delete Modal --}}
    <flux:modal
        name="delete-table"
        class="md:w-[450px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="deleteTable" class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Delete Table
                </flux:heading>

                <flux:text>
                    This action cannot be undone.
                </flux:text>

            </div>

            <div class="flex justify-end gap-2">

                <flux:modal.close>
                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="submit"
                    variant="danger"
                >
                    Delete
                </flux:button>

            </div>

        </form>

    </flux:modal>

</div>