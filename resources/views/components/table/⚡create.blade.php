<?php

use Livewire\Component;
use Flux\Flux;
use App\Livewire\Forms\TableForm;

new class extends Component
{
    // instance class table form
    public TableForm $form;

    public function save()
    {
        $this->form->store();

        Flux::modal('create-table')->close();

        session()->flash(
            'success',
            'Table created successfully'
        );

        return $this->redirectRoute(
            'table.index',
            navigate: true
        );
    }

    public function resetForm()
    {
        $this->resetValidation();

        // Periksa apakah properti $form sudah diinisialisasi
        if (isset($this->form)) {
            $this->form->reset();
        }
    }
    
};
?>

<div>
    <flux:modal name="create-table" class="md:w-150" x-on:close="$wire.resetForm()">

        <form class="space-y-8" wire:submit.prevent="save">

            <div class="space-y-2">
                <flux:heading size="lg">
                    Create Table
                </flux:heading>

                <flux:text>
                    Add a new table to your account
                </flux:text>
            </div>

            <div class="space-y-6">
                <flux:input
                    label="Table Number"
                    placeholder="Enter table number"
                    wire:model="form.number_table"
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="primary" type="submit">Create</flux:button>
            </div>
        </form>
    </flux:modal>
</div>