@php
    $role = auth()->user()->role ?? 'guest';

    // 1. Mengambil data asli dari Database
    $totalOrders = \App\Models\Pesanan::count();
    $totalRevenue = \App\Models\Pesanan::where('status', 'selesai')->sum('total_price'); 
    $totalMenus = \App\Models\Menu::count();
    $totalTables = \App\Models\Table::count();

    // Mengambil 5 data pesanan dan menu terakhir
    $recentOrders = \App\Models\Pesanan::latest()->take(5)->get();
    $recentMenus = \App\Models\Menu::latest()->take(5)->get();

    // 2. Memasukkan data dari database ke dalam array untuk tampilan kotak (cards)
    $stats = [];

    // Sembunyikan Total Pesanan untuk Pelayan dan Customer
    if (!in_array($role, ['pelayan', 'customer'])) {
        $stats[] = [
            'label' => 'Total Pesanan',
            'value' => $totalOrders,
            'desc' => 'Seluruh pesanan masuk',
            'icon' => 'shopping-bag',
            'gradient' => 'from-indigo-400 to-purple-600',
            'bg' => 'bg-indigo-50 dark:bg-indigo-950/30',
            'text' => 'text-indigo-600 dark:text-indigo-400',
        ];
    }

    // Sembunyikan Total Pendapatan untuk Pelayan dan Customer (Hanya Admin & Kasir)
    if (in_array($role, ['admin', 'kasir'])) {
        $stats[] = [
            'label' => 'Total Pendapatan',
            'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'desc' => 'Pendapatan pesanan selesai',
            'icon' => 'banknotes',
            'gradient' => 'from-emerald-400 to-teal-600',
            'bg' => 'bg-emerald-50 dark:bg-emerald-950/30',
            'text' => 'text-emerald-600 dark:text-emerald-400',
        ];
    }

    // Total Menu dan Meja tetap ditampilkan
    $stats[] = [
        'label' => 'Total Menu',
        'value' => $totalMenus,
        'desc' => 'Menu makanan & minuman',
        'icon' => 'book-open',
        'gradient' => 'from-orange-400 to-rose-500',
        'bg' => 'bg-orange-50 dark:bg-orange-950/30',
        'text' => 'text-orange-600 dark:text-orange-400',
    ];

    $stats[] = [
        'label' => 'Total Meja',
        'value' => $totalTables,
        'desc' => 'Kapasitas meja tersedia',
        'icon' => 'squares-2x2',
        'gradient' => 'from-sky-400 to-blue-600',
        'bg' => 'bg-sky-50 dark:bg-sky-950/30',
        'text' => 'text-sky-600 dark:text-sky-400',
    ];
@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        {{-- ── TOP GRID: Hero + Stat Cards ────────────────────────── --}}
        <div class="grid gap-4 lg:grid-cols-5 lg:items-stretch">

            {{-- Hero Banner --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-900 to-zinc-900 px-6 py-8 text-white shadow-lg lg:col-span-2 border border-zinc-800">
                <div class="pointer-events-none absolute -right-10 -top-10 size-48 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-8 -left-8 size-40 rounded-full bg-indigo-500/20 blur-2xl"></div>

                <div class="relative flex h-full flex-col justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-400">Selamat datang kembali 👋</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-white">
                            {{ auth()->user()->name ?? 'Administrator' }}
                        </h1>
                        <p class="mt-1 text-sm text-zinc-400">
                            {{ now()->translatedFormat('l, d F Y') }}<br class="hidden sm:inline">
                            <span class="hidden sm:inline">&mdash; </span>Sistem Manajemen Restoran
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <flux:badge color="lime" class="font-semibold">● Sistem Aktif</flux:badge>
                        
                        {{-- Tombol Buat Pesanan Baru disembunyikan untuk Pelayan --}}
                        @if (in_array($role, ['admin', 'kasir', 'customer']))
                            <flux:button
                                variant="primary"
                                size="sm"
                                icon="shopping-cart"
                                class="!bg-white !text-indigo-900 hover:!bg-zinc-100"
                                :href="route('order.create')"
                                wire:navigate
                            >
                                Buat Pesanan Baru
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 gap-3 lg:col-span-3">
                @foreach ($stats as $stat)
                    <flux:card class="group relative overflow-hidden p-3 transition-shadow duration-300 hover:shadow-md">
                        <div class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r {{ $stat['gradient'] }} rounded-t-xl"></div>
                        <div class="flex items-center gap-3 pt-1">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg {{ $stat['bg'] }}">
                                <flux:icon :icon="$stat['icon']" class="size-4 {{ $stat['text'] }}" />
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    {{ $stat['label'] }}
                                </p>
                                <p class="text-xl font-bold tabular-nums leading-tight dark:text-white">
                                    {{ $stat['value'] }}
                                </p>
                            </div>
                        </div>
                    </flux:card>
                @endforeach
            </div>

        </div>

        {{-- ── BOTTOM SECTION ───────────────────────────────────────── --}}
        <div class="grid gap-6 lg:grid-cols-5">

            {{-- Recent Orders --}}
            <div class="lg:col-span-3">
                <flux:card class="h-full">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">Pesanan Terbaru</flux:heading>
                            <flux:subheading class="text-xs">Daftar pesanan yang sedang atau baru diproses</flux:subheading>
                        </div>
                        <flux:button variant="ghost" size="sm" icon="arrow-right" :href="route('pesanan.index', [], false)" wire:navigate>
                            Lihat semua
                        </flux:button>
                    </div>

                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>ID Pesanan</flux:table.column>
                            <flux:table.column>Meja</flux:table.column>
                            <flux:table.column>Total</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($recentOrders as $order)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <span class="font-medium">#ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </flux:table.cell>
                                    <flux:table.cell class="text-zinc-500 text-sm">
                                        Meja {{ $order->table_id ?? '-' }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if (strtolower($order->status) === 'selesai')
                                            <flux:badge size="sm" color="green" inset="top bottom">Selesai</flux:badge>
                                        @elseif (strtolower($order->status) === 'pending')
                                            <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                                        @elseif (strtolower($order->status) === 'dibatalkan')
                                            <flux:badge size="sm" color="red" inset="top bottom">Dibatalkan</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="blue" inset="top bottom">Diproses</flux:badge>
                                        @endif
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4">
                                        <div class="py-6 text-center text-sm text-zinc-400">
                                            Belum ada pesanan masuk.
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            </div>

            {{-- Recent Menus --}}
            <div class="lg:col-span-2">
                <flux:card class="h-full">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">Menu Terbaru</flux:heading>
                            <flux:subheading class="text-xs">Menu yang baru ditambahkan ke sistem</flux:subheading>
                        </div>
                        <flux:button variant="ghost" size="sm" icon="arrow-right" :href="route('menu.index', [], false)" wire:navigate>
                            Lihat semua
                        </flux:button>
                    </div>

                    <div class="flex flex-col divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($recentMenus as $menu)
                            <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="mt-1.5 size-2 shrink-0 rounded-full bg-gradient-to-br from-orange-400 to-rose-600"></div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium leading-snug dark:text-zinc-200">
                                        {{ $menu->name }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-zinc-400">
                                        Rp {{ number_format($menu->price ?? 0, 0, ',', '.') }} 
                                        &bull; {{ $menu->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-sm text-zinc-400">
                                Belum ada menu yang ditambahkan.
                            </div>
                        @endforelse
                    </div>
                </flux:card>
            </div>

        </div>
    </div>
</x-layouts::app>