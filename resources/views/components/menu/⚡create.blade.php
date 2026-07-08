<?php

use Livewire\Component;
use Flux\Flux;
use App\Livewire\Forms\MenuForm;
use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public MenuForm $form;

    public $image;

    #[Computed]
    public function categories()
    {
        return Category::all();
    }

    public function save()
    {
        $this->form->store($this->image);

        Flux::modal('create-menu')->close();

        session()->flash(
            'success',
            'Menu created successfully'
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

        // Periksa apakah properti $form sudah diinisialisasi
        if (isset($this->form)) {
            $this->form->reset();
        }
    }
};

?>

<div>
    <flux:modal name="create-menu" class="md:w-150" x-on:close="$wire.resetForm()">

        <form class="space-y-8" wire:submit.prevent="save">

            <div class="space-y-2">
                <flux:heading size="lg">
                    Create Menu
                </flux:heading>

                <flux:text>
                    Add a new menu item
                </flux:text>
            </div>

            <div class="space-y-6">

                <flux:input label="Name" wire:model="form.name" />

                <flux:select label="Category" wire:model="form.category_id" placeholder="Choose category...">
                    @foreach ($this->categories as $category)
                        <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:textarea label="Description" wire:model="form.description" />

                <flux:input label="Price" type="number" step="0.01" wire:model="form.price" />

                <flux:input label="Stock" type="number" wire:model="form.stock" />

                <label class="block text-sm font-medium text-zinc-700">Gambar Menu</label>
                <input
                    type="file"
                    wire:model="image"
                    accept="image/png,image/jpeg,image/jpg,image/webp,image/gif"
                    class="block w-full text-sm text-zinc-700 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-zinc-700 hover:file:bg-zinc-200"
                />
                
            </div>

            <div class="flex justify-end gap-3">

                <flux:modal.close>
                    <flux:button variant="outline">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    Create
                </flux:button>

            </div>

        </form>

    </flux:modal>
</div>