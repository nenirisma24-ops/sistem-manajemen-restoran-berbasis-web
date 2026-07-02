<?php

use Livewire\Component;
use Flux\Flux;
use App\Models\Pesanan;
use App\Livewire\Forms\PaymentForm;

new class extends Component
{
    // Instance Form
    public PaymentForm $form;

    public function save()
    {
        $this->form->store();

        Flux::modal('create-payment')->close();

        session()->flash(
            'success',
            'Payment created successfully'
        );

        return $this->redirectRoute(
            'payment.index',
            navigate: true
        );
    }

    public function resetForm()
    {
        $this->resetValidation();

        if (isset($this->form)) {
            $this->form->reset();
            $this->form->payment_total = 0;
            $this->form->payment_status = 'Pending';
        }
    }

    public function pesanans()
    {
        return Pesanan::latest()->get();
    }
};

?>

<div>

    <flux:modal
        name="create-payment"
        class="md:w-150"
        x-on:close="$wire.resetForm()"
    >

        <form class="space-y-8" wire:submit.prevent="save">

            <div class="space-y-2">

                <flux:heading size="lg">
                    Create Payment
                </flux:heading>

                <flux:text>
                    Add a new payment
                </flux:text>

            </div>

            <div class="space-y-6">

                <flux:select
                    label="Pesanan"
                    wire:model="form.pesanan_id"
                >
                    <option value="">-- Select Pesanan --</option>

                    @foreach($this->pesanans() as $pesanan)
                        <option value="{{ $pesanan->id }}">
                            Pesanan #{{ $pesanan->id }}
                        </option>
                    @endforeach

                </flux:select>

                <flux:select
                    label="Payment Method"
                    wire:model="form.payment_method"
                >
                    <option value="">-- Select Payment Method --</option>
                    <option value="Cash">Cash</option>
                    <option value="Transfer">Transfer</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="Credit Card">Credit Card</option>
                </flux:select>

                <flux:input
                    type="number"
                    label="Payment Total"
                    placeholder="0"
                    wire:model="form.payment_total"
                />

                <flux:input
                    type="date"
                    label="Payment Date"
                    wire:model="form.payment_date"
                />

                <flux:select
                    label="Payment Status"
                    wire:model="form.payment_status"
                >
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                    <option value="Failed">Failed</option>
                </flux:select>

            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">

                <flux:modal.close>
                    <flux:button
                        variant="outline"
                        color="neutral"
                    >
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    variant="primary"
                    color="primary"
                    type="submit"
                >
                    Create
                </flux:button>

            </div>

        </form>

    </flux:modal>

</div>