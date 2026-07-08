<?php

use Livewire\Component;
use App\Models\Menu;
use App\Models\Detail_Pesanan; 
use Livewire\Attributes\Computed;

new class extends Component
{
    public $pesanan_id;
    
    public $items = [
        ['menu_id' => '', 'jumlah' => 1, 'subtotal' => 0]
    ];

    #[Computed]
    public function menus()
    {
        return Menu::all();
    }

    public function addItem()
    {
        $this->items[] = ['menu_id' => '', 'jumlah' => 1, 'subtotal' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); 
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $index = $parts[0];
            $menuId = $this->items[$index]['menu_id'];
            $jumlah = (int)$this->items[$index]['jumlah'];

            if ($menuId) {
                $menu = Menu::find($menuId);
                if ($menu) {
                    $this->items[$index]['subtotal'] = $menu->price * ($jumlah > 0 ? $jumlah : 1);
                }
            } else {
                $this->items[$index]['subtotal'] = 0;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'pesanan_id' => 'required',
            'items.*.menu_id' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
        ], [
            'items.*.menu_id.required' => 'Menu harus dipilih',
            'items.*.jumlah.required' => 'Jumlah wajib diisi',
        ]);

        foreach ($this->items as $item) {
            Detail_Pesanan::create([
                'pesanan_id' => $this->pesanan_id,
                'menu_id' => $item['menu_id'],
                'jumlah' => $item['jumlah'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        Flux::modal('create-detail-pesanan')->close();

        session()->flash('success', 'Detail Pesanan created successfully');

        $this->redirectRoute('detail-pesanan.index');
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->pesanan_id = null;
        $this->items = [
            ['menu_id' => '', 'jumlah' => 1, 'subtotal' => 0]
        ];
    }   
};

?>

<div>
    <flux:modal name="create-detail-pesanan" class="md:w-180" x-on:close="$wire.resetForm()">
        <form class="space-y-8" wire:submit.prevent="save">
            <div class="space-y-2">
                <flux:heading size="lg">Create Detail Pesanan</flux:heading>
                <flux:text>Add new detail pesanans</flux:text>
            </div>

            <div class="space-y-6">
                <flux:input
                    label="Pesanan ID"
                    type="number"
                    placeholder="Enter pesanan ID"
                    wire:model="pesanan_id"
                />  
                @error('pesanan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                <hr class="border-zinc-200 dark:border-zinc-800">
                
                <div class="flex justify-between items-center">
                    <flux:text font-weight="semibold">Daftar Menu</flux:text>
                    <flux:button type="button" size="sm" variant="outline" wire:click="addItem">
                        + Tambah Baris Menu
                    </flux:button>
                </div>

                @foreach ($items as $index => $item)
                    <div class="grid grid-cols-12 gap-3 items-end p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg relative">
                        
                        <div class="col-span-5">
                            <flux:select
                                label="Menu"
                                placeholder="Select menu"
                                wire:model.live="items.{{ $index }}.menu_id"
                            >
                                <option value="">Select menu</option>
                                @foreach ($this->menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }} (Rp {{ number_format($menu->price, 0, ',', '.') }})</option>
                                @endforeach
                            </flux:select>
                            @error("items.{$index}.menu_id") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-3">
                            <flux:input
                                label="Jumlah"
                                type="number"
                                min="1"
                                wire:model.live="items.{{ $index }}.jumlah"
                            />
                            @error("items.{$index}.jumlah") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-3">
                            <flux:input
                                label="Subtotal"
                                type="number"
                                readonly
                                wire:model="items.{{ $index }}.subtotal"
                            />
                        </div>

                        <div class="col-span-1 text-center">
                            @if(count($items) > 1)
                                <flux:button type="button" variant="ghost" color="danger" size="sm" wire:click="removeItem({{ $index }})">
                                    ❌
                                </flux:button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Create</flux:button>
            </div>
        </form>
    </flux:modal>
</div>