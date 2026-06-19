<?php

use Livewire\Component;
use App\Livewire\Forms\Detail_PesananForm;
use App\Models\Menu;
use Livewire\Attributes\Computed;
use Flux\Flux;

new class extends Component
{
    public Detail_PesananForm $form;

    #[Computed]
    public function menus()
    {
        return Menu::all();
    }

    public function save()
    {
        $this->form->save();

        Flux::modal('create-detail-pesanan')->close();

        session()->flash(
            'success',
            'Detail Pesanan created successfully'
        );

        $this->redirectRoute(
            'detail-pesanan.index',
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
    <flux:modal name="create-detail-pesanan" class="md:w-150" x-on:close="$wire.resetForm()">
        <form class="space-y-8" wire:submit.prevent="save">
            <div class="space-y-2">
                <flux:heading size="lg">
                    Create Detail Pesanan
                </flux:heading>

                <flux:text>
                    Add a new detail pesanan
                </flux:text>
            </div>

            <div class="space-y-6">

                <flux:input
                    label="Pesanan ID"
                    type="number"
                    placeholder="Enter pesanan ID"
                    wire:model="form.pesanan_id"
                />  

                <flux:select
                    label="Menu"
                    placeholder="Select menu"
                    wire:model="form.menu_id"
                >
                    <option value="">Select menu</option>
                    @foreach ($this->menus as $menu)
                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input
                    label="Jumlah"
                    type="number"
                    min="1"
                    placeholder="Enter jumlah"
                    wire:model="form.jumlah"
                />

                <flux:input
                    label="Subtotal"
                    type="number"
                    min="1"
                    placeholder="Enter subtotal"
                    wire:model="form.subtotal"
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