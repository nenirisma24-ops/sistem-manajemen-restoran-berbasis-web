<?php

use Livewire\Component;
use App\Livewire\Forms\Detail_PesananForm;
use App\Models\Menu;
use Flux\Flux;

new class extends Component
{
    public Detail_PesananForm $form;

    public $menus = [];

    public function mount()
    {
        $this->form = new Detail_PesananForm($this, 'form');

        $this->menus = Menu::all();
    }

    public function updated($property)
{
    if (
        in_array($property, [
            'form.menu_id',
            'form.jumlah',
        ])
    ) {
        $this->calculateSubtotal();
    }
}

    private function calculateSubtotal()
    {
        $menu = Menu::find($this->form->menu_id);

        $this->form->subtotal = $menu
            ? ($menu->harga * $this->form->jumlah)
            : 0;
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
        $this->form->reset();
    }
};
?>

<div>
    <flux:modal name="create-detail-pesanan" class="md:w-150">
        <form wire:submit="save" class="space-y-8">

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
                    wire:model="form.pesanan_id"
                />

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Menu
                    </label>

                    <select
                        wire:model.live="form.menu_id"
                        class="w-full rounded-lg border px-3 py-2"
                    >
                        <option value="">
                            Pilih Menu
                        </option>

                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}">
                                {{ $menu->nama_menu }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <flux:input
                    label="Jumlah"
                    type="number"
                    wire:model.live="form.jumlah"
                />

                <flux:input
                    label="Subtotal"
                    type="number"
                    wire:model="form.subtotal"
                    readonly
                />

            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <flux:modal.close>
                    <flux:button variant="outline">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    variant="primary"
                    type="submit">
                    Create
                </flux:button>
            </div>

        </form>
    </flux:modal>
</div>