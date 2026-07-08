<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Menu;
use App\Livewire\Forms\MenuForm;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public MenuForm $form;
    public $image;

    #[On('edit-menu')]
    public function editMenu($id)
    {
        $menu = Menu::findOrFail($id);

        $this->form->setMenu($menu);
        $this->image = $menu->image; 

        Flux::modal('edit-menu')->show();
    }

    public function updateMenu()
    {
        $this->form->update($this->image);

        Flux::modal('edit-menu')->close();

        session()->flash(
            'success',
            'Menu updated successfully'
        );

        return $this->redirectRoute(
            'menu.index',
            navigate: true
        );
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->image = null;
        $this->form->reset();
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $menu = Menu::findOrFail($id);

        $this->form->setMenu($menu);

        Flux::modal('delete-menu')->show();
    }

    public function deleteMenu()
    {
        if ($this->form->menu->image) {
            Storage::disk('public')->delete($this->form->menu->image);
        }

        $this->form->menu->delete();

        Flux::modal('delete-menu')->close();

        session()->flash(
            'success',
            'Menu deleted successfully'
        );

        return $this->redirectRoute(
            'menu.index',
            navigate: true
        );
    }
};

?>

<div>

    <flux:modal
        name="edit-menu"
        class="md:w-150"
        x-on:close="$wire.resetForm()"
    >

        <form
            wire:submit.prevent="updateMenu"
            class="space-y-8"
        >

            <div class="space-y-2">
                <flux:heading size="lg">
                    Edit Menu
                </flux:heading>
            </div>

            <div class="space-y-6">

                <flux:input
                    label="Name"
                    wire:model="form.name"
                />

                <flux:textarea
                    label="Description"
                    wire:model="form.description"
                />

                <flux:input
                    label="Price"
                    type="number"
                    wire:model="form.price"
                />

                <flux:input
                    label="Stock"
                    type="number"
                    wire:model="form.stock"
                />

                @if ($image && is_string($image))
                    <div class="space-y-2">
                        <span class="block text-sm font-medium text-zinc-800">Current Image:</span>
                        <img src="{{ asset('storage/' . $image) }}" class="w-20 h-20 object-cover rounded-lg border border-zinc-200">
                    </div>
                @endif

                <flux:input 
                    label="Ubah Gambar Menu (Opsional)" 
                    type="file" 
                    wire:model="image" 
                    accept="image/*" 
                />

            </div>

            <div class="flex justify-end gap-3">

                <flux:modal.close>
                    <flux:button variant="outline">
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

    <flux:modal
        name="delete-menu"
        class="md:w-96"
    >

        <form
            wire:submit.prevent="deleteMenu"
            class="space-y-6"
        >

            <div>
                <flux:heading size="lg">
                    Delete Menu
                </flux:heading>

                <flux:text>
                    This action cannot be undone.
                </flux:text>
            </div>

            <div class="flex justify-end gap-3">

                <flux:modal.close>
                    <flux:button variant="outline">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    color="danger"
                    type="submit"
                >
                    Delete
                </flux:button>

            </div>

        </form>

    </flux:modal>

</div>