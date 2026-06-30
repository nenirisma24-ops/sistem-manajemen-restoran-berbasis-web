<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
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

    public function edit($id)
    {
        $this->dispatch('edit-pesanan', id: $id);
    }
};

?>

<div class="max-w-7xl mx-auto space-y-4">

    <flux:heading size="xl">
        Pesanan
    </flux:heading>

    <flux:subheading size="lg">
        Manage your orders
    </flux:subheading>

    <flux:separator variant="subtle" />

    {{-- Button Create --}}
    <flux:modal.trigger name="create-pesanan">
        <flux:button
            variant="primary"
            icon="plus"
        >
            Create Pesanan
        </flux:button>
    </flux:modal.trigger>

    {{-- Modal --}}
    <livewire:pesanan.create />
    <livewire:pesanan.edit />

    <x-flash-message />

    <div class="overflow-x-auto">

        <flux:table :paginate="$this->pesanans">

            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Table</flux:table.column>
                <flux:table.column>Order_Date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Total_price</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>

                @forelse($this->pesanans as $pesanan)

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
                            {{ $pesanan->order_date }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ ucfirst($pesanan->status) }}
                        </flux:table.cell>

                        <flux:table.cell>
                            Rp {{ number_format($pesanan->total_price,0,',','.') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $pesanan->created_at?->diffForHumans() }}
                        </flux:table.cell>

                        <flux:table.cell>

                            <flux:dropdown>

                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="ellipsis-horizontal"
                                />

                                <flux:menu>

                                    <flux:menu.item
                                        icon="pencil"
                                        wire:click="edit({{ $pesanan->id }})"
                                    >
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
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

                        <flux:table.cell colspan="8">

                            <div class="text-center py-8 text-zinc-500">
                                No order found.
                            </div>

                        </flux:table.cell>

                    </flux:table.row>

                @endforelse

            </flux:table.rows>

        </flux:table>

    </div>

</div>