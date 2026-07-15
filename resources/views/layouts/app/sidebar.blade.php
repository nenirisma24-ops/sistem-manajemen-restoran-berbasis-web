<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if (auth()->user()->role === 'admin')
                        <flux:sidebar.item icon="tag" :href="route('category.index')" :current="request()->routeIs('category.index')" wire:navigate>
                            {{ __('Categories') }}
                        </flux:sidebar.item>
                    @endif

                    {{-- Menu khusus Admin --}}
                    @if (auth()->user()->role === 'admin')
                        <flux:sidebar.item icon="book-open" :href="route('menu.index')" :current="request()->routeIs('menu.index')" wire:navigate>
                            {{ __('Menus') }}
                        </flux:sidebar.item>
                    @endif

                    {{-- Navigasi Pesanan Baru (Tampilan Modern) --}}
                    @if (in_array(auth()->user()->role, ['admin', 'kasir', 'customer'], true))
                        <flux:sidebar.item 
                            icon="shopping-cart" 
                            :href="route('order.create')" 
                            :current="request()->routeIs('order.create')" 
                            wire:navigate>
                            {{ __('Pesanan Baru') }}
                        </flux:sidebar.item>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'pelayan'], true))
                        <flux:sidebar.item icon="document-text" :href="route('detail-pesanan.index')" :current="request()->routeIs('detail-pesanan.index')" wire:navigate>
                            {{ __('Detail Pesanan') }}
                        </flux:sidebar.item>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'pelayan'], true))
                        <flux:sidebar.item icon="table-cells" :href="route('table.index')" :current="request()->routeIs('table.index')" wire:navigate>
                            {{ __('Tables') }}
                        </flux:sidebar.item>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'kasir', 'pelayan', 'koki', 'customer'], true))
                        <flux:sidebar.item icon="receipt-percent" :href="route('pesanan.index')" :current="request()->routeIs('pesanan.index')" wire:navigate>
                            {{ __('Pesanans') }}
                        </flux:sidebar.item>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'kasir', 'customer'], true))
                        <flux:sidebar.item icon="credit-card" :href="route('payment.index')" :current="request()->routeIs('payment.index')" wire:navigate>
                            {{ __('Payments') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()->role === 'gudang')
                        <flux:sidebar.item icon="cube" :href="route('inventory')" :current="request()->routeIs('inventory')" wire:navigate>
                            {{ __('Inventaris') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">{{ __('Log out') }}</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>