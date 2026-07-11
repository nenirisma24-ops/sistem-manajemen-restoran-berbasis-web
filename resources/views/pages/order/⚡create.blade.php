<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Table;
use App\Models\Pesanan;
use App\Models\Detail_Pesanan;
use App\Models\Payment;

new class extends Component
{
    public array $cart = [];

    public $table_id = '';

    public $payment_method = '';

    public $search = '';

    public $activeCategoryId = null;

    #[Computed]
    public function categories()
    {
        return Category::with('menus')->get();
    }

    #[Computed]
    public function availableTables()
    {
        return Table::where('status', 'tersedia')->orderBy('number_table')->get();
    }

    #[Computed]
    public function filteredMenus()
    {
        $query = Menu::with('category');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->activeCategoryId) {
            $query->where('category_id', $this->activeCategoryId);
        }

        return $query->get()->groupBy(fn($m) => $m->category->name ?? 'Lainnya');
    }

    #[Computed]
    public function cartTotal()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    #[Computed]
    public function cartCount()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function addToCart($menuId)
    {
        $menu = Menu::find($menuId);
        if (!$menu) return;

        foreach ($this->cart as $i => $item) {
            if ($item['menu_id'] == $menuId) {
                if ($item['quantity'] < $menu->stock) {
                    $this->cart[$i]['quantity']++;
                }
                return;
            }
        }

        $this->cart[] = [
            'menu_id' => $menu->id,
            'name' => $menu->name,
            'price' => $menu->price,
            'quantity' => 1,
            'stock' => $menu->stock,
        ];
    }

    public function updateQuantity($menuId, $delta)
    {
        foreach ($this->cart as $i => $item) {
            if ($item['menu_id'] == $menuId) {
                $newQty = $item['quantity'] + $delta;
                if ($newQty <= 0) {
                    unset($this->cart[$i]);
                    $this->cart = array_values($this->cart);
                } elseif ($newQty <= $item['stock']) {
                    $this->cart[$i]['quantity'] = $newQty;
                }
                break;
            }
        }
    }

    public function removeFromCart($menuId)
    {
        foreach ($this->cart as $i => $item) {
            if ($item['menu_id'] == $menuId) {
                unset($this->cart[$i]);
                $this->cart = array_values($this->cart);
                break;
            }
        }
    }

    public function setCategory($categoryId)
    {
        $this->activeCategoryId = $this->activeCategoryId === $categoryId ? null : $categoryId;
    }

    public function save()
    {
        $this->validate([
            'table_id' => 'required|exists:tables,id',
            'payment_method' => 'required',
        ], [
            'table_id.required' => 'Pilih meja terlebih dahulu',
            'payment_method.required' => 'Pilih metode pembayaran',
        ]);

        if (empty($this->cart)) {
            session()->flash('error', 'Pilih minimal 1 menu');
            return;
        }

        $total = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        \DB::beginTransaction();

        try {
            $pesanan = Pesanan::create([
                'user_id' => auth()->id(),
                'table_id' => $this->table_id,
                'order_date' => now()->toDateString(),
                'status' => 'pending',
                'total_price' => $total,
            ]);

            foreach ($this->cart as $item) {
                Detail_Pesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            Payment::create([
                'pesanan_id' => $pesanan->id,
                'payment_method' => $this->payment_method,
                'payment_total' => $total,
                'payment_date' => now()->toDateString(),
                'payment_status' => 'Pending',
            ]);

            Table::where('id', $this->table_id)->update(['status' => 'tidak tersedia']);

            \DB::commit();

            session()->flash('success', 'Pesanan berhasil dibuat');

            return $this->redirectRoute('pesanan.index', navigate: true);

        } catch (\Throwable $e) {
            \DB::rollBack();
            session()->flash('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }
};

?>

<div class="h-full flex flex-col gap-6">

    <x-flash-message />

    {{-- Top Bar --}}
    <div class="flex items-center justify-between gap-4">
        <div class="relative w-96">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input type="text" wire:model.live="search" placeholder="Cari menu..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
        </div>
        <div class="flex items-center gap-2">
            <button class="size-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            </button>
            <button class="size-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition relative">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/></svg>
                <span class="absolute -top-0.5 -right-0.5 size-3.5 rounded-full bg-red-500 text-white text-[8px] flex items-center justify-center font-bold">{{ $this->cartCount }}</span>
            </button>
            <div class="size-9 rounded-xl bg-orange-500 flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex gap-6 flex-1 min-h-0">

        {{-- Left: Menu Area --}}
        <div class="flex-1 min-w-0 flex flex-col gap-4">

            {{-- Categories --}}
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                @foreach ($this->categories as $cat)
                    <button wire:click="setCategory({{ $cat->id }})"
                        class="whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-semibold border transition
                            {{ $activeCategoryId === $cat->id ? 'bg-orange-500 text-white border-orange-500' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:border-orange-300' }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
                @if ($activeCategoryId)
                    <button wire:click="setCategory(null)" class="whitespace-nowrap px-3 py-1.5 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-600 hover:bg-zinc-200">
                        ✕ Semua
                    </button>
                @endif
            </div>

            {{-- Menu Grid --}}
            <div class="flex-1 overflow-y-auto">
                @php $groups = $this->filteredMenus; @endphp

                @if ($groups->isEmpty())
                    <div class="flex items-center justify-center h-full text-zinc-400 text-sm">Menu tidak ditemukan</div>
                @else
                    <div class="space-y-6">
                        @foreach ($groups as $categoryName => $menus)
                            <div>
                                <h3 class="text-sm font-bold mb-3 dark:text-white">{{ $categoryName }}</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach ($menus as $menu)
                                        @php
                                            $inCart = collect($this->cart)->firstWhere('menu_id', $menu->id);
                                            $qty = $inCart['quantity'] ?? 0;
                                        @endphp
                                        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden {{ $qty > 0 ? 'ring-2 ring-orange-500' : '' }}">
                                            <div class="h-36 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-900 relative">
                                                @if ($menu->image)
                                                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-xs text-zinc-400">No Photo</div>
                                                @endif
                                                @if ($qty > 0)
                                                    <div class="absolute top-1.5 right-1.5 size-5 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center">{{ $qty }}</div>
                                                @endif
                                            </div>
                                            <div class="p-2.5">
                                                <p class="text-sm font-semibold truncate dark:text-white">{{ $menu->name }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                                @if ($qty > 0)
                                                    <div class="flex items-center justify-between mt-2">
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" wire:click="updateQuantity({{ $menu->id }}, -1)" class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-600 transition text-sm font-bold">−</button>
                                                            <span class="w-5 text-center text-sm font-bold">{{ $qty }}</span>
                                                            <button type="button" wire:click="updateQuantity({{ $menu->id }}, 1)" class="size-7 rounded-lg bg-orange-500 text-white flex items-center justify-center hover:bg-orange-600 transition text-sm font-bold">+</button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <button type="button" wire:click="addToCart({{ $menu->id }})" class="w-full mt-2 py-1.5 rounded-lg bg-orange-500 text-white text-xs font-semibold hover:bg-orange-600 transition">Pesan</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Right: Cart + Checkout --}}
        <div class="w-80 shrink-0">
            <div class="sticky top-0 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 flex flex-col max-h-[calc(100vh-12rem)]">
                {{-- Header --}}
                <div class="flex items-center justify-between p-4 pb-2">
                    <h3 class="text-sm font-bold dark:text-white">Pesanan</h3>
                    <span class="text-xs bg-orange-500 text-white px-2 py-0.5 rounded-full font-semibold">{{ $this->cartCount }} item</span>
                </div>

                {{-- Cart Items --}}
                <div class="flex-1 overflow-y-auto px-4 space-y-2">
                    @if (empty($this->cart))
                        <div class="text-center py-8">
                            <svg class="size-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                            <p class="text-xs text-zinc-400">Belum ada item dipilih</p>
                        </div>
                    @else
                        @foreach ($this->cart as $item)
                            <div class="flex items-center gap-2 py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item['name'] }}</p>
                                    <p class="text-xs text-zinc-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" wire:click="updateQuantity({{ $item['menu_id'] }}, -1)" class="size-6 rounded bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-600 text-xs font-bold">−</button>
                                    <span class="w-5 text-center text-xs font-bold">{{ $item['quantity'] }}</span>
                                    <button type="button" wire:click="updateQuantity({{ $item['menu_id'] }}, 1)" class="size-6 rounded bg-orange-500 text-white flex items-center justify-center hover:bg-orange-600 text-xs font-bold">+</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Checkout --}}
                @if (!empty($this->cart))
                    <div class="border-t border-zinc-100 dark:border-zinc-700 p-4 space-y-3">
                        <div>
                            <label class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1 block">Meja</label>
                            <select wire:model="table_id" class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
                                <option value="">-- Pilih Meja --</option>
                                @foreach ($this->availableTables as $table)
                                    <option value="{{ $table->id }}">Meja {{ $table->number_table }}</option>
                                @endforeach
                            </select>
                            @error('table_id') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1 block">Metode Bayar</label>
                            <select wire:model="payment_method" class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
                                <option value="">-- Pilih Metode --</option>
                                <option value="Cash">Cash</option>
                                <option value="Transfer">Transfer</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Credit Card">Credit Card</option>
                            </select>
                            @error('payment_method') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="border-t border-zinc-100 dark:border-zinc-700 pt-3 space-y-1.5">
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-500">Sub Total</span>
                                <span class="font-medium">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold">
                                <span>Total</span>
                                <span class="text-orange-500 text-base">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="button" wire:click="save"
                            class="w-full py-2.5 rounded-xl bg-orange-500 text-white text-sm font-bold hover:bg-orange-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ empty($this->cart) ? 'disabled' : '' }}>
                            Buat Pesanan
                        </button>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
