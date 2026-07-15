<!-- Daftar Menu (Versi Rapi) -->
<div class="rounded-2xl border border-zinc-700 bg-zinc-900 p-6 shadow-sm">
    <h2 class="text-lg font-semibold text-white mb-4">Daftar Menu</h2>
    <div class="space-y-4">
        @forelse ($menus as $menu)
            <div class="flex flex-col gap-3 rounded-lg border border-zinc-700 p-4 bg-zinc-800">
                <div>
                    <p class="font-medium text-white">{{ $menu->name }}</p>
                    <p class="text-sm text-zinc-400">Rp {{ number_format($menu->price ?? 0, 0, ',', '.') }}</p>
                </div>

                <form method="POST" action="{{ route('customer.orders.store') }}" class="flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                    
                    <!-- Dropdown Meja -->
                    <select name="table_id" class="w-full rounded-lg bg-zinc-950 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-indigo-500" required>
                        <option value="">-- Pilih Meja --</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->id }}">Meja {{ $table->number ?? $table->id }}</option>
                        @endforeach
                    </select>

                    <!-- Input Jumlah dan Tombol Sejajar -->
                    <div class="flex gap-2">
                        <input type="number" name="jumlah" value="1" min="1" class="w-20 rounded-lg bg-zinc-950 border border-zinc-700 text-white px-3 py-2 text-sm text-center">
                        <button type="submit" class="flex-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm transition">
                            Pesan
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <p class="text-sm text-zinc-500">Belum ada menu aktif.</p>
        @endforelse
    </div>
</div> 