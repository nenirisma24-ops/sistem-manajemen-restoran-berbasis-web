<?php

use Livewire\Component;
use App\Livewire\Forms\MenuForm;

new class extends Component
{
    public MenuForm $form;

    public function save()
    {
        $this->form->store();

        Flux::modal('create-menu')->close();

        session()->flash(
            'success',
            'Menu created successfully'
        );

        return $this->redirectRoute(
            'menus.index',
            navigate: true
        );
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->form->reset();
    }
};

?>

<div>
    <flux:modal
        name="create-menu"
        class="md:w-150"
        x-on:close="$wire.resetForm()"
    >

        <form
            class="space-y-8"
            wire:submit.prevent="save"
        >

            <div class="space-y-2">
                <flux:heading size="lg">
                    Create Menu
                </flux:heading>

                <flux:text>
                    Add a new menu item
                </flux:text>
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
                    step="0.01"
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
                    Create
                </flux:button>

            </div>

        </form>

    </flux:modal>
</div>