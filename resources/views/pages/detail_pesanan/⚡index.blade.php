<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Detail_Pesanan;

new class extends Component
{
    use WithPagination;
    
    #[Computed]
    public function detailPesanans()
    {
        return Detail_Pesanan::latest()->paginate(10);
    }       
    
    
    public function edit($id)
    {
        $this->dispatch('edit-detail-pesanan', id: $id);
    }
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl">Detail Pesanan</flux:heading>
    <flux:subheading size="lg">
        Manage your detail pesanans
    </flux:subheading>

    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-detail-pesanan">
        <flux:button
            variant="primary"
            icon="plus"
            color="primary">
            Create Detail Pesanan
        </flux:button>
    </flux:modal.trigger>
    
    {{-- Livewire Components --}}
    <livewire:detail-pesanan.create />
    <livewire:detail-pesanan.edit />

    <x-flash-message />

    <div class="overflow-x-auto">

        <flux:table :paginate="$this->detailPesanans">

            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Pesanan ID</flux:table.column>
                <flux:table.column>Menu</flux:table.column>
                <flux:table.column>Jumlah</flux:table.column>
                <flux:table.column>Subtotal</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>

                @foreach ($this->detailPesanans as $detailPesanan)

                    <flux:table.row :key="$detailPesanan->id">

                        <flux:table.cell>
                            {{ $loop->iteration + $this->detailPesanans->firstItem() - 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $detailPesanan->pesanan_id }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $detailPesanan->menu?->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $detailPesanan->jumlah }}
                        </flux:table.cell>

                        <flux:table.cell>
                            Rp {{ number_format($detailPesanan->subtotal, 0, ',', '.') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $detailPesanan->created_at?->diffForHumans() ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>

                            <flux:dropdown>

                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>

                                <flux:menu>

                                    <flux:menu.item
                                        icon="pencil"
                                        wire:click="edit({{ $detailPesanan->id }})">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
                                        wire:click="$dispatch('confirm-delete-detail', { id: {{ $detailPesanan->id }} })">
                                        Delete
                                    </flux:menu.item>

                                </flux:menu>

                            </flux:dropdown>

                        </flux:table.cell>

                    </flux:table.row>

                @endforeach

            </flux:table.rows>

        </flux:table>

    </div>
</div>