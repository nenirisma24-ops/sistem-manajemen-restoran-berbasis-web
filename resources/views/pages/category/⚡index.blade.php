<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;

new class extends Component
{
    use WithPagination;
    
    #[Computed]
    public function categories()
    {
        return Category::latest()->paginate(10);
    }

    public function edit($id){
        $this->dispatch('edit-category', id: $id);
    }
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">Category</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Manage your categories</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-category">
        <flux:button variant="primary" icon="plus" color="primary">Category Menu</flux:button>
    </flux:modal.trigger>

    <livewire:pages::category.menu />
    <livewire:pages::category.edit />
    <x-flash-message />

     {{-- table --}}
    <div class="overflow-x-auto">
       <flux:table :paginate="$this->categories">
            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->categories as $category)
                    <flux:table.row :key="$category->id">

                        <flux:table.cell>
                            {{$loop->iteration + $this->categories->firstItem()-1}}
                        </flux:table.cell> 
                        
                        <flux:table.cell class="flex items-center gap-3">
                            {{ $category->name }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">{{ $category->created_at->diffForHumans() }}</flux:table.cell>

                        <flux:table.cell>


                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $category->id }})">Edit</flux:menu.item>

                                    <flux:menu.separator />

                                    {{-- <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', id: $category->id)">Delete</flux:menu.item> --}}
                                    <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', { id: {{ $category->id }} })">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>


    </div>
</div>