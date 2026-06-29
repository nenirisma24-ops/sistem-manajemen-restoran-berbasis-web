<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Table;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function tables()
    {
        return Table::latest()->paginate(10);
    }
};

?>

<div class="max-w-7xl mx-auto space-y-6">

    <div>
        <flux:heading size="xl">Tables</flux:heading>
        <flux:subheading>
            Manage your restaurant tables
        </flux:subheading>
    </div>

    <flux:separator />

    <div class="flex justify-end">
        <flux:modal.trigger name="create-table">
            <flux:button variant="primary" icon="plus">
                Create Table
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Modal Components --}}
    <livewire:table.create />
    <livewire:table.edit />

    <x-flash-message />

    <div class="overflow-x-auto">

        <flux:table :paginate="$this->tables">

            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Table Number</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>

                @forelse ($this->tables as $table)

                    <flux:table.row :key="$table->id">

                        <flux:table.cell>
                            {{ $loop->iteration + $this->tables->firstItem() - 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $table->number_table }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ ucfirst($table->status) }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $table->created_at->diffForHumans() }}
                        </flux:table.cell>

                        <flux:table.cell>

                            <div class="flex gap-2">

                                <flux:button
                                    size="sm"
                                    icon="pencil"
                                    variant="filled"
                                    wire:click="$dispatch('edit-table', { id: {{ $table->id }} })"
                                >
                                    Edit
                                </flux:button>

                                <flux:button
                                    size="sm"
                                    icon="trash"
                                    variant="danger"
                                    wire:click="$dispatch('delete-table', { id: {{ $table->id }} })"
                                >
                                    Delete
                                </flux:button>

                            </div>

                        </flux:table.cell>

                    </flux:table.row>

                @empty

                    <flux:table.row>

                        <flux:table.cell colspan="5" class="text-center py-8">
                            No table data found.
                        </flux:table.cell>

                    </flux:table.row>

                @endforelse

            </flux:table.rows>

        </flux:table>

    </div>

</div>