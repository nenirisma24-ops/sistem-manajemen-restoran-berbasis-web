<?php

use Livewire\Component;
use Flux\Flux;
use App\Models\User;
use App\Models\Table;
use App\Livewire\Forms\PesananForm;

new class extends Component
{
    public PesananForm $form;

    public function mount()
    {
        $this->form = new PesananForm($this, 'form');
    }

    public function save()
    {
        $this->form->store();

        Flux::modal('create-pesanan')->close();

        session()->flash(
            'success',
            'Pesanan created successfully'
        );

        return $this->redirectRoute(
            'pesanan.index',
            navigate: true
        );
    }

    public function resetForm()
    {
        $this->resetValidation();

        if (isset($this->form)) {
            $this->form->reset();
            $this->form->status = 'pending';
            $this->form->total_price = 0;
        }
    }

    public function users()
    {
        return User::all();
    }

    public function tables()
    {
        return Table::where('status', 'tersedia')->get();
    }
};

?>

<div>
    <flux:modal
        name="create-pesanan"
        class="md:w-150"
        x-on:close="$wire.resetForm()"
    >

        <form class="space-y-6" wire:submit.prevent="save">

            <div class="space-y-2">
                <flux:heading size="lg">
                    Create Pesanan
                </flux:heading>

                <flux:text>
                    Add a new order
                </flux:text>
            </div>

            <div class="space-y-6">

                {{-- Customer --}}
                <flux:select
                    label="Customer"
                    wire:model="form.user_id"
                >
                    <option value="">-- Select Customer --</option>

                    @foreach($this->users() as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach

                </flux:select>

                {{-- Table --}}
                <flux:select
                    label="Table"
                    wire:model="form.table_id"
                >
                    <option value="">-- Select Table --</option>

                    @foreach($this->tables() as $table)
                        <option value="{{ $table->id }}">
                            {{ $table->number_table }}
                        </option>
                    @endforeach

                </flux:select>

                {{-- Order Date --}}
                <flux:input
                    type="date"
                    label="Order Date"
                    wire:model="form.order_date"
                />

                {{-- Status --}}
                <flux:select
                    label="Status"
                    wire:model="form.status"
                >
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </flux:select>

                {{-- Total --}}
                <flux:input
                    type="number"
                    label="Total Price"
                    placeholder="0"
                    wire:model="form.total_price"
                />

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