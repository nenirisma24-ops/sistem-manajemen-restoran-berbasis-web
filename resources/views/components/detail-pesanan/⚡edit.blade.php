<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Detail_Pesanan;
use App\Livewire\Forms\Detai_lPesananForm;

new class extends Component
{
    public Detai_lPesananForm $form;

    #[On('edit-Detail_Pesanan')]
    public function editDetail_Pesanan($id)
    {
        $detailPesanan = Detail_Pesanan::findOrFail($id);       

        $this->form->setDetail_Pesanan($detailPesanan);

        Flux::modal('edit-detail-pesanan')->show();
    }

    public function updateDetail_Pesanan()
    {
        $this->form->update();

        Flux::modal('edit-detail-pesanan')->close();

        session()->flash(
            'success',
            'Detail Pesanan updated successfully'
        );

        return $this->redirectRoute(
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
        $detailPesanan = Detail_Pesanan::findOrFail($id);

        $this->form->setDetail_Pesanan($detailPesanan);

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

        return $this->redirectRoute(
            'detail-pesanan.index',
            navigate: true
        );
    }
};

?>

<div>
    <flux:modal name="edit-detail-pesanan" class="md:w-150" x-on:close="$wire.resetForm()">
        <form wire:submit.prevent="updateDetail_Pesanan" class="space-y-8">

            <div class="space-y-2">
                <flux:heading size="lg">
                    Edit Detail Pesanan
                </flux:heading>

                <flux:text>
                    Update the detail pesanan information
                </flux:text>
            </div>

            <div class="space-y-6">

                <flux:input
                    label="Pesanan ID"
                    type="number"
                    wire:model="form.pesanan_id"
                />

                <flux:input
                    label="Jumlah"
                    type="number"
                    wire:model="form.jumlah"
                />

                <flux:input
                    label="Subtotal"
                    type="number"
                    wire:model="form.subtotal"  
                />

            </div>      

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="primary" type="submit">Update</flux:button>
            </div>

        </form>
    </flux:modal>
</div>