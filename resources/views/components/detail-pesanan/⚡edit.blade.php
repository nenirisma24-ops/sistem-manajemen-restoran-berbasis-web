<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Menu;
use App\Models\Detail_Pesanan;
use App\Livewire\Forms\DetailPesananForm;
use Flux\Flux;

new class extends Component
{
    public Detai_lPesananForm $form;

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

    #[On('edit-Detail_Pesanan')]
    public function editMenu($id)
    {
        $detail_pesanan = Detail_Pesanan::find($id);

        if (!$detail_pesanan) {
            return;
        }

        $this->form->setDetailPesanan($detail_pesanan);

        Flux::modal('edit-detail-pesanan')->show();
    }

    public function updateMenu()
    {
        $this->form->update();

        Flux::modal('edit-detail-pesanan')->close();

        session()->flash(
            'success',
            'Detail Pesanan updated successfully'
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

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $detail_pesanan = Detail_Pesanan::find($id);

        if (!$detail_pesanan) {
            return;
        }

        $this->form->setDetailPesanan($detail_pesanan);

        Flux::modal('delete-detail-pesanan')->show();
    }

    public function deleteDetail_Pesanan()
    {
        $this->form->detailPesanan->delete();

        Flux::modal('delete-detail-pesanan')->close();

        session()->flash(
            'success',
            'Detail Pesanan deleted successfully'
        );

        $this->redirectRoute(
            'detail-pesanan.index',
            navigate: true
        );
    }
};
?>

<div>

    {{-- Edit Modal --}}
    <flux:modal
        name="edit-detail-pesanan"
        class="md:w-150"
        x-on:close="$wire.resetForm()"
    >

        <form class="space-y-8" wire:submit.prevent="updateMenu">

            <div class="space-y-2">
                <flux:heading size="lg">
                    Edit Detail Pesanan
                </flux:heading>

                <flux:text>
                    Edit detail pesanan
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

                        @foreach(Menu::all() as $menu)
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
                    Update
                </flux:button>
            </div>

        </form>

    </flux:modal>

    {{-- Delete Modal --}}
    <flux:modal
        name="delete-detail-pesanan"
        class="md:w-150"
        x-on:close="$wire.resetForm()"
    >

        <form
            class="space-y-8"
            wire:submit.prevent="deleteDetail_Pesanan"
        >

            <div class="space-y-2">
                <flux:heading size="lg">
                    Delete Detail Pesanan
                </flux:heading>

                <flux:text>
                    This action cannot be undone
                </flux:text>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <flux:modal.close>
                    <flux:button variant="outline">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    variant="danger"
                    type="submit">
                    Delete
                </flux:button>
            </div>

        </form>

    </flux:modal>

</div>