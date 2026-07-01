<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Livewire\Forms\DetailPesananForm; // Pastikan Anda membuat Form Object ini

new class extends Component
{
    public DetailPesananForm $form;

    #[On('edit-detail-pesanan')]
    public function editDetailPesanan($id)
    {
        $detail = DetailPesanan::findOrFail($id);

        $this->form->setDetailPesanan($detail);

        Flux::modal('edit-detail-pesanan')->show();
    }

    public function updateDetailPesanan()
    {
        $this->form->update();

        Flux::modal('edit-detail-pesanan')->close();

        session()->flash('success', 'Detail pesanan berhasil diperbarui');

        // Mengarahkan kembali ke halaman detail pesanan utama
        return $this->redirectRoute(
            'pesanan.show', 
            ['pesanan' => $this->form->pesanan_id], 
            navigate: true
        );
    }

    public function resetForm()
    {
        $this->resetValidation();

        if (isset($this->form)) {
            $this->form->reset();
            $this->form->quantity = 1;
            $this->form->notes = '';
        }
    }

    #[On('delete-detail-pesanan')]
    public function confirmDelete($id)
    {
        $detail = DetailPesanan::findOrFail($id);

        $this->form->setDetailPesanan($detail);

        Flux::modal('delete-detail-pesanan')->show();
    }

    public function deleteDetailPesanan()
    {
        $pesananId = $this->form->pesanan_id;
        
        $this->form->detailPesanan->delete();

        Flux::modal('delete-detail-pesanan')->close();

        session()->flash('success', 'Menu dari pesanan berhasil dihapus');

        return $this->redirectRoute(
            'pesanan.show',
            ['pesanan' => $pesananId],
            navigate: true
        );
    }

    public function menus()
    {
        return Menu::orderBy('name')->get();
    }
};

?>

<div>

    {{-- Edit Detail Modal --}}
    <flux:modal
        name="edit-detail-pesanan"
        class="md:w-[500px]"
        x-on:close="$wire.resetForm()"
    >
        <form wire:submit="updateDetailPesanan" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    Edit Detail Pesanan
                </flux:heading>

                <flux:text>
                    Ubah menu, kuantitas, atau catatan untuk item pesanan ini.
                </flux:text>
            </div>

            {{-- Pilihan Menu --}}
            <flux:select
                label="Menu / Produk"
                wire:model="form.menu_id"
            >
                @foreach($this->menus() as $menu)
                    <option value="{{ $menu->id }}">
                        {{ $menu->name }} - Rp{{ number_format($menu->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </flux:select>

            {{-- Kuantitas / Jumlah --}}
            <flux:input
                type="number"
                label="Kuantitas"
                min="1"
                wire:model="form.quantity"
            />

            {{-- Catatan Opsional --}}
            <flux:input
                type="text"
                label="Catatan (Misal: Pedas, Tanpa Es)"
                wire:model="form.notes"
            />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">
                        Batal
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="submit"
                    variant="primary"
                >
                    Simpan Perubahan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Detail Modal --}}
    <flux:modal
        name="delete-detail-pesanan"
        class="md:w-[450px]"
        x-on:close="$wire.resetForm()"
    >
        <form wire:submit="deleteDetailPesanan" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    Hapus Item Pesanan
                </flux:heading>

                <flux:text>
                    Apakah Anda yakin ingin menghapus item menu ini dari daftar pesanan? Tindakan ini tidak bisa dibatalkan.
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">
                        Batal
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="submit"
                    variant="danger"
                >
                    Hapus Item
                </flux:button>
            </div>
        </form>
    </flux:modal>

</div>