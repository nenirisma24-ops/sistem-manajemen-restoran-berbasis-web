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
    public int $step = 1;

    public array $cart = [];

    public $table_id = '';

    public $payment_method = '';

    public function mount()
    {
        $this->cart = [];
    }

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
    public function cartTotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
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

        $existing = collect($this->cart)->firstWhere('menu_id', $menuId);

        if ($existing) {
            foreach ($this->cart as $i => $item) {
                if ($item['menu_id'] == $menuId) {
                    $this->cart[$i]['quantity']++;
                    break;
                }
            }
        } else {
            $this->cart[] = [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'quantity' => 1,
                'stock' => $menu->stock,
            ];
        }
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

    public function nextStep()
    {
        if ($this->step == 1) {
            if (empty($this->cart)) {
                session()->flash('error', 'Pilih minimal 1 menu terlebih dahulu');
                return;
            }
        }

        if ($this->step == 2) {
            $rules = ['table_id' => 'required|exists:tables,id'];
            $this->validate($rules, ['table_id.required' => 'Pilih meja terlebih dahulu']);
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step = max(1, $this->step - 1);
    }

    public function save()
    {
        $this->validate([
            'table_id' => 'required|exists:tables,id',
            'payment_method' => 'required',
        ]);

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

<div class="max-w-7xl mx-auto space-y-6">

    <x-flash-message />

    {{-- Header & Steps --}}
    <div class="mb-8">
        <flux:heading size="xl">Pesanan Baru</flux:heading>
        <flux:subheading>Buat pesanan baru untuk pelanggan</flux:subheading>

        <div class="mt-6 flex items-center gap-2 text-sm">
            <div class="flex items-center gap-2 {{ $step >= 1 ? 'text-indigo-600 dark:text-indigo-400' : 'text-zinc-400' }}">
                <span class="flex size-8 items-center justify-center rounded-full {{ $step >= 1 ? 'bg-indigo-600 text-white' : 'bg-zinc-200 dark:bg-zinc-700' }} font-semibold text-xs">1</span>
                <span class="font-medium">Pilih Menu</span>
            </div>
            <div class="h-px w-12 {{ $step >= 2 ? 'bg-indigo-600' : 'bg-zinc-300 dark:bg-zinc-600' }}"></div>
            <div class="flex items-center gap-2 {{ $step >= 2 ? 'text-indigo-600 dark:text-indigo-400' : 'text-zinc-400' }}">
                <span class="flex size-8 items-center justify-center rounded-full {{ $step >= 2 ? 'bg-indigo-600 text-white' : 'bg-zinc-200 dark:bg-zinc-700' }} font-semibold text-xs">2</span>
                <span class="font-medium">Pilih Meja</span>
            </div>
            <div class="h-px w-12 {{ $step >= 3 ? 'bg-indigo-600' : 'bg-zinc-300 dark:bg-zinc-600' }}"></div>
            <div class="flex items-center gap-2 {{ $step >= 3 ? 'text-indigo-600 dark:text-indigo-400' : 'text-zinc-400' }}">
                <span class="flex size-8 items-center justify-center rounded-full {{ $step >= 3 ? 'bg-indigo-600 text-white' : 'bg-zinc-200 dark:bg-zinc-700' }} font-semibold text-xs">3</span>
                <span class="font-medium">Pembayaran</span>
            </div>
        </div>
    </div>

    @if ($step == 1)
        {{-- STEP 1: Pilih Menu --}}
        <div class="flex gap-6">
            {{-- Menu Grid --}}
            <div class="flex-1 min-w-0 space-y-6">
                @forelse ($this->categories as $category)
                    <div>
                        <h3 class="text-base font-bold mb-3 dark:text-white">{{ $category->name }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($category->menus as $menu)
                                @php
                                    $inCart = collect($this->cart)->firstWhere('menu_id', $menu->id);
                                    $qty = $inCart['quantity'] ?? 0;
                                @endphp
                                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden {{ $qty > 0 ? 'ring-2 ring-indigo-500' : '' }}">
                                    <div class="h-32 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                        @if ($menu->image)
                                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-zinc-400">No Photo</div>
                                        @endif
                                    </div>
                                    <div class="p-2.5">
                                        <p class="text-sm font-semibold truncate dark:text-white">{{ $menu->name }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                        <div class="mt-2">
                                            @if ($qty > 0)
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" wire:click="updateQuantity({{ $menu->id }}, -1)" class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-600 transition text-sm font-bold">-</button>
                                                    <span class="w-6 text-center text-sm font-bold">{{ $qty }}</span>
                                                    <button type="button" wire:click="updateQuantity({{ $menu->id }}, 1)" class="size-7 rounded-lg bg-indigo-500 text-white flex items-center justify-center hover:bg-indigo-600 transition text-sm font-bold">+</button>
                                                </div>
                                            @else
                                                <button type="button" wire:click="addToCart({{ $menu->id }})" class="w-full py-1.5 rounded-lg bg-indigo-500 text-white text-xs font-semibold hover:bg-indigo-600 transition">+ Tambah</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-zinc-400">Belum ada menu tersedia.</div>
                @endforelse
            </div>

            {{-- Cart Sidebar --}}
            <div class="w-72 shrink-0">
                <div class="sticky top-6 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold dark:text-white">Keranjang</h3>
                        <span class="text-xs bg-indigo-500 text-white px-2 py-0.5 rounded-full font-semibold">{{ $this->cartCount }} item</span>
                    </div>

                    @if (empty($this->cart))
                        <p class="text-sm text-zinc-400 py-6 text-center">Belum ada menu dipilih</p>
                    @else
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach ($this->cart as $item)
                                <div class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium truncate">{{ $item['name'] }}</p>
                                        <p class="text-xs text-zinc-500">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex items-center gap-1 ml-2">
                                        <button type="button" wire:click="updateQuantity({{ $item['menu_id'] }}, -1)" class="size-6 rounded bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-600 text-xs font-bold">-</button>
                                        <span class="w-5 text-center text-xs font-bold">{{ $item['quantity'] }}</span>
                                        <button type="button" wire:click="updateQuantity({{ $item['menu_id'] }}, 1)" class="size-6 rounded bg-indigo-500 text-white flex items-center justify-center hover:bg-indigo-600 text-xs font-bold">+</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-zinc-100 dark:border-zinc-700 my-3"></div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold">Total</span>
                            <span class="text-base font-bold text-indigo-600">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <button type="button" wire:click="nextStep" {{ empty($this->cart) ? 'disabled' : '' }} class="mt-4 w-full py-2.5 rounded-xl bg-indigo-500 text-white text-sm font-bold hover:bg-indigo-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Lanjut ke Meja →
                    </button>
                </div>
            </div>
        </div>

    @elseif ($step == 2)
        {{-- STEP 2: Pilih Meja --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Pilih Meja</flux:heading>

                    <flux:select
                        label="Nomor Meja"
                        placeholder="-- Pilih Meja --"
                        wire:model="table_id"
                    >
                        @foreach ($this->availableTables as $table)
                            <option value="{{ $table->id }}">Meja {{ $table->number_table }}</option>
                        @endforeach
                    </flux:select>
                    @error('table_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:card>

                <flux:card>
                    <flux:heading size="lg" class="mb-4">Ringkasan Pesanan</flux:heading>
                    <div class="overflow-x-auto">
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>Menu</flux:table.column>
                                <flux:table.column class="text-right">Harga</flux:table.column>
                                <flux:table.column class="text-right">Jumlah</flux:table.column>
                                <flux:table.column class="text-right">Subtotal</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($this->cart as $item)
                                    <flux:table.row>
                                        <flux:table.cell>{{ $item['name'] }}</flux:table.cell>
                                        <flux:table.cell class="text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</flux:table.cell>
                                        <flux:table.cell class="text-right">{{ $item['quantity'] }}</flux:table.cell>
                                        <flux:table.cell class="text-right font-medium">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </flux:card>
            </div>

            <div class="lg:col-span-1">
                <flux:card class="sticky top-6">
                    <flux:heading size="sm" class="mb-4">Total</flux:heading>
                    <div class="space-y-2 text-sm">
                        @foreach ($this->cart as $item)
                            <div class="flex justify-between">
                                <span class="text-zinc-500 truncate">{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                                <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <flux:separator class="my-3" />
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Total Pesanan</span>
                        <span class="font-bold text-xl text-indigo-600">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="mt-6 space-y-2">
                        <flux:button variant="primary" class="w-full" wire:click="nextStep">
                            Lanjut ke Pembayaran
                        </flux:button>
                        <flux:button variant="ghost" class="w-full" wire:click="previousStep">
                            Kembali
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </div>

    @elseif ($step == 3)
        {{-- STEP 3: Pembayaran --}}
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Metode Pembayaran</flux:heading>

                        <flux:select
                            label="Metode Bayar"
                            placeholder="-- Pilih Metode --"
                            wire:model="payment_method"
                        >
                            <option value="Cash">Cash</option>
                            <option value="Transfer">Transfer</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Credit Card">Credit Card</option>
                        </flux:select>
                        @error('payment_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('table_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </flux:card>

                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Ringkasan Pesanan</flux:heading>
                        <div class="overflow-x-auto">
                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>Menu</flux:table.column>
                                    <flux:table.column class="text-right">Harga</flux:table.column>
                                    <flux:table.column class="text-right">Jumlah</flux:table.column>
                                    <flux:table.column class="text-right">Subtotal</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @foreach ($this->cart as $item)
                                        <flux:table.row>
                                            <flux:table.cell>{{ $item['name'] }}</flux:table.cell>
                                            <flux:table.cell class="text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</flux:table.cell>
                                            <flux:table.cell class="text-right">{{ $item['quantity'] }}</flux:table.cell>
                                            <flux:table.cell class="text-right font-medium">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        </div>
                    </flux:card>
                </div>

                <div class="lg:col-span-1">
                    <flux:card class="sticky top-6">
                        <flux:heading size="sm" class="mb-4">Konfirmasi Pesanan</flux:heading>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-zinc-500">Meja</span>
                                <span class="font-medium">
                                    @php $selectedTable = $this->availableTables->firstWhere('id', (int) $table_id); @endphp
                                    Meja {{ $selectedTable?->number_table ?? '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-500">Tanggal</span>
                                <span>{{ now()->format('d-m-Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-500">Metode Bayar</span>
                                <span class="font-medium">{{ $payment_method ?: '-' }}</span>
                            </div>
                            <flux:separator />
                            <div class="flex justify-between">
                                <span class="font-semibold">Total Bayar</span>
                                <span class="font-bold text-xl text-indigo-600">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-2">
                            <flux:button type="submit" variant="primary" class="w-full">
                                Buat Pesanan
                            </flux:button>
                            <flux:button variant="ghost" class="w-full" wire:click="previousStep">
                                Kembali
                            </flux:button>
                        </div>
                    </flux:card>
                </div>
            </div>
        </form>
    @endif
</div>
