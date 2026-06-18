<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Menu;
use App\Livewire\Forms\MenuForm;

new class extends Component
{
    public MenuForm $form;

   #[On('edit-Menu')]
    public function editMenu($id){

        $menu = Menu::find($id);
        $this->form->setMenu($menu);
        Flux::modal('edit-menu')->show();
    }

    public function updateMenu() {
        $this->form->update();
        Flux::modal('edit-menu')->close();
        session()->flash('success', 'Menu updated successfully');
        $this->redirectRoute('menu.index', navigate: true);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->form->reset();
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $menu = Menu::find($id);
        $this->form->setMenu($menu);
        Flux::modal('delete-menu')->show();
    }

     public function deleteMenu() {
        $this->form->menu->delete();
        Flux::modal('delete-menu')->close();
        session()->flash('success', 'Menu deleted successfully');
        $this->redirectRoute('menu.index', navigate: true);
    }
};
?>

<div>
    {{--edit menu --}}
    <flux:modal 
        name="edit-menu" 
        class="md:w-150" 
        x-on:close="$wire.resetForm()" 
    >
        <form class="space-y-8" wire:submit.prevent="updateMenu">
            {{-- header --}}
            <div class="space-y-2">
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">
                    Edit Menu
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400">
                    Edit your menu details below
                </flux:text>
            </div>

            {{-- form field --}}
            <div class="space-y-6">
                <flux:input
                    label="Name"
                    placeholder="Enter menu name"
                    wire:model="form.name_menu"
                    wire:dirty.class.text-red-500
                />

                <flux:input
                    label="Description"
                    placeholder="Enter menu description"
                    wire:model="form.deskripsi_menu"
                    wire:dirty.class.text-red-500
                />
                <flux:input
                    label="Harga"
                    placeholder="Enter menu price"
                    type="number"
                    step="0.01"
                    wire:model="form.harga"
                    wire:dirty.class.text-red-500
                />
                <flux:input
                    label="Stok"
                    placeholder="Enter menu stock"
                    type="number"
                    wire:model="form.stok"
                    wire:dirty.class.text-red-500
                />  
            </div>

            <div 
                wire:show ="$dirty"
                class="text-red-500 dark:text-red-400"
            >
                you have unsaved changes
            </div>
    
            {{-- footer --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="primary" type="submit">Update</flux:button>
            </div>
                

        </form>
    </flux:modal>

    {{-- delete modal --}}

    <flux:modal 
        name="delete-menu" 
        class="md:w-150" 
        x-on:close="$wire.resetForm()" 
    >
        <form class="space-y-8" wire:submit.prevent="deleteMenu">
            {{-- header --}}
            <div class="space-y-2">
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">
                    Delete Menu
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400">
                    this action cannot be undone
                </flux:text>
            </div>

            {{-- footer --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="danger" type="submit">Delete</flux:button>
            </div>
                

        </form>
    </flux:modal>
</div>