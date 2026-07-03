<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;
use App\Models\Detail_Pesanan;
use App\Models\Pesanan;
use App\Models\Menu;
use App\Livewire\Forms\Detail_PesananForm;

new class extends Component
{
    public Detail_PesananForm $form;

    public $selectedDetailId;

    #[On('edit-detail-pesanan')]
    public function editDetailPesanan($id)
    {
        $this->selectedDetailId = $id;
        $detail = Detail_Pesanan::findOrFail($id);

        $this->form->setDetailPesanan($detail);

        Flux::modal('edit-detail-pesanan')->show();
    }

    public function updateDetailPesanan()
    {
        $this->form->update();

        Flux::modal('edit-detail-pesanan')->close();

        $this->dispatch('refresh-detail-pesanans');

        session()->flash('success', 'Detail pesanan updated successfully');
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->selectedDetailId = null;

        if (isset($this->form)) {
            $this->form->reset();
        }
    }

    #[On('confirm-delete-detail')]
    public function confirmDeleteDetail($id)
    {
        $this->selectedDetailId = $id;
        $detail = Detail_Pesanan::findOrFail($id);

        $this->form->setDetailPesanan($detail);

        Flux::modal('delete-detail-pesanan')->show();
    }

    public function deleteDetailPesanan()
    {
       
        if ($this->selectedDetailId) {
            Detail_Pesanan::destroy($this->selectedDetailId);
        }

        Flux::modal('delete-detail-pesanan')->close();

        $this->dispatch('refresh-detail-pesanans');
        $this->resetForm();

        session()->flash('success', 'Detail pesanan deleted successfully');
    }

    public function pesanans()
    {
        return Pesanan::orderBy('id')->get();
    }

    public function menus()
    {
        return Menu::orderBy('name')->get();
    }
};

?>

<div>

    {{-- Edit Modal --}}
    <flux:modal
        name="edit-detail-pesanan"
        class="md:w-[600px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="updateDetailPesanan" class="space-y-6">

            <div>
                <flux:heading size="lg">
                    Edit Detail Pesanan
                </flux:heading>

                <flux:text>
                    Update detail pesanan information.
                </flux:text>
            </div>

            <flux:select
                label="Pesanan"
                wire:model="form.pesanan_id"
            >
                <option value="">-- Pilih Pesanan --</option>
                @foreach($this->pesanans() as $pesanan)
                    <option value="{{ $pesanan->id }}">
                        Pesanan #{{ $pesanan->id }} - {{ $pesanan->user->name ?? 'Unknown' }}
                    </option>
                @endforeach
            </flux:select>

            <flux:select
                label="Menu"
                wire:model="form.menu_id"
            >
                <option value="">-- Pilih Menu --</option>
                @foreach($this->menus() as $menu)
                    <option value="{{ $menu->id }}">
                        {{ $menu->name }} - Rp{{ number_format($menu->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </flux:select>

            <flux:input
                type="number"
                label="Jumlah"
                min="1"
                wire:model="form.jumlah"
            />

            <flux:input
                type="number"
                label="Subtotal"
                wire:model="form.subtotal"
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
        name="delete-detail-pesanan"
        class="md:w-[450px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="deleteDetailPesanan" class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Delete Detail Pesanan
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