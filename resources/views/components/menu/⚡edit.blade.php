<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Menu;
use App\Livewire\Forms\MenuForm;

new class extends Component
{
    public MenuForm $form;

    #[On('edit-menu')]
    public function editMenu($id)
    {
        $menu = Menu::findOrFail($id);

        $this->form->setMenu($menu);

        Flux::modal('edit-menu')->show();
    }

    public function updateMenu()
    {
        $this->form->update();

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

    {{-- EDIT MODAL --}}
    <flux:modal
        name="edit-menu"
        class="md:w-150"
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

    {{-- DELETE MODAL --}}
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