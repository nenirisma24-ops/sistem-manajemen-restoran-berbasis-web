<?php

namespace App\Http\Controllers;

use App\Models\Detail_Pesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        $tables = Table::all(); // Mengambil data meja untuk dropdown
        $orders = Pesanan::with(['detailPesanans.menu'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.orders', compact('menus', 'orders', 'tables'));
    }

    public function store(Request $request)
    {
        // Validasi wajib pilih meja
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'jumlah' => 'required|integer|min:1',
            'table_id' => 'required|exists:tables,id', 
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        $subtotal = $menu->price * $request->jumlah;

        $pesanan = Pesanan::create([
            'user_id' => Auth::id(),
            'table_id' => $request->table_id, // Data tersimpan dengan benar
            'order_date' => now(),
            'status' => 'pending',
            'total_price' => $subtotal,
        ]);

        Detail_Pesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $menu->id,
            'jumlah' => $request->jumlah,
            'subtotal' => $subtotal,
        ]);

        return redirect()->route('customer.orders')->with('success', 'Pesanan berhasil dibuat.');
    }
}