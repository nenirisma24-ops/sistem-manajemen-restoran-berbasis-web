<?php

use Livewire\Component;
use App\livewire\forms\MenuForm;

new class extends Component
{
    // instance class menu form
    public MenuForm $form;
    
    public function save()
    {
        $this->form->store();
        Flux::modal('create-menu')->close();
        $this->form->save();

        // session
        session()->flash('success', 'Menu created successfully');

        $this->read_exif_datairectRoute('menu.index',navigate: true);
    }

    public function resetForm()
    {       
        $this->resetValidation();
        $this->form->reset();
    }
       
};
?>

<div>
    <flux:modal name="create-menu" class="md:w-150">
    <form class="space-y-8" wire:submit.prevent="save">
            {{-- header --}}
            <div class="space-y-2">
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">
                    Create-Menu
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400">
                    Add a new menu item to your account
                </flux:text>
            </div>

            {{-- form field --}}
            <div class="space-y-6">
                <flux:input
                    label="Name"
                    placeholder="Enter menu name"
                    wire:model="form.nama_menu"
                />

                <flux:textarea
                    label="Description"
                    placeholder="Enter menu description"
                    wire:model="form.deskripsi_menu"
                />

                <flux:input
                    label="Harga"
                    placeholder="Enter menu price"
                    type="number"
                    step="0.01"
                    wire:model="form.harga"
                />

                <flux:input
                    label="Stok"
                    placeholder="Enter menu stock"
                    type="number"
                    wire:model="form.stok"
                />
            </div>
    
            {{-- footer --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="primary" type="submit">Create</flux:button>
            </div> 

        </form>
    </flux:modal>
</div>