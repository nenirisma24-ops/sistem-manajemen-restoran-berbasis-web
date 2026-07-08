<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Menu;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function menus()
    {
        return Menu::latest()->paginate(10);
    }

    public function edit($id)
    {
        $this->dispatch('edit-menu', id: $id);
    }
};

?>

<div class="max-w-7xl mx-auto space-y-4">

    <flux:heading size="xl">
        Menu
    </flux:heading>

    <flux:subheading size="lg">
        Manage your menus
    </flux:subheading>

    <flux:separator variant="subtle" />

    {{-- Button Create --}}
    <flux:modal.trigger name="create-menu">
        <flux:button
            variant="primary"
            icon="plus"
        >
            Create Menu
        </flux:button>
    </flux:modal.trigger>

    {{-- Livewire Components --}}
    <livewire:menu.create />
    <livewire:menu.edit />

    <x-flash-message />

    {{-- Table --}}
    <div class="overflow-x-auto">

        <flux:table :paginate="$this->menus">

            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Image</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Description</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Stock</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>

                @forelse ($this->menus as $menu)

                    <flux:table.row :key="$menu->id">

                        {{-- No --}}
                        <flux:table.cell>
                            {{ $loop->iteration + $this->menus->firstItem() - 1 }}
                        </flux:table.cell>

                        {{-- Image (Dikunci ukurannya agar tidak merusak tinggi baris) --}}
                        <flux:table.cell>
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" 
                                    alt="{{ $menu->name }}" 
                                    class="w-14 h-14 min-w-14 min-h-14 object-cover aspect-square rounded-lg shadow-sm border border-zinc-200 block">
                            @else
                                <div class="w-14 h-14 min-w-14 min-h-14 bg-zinc-100 rounded-lg flex items-center justify-center text-[10px] text-zinc-400 border border-dashed border-zinc-300">
                                    No Photo
                                </div>
                            @endif
                        </flux:table.cell>

                        {{-- Name --}}
                        <flux:table.cell>
                            {{ $menu->name }}
                        </flux:table.cell>
                        
                        {{-- Description --}}
                        <flux:table.cell>
                            {{ $menu->description }}
                        </flux:table.cell>

                        {{-- Price --}}
                        <flux:table.cell>
                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                        </flux:table.cell>

                        {{-- Stock --}}
                        <flux:table.cell>
                            {{ $menu->stock }}
                        </flux:table.cell>

                        {{-- Created --}}
                        <flux:table.cell>
                            {{ $menu->created_at?->diffForHumans() }}
                        </flux:table.cell>

                        {{-- Actions --}}
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
                                        wire:click="edit({{ $menu->id }})"
                                    >
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
                                        wire:click="$dispatch('confirm-delete', { id: {{ $menu->id }} })"
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
                                No menu found.
                            </div>
                        </flux:table.cell>

                    </flux:table.row>

                @endforelse

            </flux:table.rows>

        </flux:table>

    </div>

</div>