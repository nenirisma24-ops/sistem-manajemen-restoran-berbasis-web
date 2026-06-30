<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Table;
use App\Livewire\Forms\PesananForm;

new class extends Component
{
    public PesananForm $form;

    #[On('edit-pesanan')]
    public function editPesanan($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $this->form->setPesanan($pesanan);

        Flux::modal('edit-pesanan')->show();
    }

    public function updatePesanan()
    {
        $this->form->update();

        Flux::modal('edit-pesanan')->close();

        session()->flash('success', 'Pesanan updated successfully');

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

    #[On('delete-pesanan')]
    public function confirmDelete($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $this->form->setPesanan($pesanan);

        Flux::modal('delete-pesanan')->show();
    }

    public function deletePesanan()
    {
        $this->form->pesanan->delete();

        Flux::modal('delete-pesanan')->close();

        session()->flash('success', 'Pesanan deleted successfully');

        return $this->redirectRoute(
            'pesanan.index',
            navigate: true
        );
    }

    public function users()
    {
        return User::orderBy('name')->get();
    }

    public function tables()
    {
        return Table::orderBy('number_table')->get();
    }
};

?>

<div>

    {{-- Edit Modal --}}
    <flux:modal
        name="edit-pesanan"
        class="md:w-[600px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="updatePesanan" class="space-y-6">

            <div>
                <flux:heading size="lg">
                    Edit Pesanan
                </flux:heading>

                <flux:text>
                    Update order information.
                </flux:text>
            </div>

            <flux:select
                label="Customer"
                wire:model="form.user_id"
            >
                @foreach($this->users() as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </flux:select>

            <flux:select
                label="Table"
                wire:model="form.table_id"
            >
                @foreach($this->tables() as $table)
                    <option value="{{ $table->id }}">
                        {{ $table->number_table }}
                    </option>
                @endforeach
            </flux:select>

            <flux:input
                type="date"
                label="Order Date"
                wire:model="form.order_date"
            />

            <flux:select
                label="Status"
                wire:model="form.status"
            >
                <option value="pending">Pending</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="dibatalkan">Dibatalkan</option>
            </flux:select>

            <flux:input
                type="number"
                label="Total Price"
                wire:model="form.total_price"
            />

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
        name="delete-pesanan"
        class="md:w-[450px]"
        x-on:close="$wire.resetForm()"
    >

        <form wire:submit="deletePesanan" class="space-y-6">

            <div>

                <flux:heading size="lg">
                    Delete Pesanan
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