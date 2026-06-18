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
        $this->dispatch('edit-Menu',  id:$id);
    }
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">Menu</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Manage your menus</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-menu">
        <flux:button variant="primary" icon="plus" color="primary">Create Menu</flux:button>
    </flux:modal.trigger>

    <livewire:menu.create />
    <livewire:menu.edit />
    <x-flash-message />

     {{-- table --}}
    <div class="overflow-x-auto">
       <flux:table :paginate="$this->menus">
            <flux:table.columns>
                <flux:table.column>kategori_id</flux:table.column>
                <flux:table.column>nama_menu</flux:table.column>
                <flux:table.column>deskripsi</flux:table.column>
                <flux:table.column>Harga</flux:table.column>
                <flux:table.column>Stok</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->menus as $menu)
                    <flux:table.row :key="$menu->id">

                        <flux:table.cell>
                            {{$loop->iteration + $this->menus->firstItem()-1}}
                        </flux:table.cell> 
                        
                        <flux:table.cell class="flex items-center gap-3">
                            {{ $menu->nama_menu }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $menu->deskripsi_menu }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $menu->harga }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $menu->stok }}
                        </flux:table.cell>
                       <flux:table.cell class="whitespace-nowrap">{{ $menu->created_at?->diffForHumans() ?? '-' }}
                       </flux:table.cell>

                        <flux:table.cell>


                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $menu->id }})">Edit</flux:menu.item>

                                    <flux:menu.separator />

                                    {{-- <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', id: $menu->id)">Delete</flux:menu.item> --}}
                                    <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', { id: {{ $menu->id }} })">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>


    </div>
</div>