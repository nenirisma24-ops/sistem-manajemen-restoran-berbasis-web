<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;
use App\Models\Payment;
use App\Models\Pesanan;
use App\Livewire\Forms\PaymentForm;

new class extends Component
{
    public PaymentForm $form;

    #[On('edit-payment')]
    public function editPayment($id)
    {
        $payment = Payment::findOrFail($id);

        $this->form->setPayment($payment);

        Flux::modal('edit-payment')->show();
    }

    public function updatePayment()
    {
        $this->form->update();

        Flux::modal('edit-payment')->close();

        session()->flash('success', 'Payment updated successfully');

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
        }
    }

    #[On('delete-payment')]
    public function confirmDelete($id)
    {
        $payment = Payment::findOrFail($id);

        $this->form->setPayment($payment);

        Flux::modal('delete-payment')->show();
    }

    public function deletePayment()
    {
        $this->form->payment->delete();

        Flux::modal('delete-payment')->close();

        session()->flash('success', 'Payment deleted successfully');

        return $this->redirectRoute(
            'payment.index',
            navigate: true
        );
    }

    public function pesanans()
    {
        return Pesanan::latest()->get();
    }
};

?>

<div>

    {{-- Edit Modal --}}
    <flux:modal
        name="edit-payment"
        class="md:w-[600px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="updatePayment" class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Edit Payment
                </flux:heading>

                <flux:text>
                    Update payment information.
                </flux:text>

            </div>

            <flux:select
                label="Pesanan"
                wire:model="form.pesanan_id"
            >
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
                <option value="Cash">Cash</option>
                <option value="Transfer">Transfer</option>
                <option value="QRIS">QRIS</option>
                <option value="Debit Card">Debit Card</option>
                <option value="Credit Card">Credit Card</option>
            </flux:select>

            <flux:input
                type="number"
                label="Payment Total"
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
        name="delete-payment"
        class="md:w-[450px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="deletePayment" class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Delete Payment
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