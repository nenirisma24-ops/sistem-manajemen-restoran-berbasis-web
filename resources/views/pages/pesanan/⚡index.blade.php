<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Pesanan;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function pesanans()
    {
        return Pesanan::with(['user', 'table'])
            ->latest()
            ->paginate(10);
    }

    public function markAsCompleted($id)
    {
        $pesanan = Pesanan::with('table')->findOrFail($id);

        if ($pesanan->status === 'selesai') {
            session()->flash('info', 'Pesanan #' . $id . ' sudah selesai');
            return;
        }

        \DB::beginTransaction();

        try {
            $pesanan->update(['status' => 'selesai']);

            if ($pesanan->table) {
                $pesanan->table->update(['status' => 'tersedia']);
            }

            \DB::commit();

            session()->flash('success', 'Pesanan #' . $id . ' selesai, meja tersedia kembali');

        } catch (\Throwable $e) {
            \DB::rollBack();
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }
};

?>

<div class="max-w-7xl mx-auto space-y-6">

    <div>
        <flux:heading size="xl">Pesanan</flux:heading>

        <flux:subheading>
            Manage your restaurant orders
        </flux:subheading>
    </div>

    <flux:separator />

    {{-- Create Button --}}
    <div class="mb-4">
        <flux:modal.trigger name="create-pesanan">
            <flux:button
                variant="primary"
                icon="plus"
            >
                Create Pesanan
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Modal Components --}}
    <livewire:pesanan.create />
    <livewire:pesanan.edit />

    <x-flash-message />

    <div class="overflow-x-auto">

        <flux:table :paginate="$this->pesanans">

            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Table</flux:table.column>
                <flux:table.column>Order Date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Total Price</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>

                @forelse ($this->pesanans as $pesanan)

                    <flux:table.row :key="$pesanan->id">

                        <flux:table.cell>
                            {{ $loop->iteration + $this->pesanans->firstItem() - 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $pesanan->user->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $pesanan->table->number_table }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ \Carbon\Carbon::parse($pesanan->order_date)->format('d-m-Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ ucfirst($pesanan->status) }}
                        </flux:table.cell>

                        <flux:table.cell>
                            Rp {{ number_format($pesanan->total_price, 0, ',', '.') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $pesanan->created_at->diffForHumans() }}
                        </flux:table.cell>

                        <flux:table.cell align="end">

                            <flux:dropdown position="bottom end">

                                <flux:button
                                    icon="ellipsis-horizontal"
                                    variant="ghost"
                                    size="sm"
                                />

                                <flux:menu>

                                    @if ($pesanan->status !== 'selesai' && $pesanan->status !== 'dibatalkan')
                                        <flux:menu.item
                                            icon="check-circle"
                                            wire:click="markAsCompleted({{ $pesanan->id }})"
                                        >
                                            Selesai
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                    @endif

                                    <flux:menu.item
                                        icon="pencil"
                                        wire:click="$dispatch('edit-pesanan', { id: {{ $pesanan->id }} })"
                                    >
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        icon="trash"
                                        variant="danger"
                                        wire:click="$dispatch('delete-pesanan', { id: {{ $pesanan->id }} })"
                                    >
                                        Delete
                                    </flux:menu.item>

                                </flux:menu>

                            </flux:dropdown>

                        </flux:table.cell>

                    </flux:table.row>

                @empty

                    <flux:table.row>

                        <flux:table.cell colspan="8" class="text-center py-8">
                            No order data found.
                        </flux:table.cell>

                    </flux:table.row>

                @endforelse

            </flux:table.rows>

        </flux:table>

    </div>

</div>