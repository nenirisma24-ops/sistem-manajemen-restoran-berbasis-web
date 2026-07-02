<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Payment;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function payments()
    {
        return Payment::with('pesanan')
            ->latest()
            ->paginate(10);
    }
};

?>

<div class="max-w-7xl mx-auto space-y-6">

    <div>
        <flux:heading size="xl">Payment</flux:heading>

        <flux:subheading>
            Manage your payment data
        </flux:subheading>
    </div>

    <flux:separator />

    {{-- Create Button --}}
    <div class="mb-4">
        <flux:modal.trigger name="create-payment">
            <flux:button
                variant="primary"
                icon="plus"
            >
                Create Payment
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Modal Components --}}
    <livewire:payment.create />
    <livewire:payment.edit />

    <x-flash-message />

    <div class="overflow-x-auto">

        <flux:table :paginate="$this->payments">

            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Pesanan</flux:table.column>
                <flux:table.column>Payment Method</flux:table.column>
                <flux:table.column>Payment Total</flux:table.column>
                <flux:table.column>Payment Date</flux:table.column>
                <flux:table.column>Payment Status</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>

                @forelse ($this->payments as $payment)

                    <flux:table.row :key="$payment->id">

                        <flux:table.cell>
                            {{ $loop->iteration + $this->payments->firstItem() - 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            Pesanan #{{ $payment->pesanan_id }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $payment->payment_method }}
                        </flux:table.cell>

                        <flux:table.cell>
                            Rp {{ number_format($payment->payment_total, 0, ',', '.') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ ucfirst($payment->payment_status) }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $payment->created_at->diffForHumans() }}
                        </flux:table.cell>

                        <flux:table.cell align="end">

                            <flux:dropdown position="bottom end">

                                <flux:button
                                    icon="ellipsis-horizontal"
                                    variant="ghost"
                                    size="sm"
                                />

                                <flux:menu>

                                    <flux:menu.item
                                        icon="pencil"
                                        wire:click="$dispatch('edit-payment', { id: {{ $payment->id }} })"
                                    >
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        icon="trash"
                                        variant="danger"
                                        wire:click="$dispatch('delete-payment', { id: {{ $payment->id }} })"
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
                            No payment data found.
                        </flux:table.cell>

                    </flux:table.row>

                @endforelse

            </flux:table.rows>

        </flux:table>

    </div>

</div>