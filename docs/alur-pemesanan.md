# Alur Pemesanan - Sistem Manajemen Restoran

## Arsitektur

Aplikasi menggunakan **Laravel 13 + Livewire v4 + Flux UI v2**.

Seluruh CRUD dan logika bisnis ditangani oleh **Livewire anonymous components** yang didefinisikan langsung di file Blade (`⚡` prefix).

---

## Halaman Pesanan Baru (`/order-baru`)

Halaman **single-page** (bukan wizard) yang menggabungkan pemilihan menu, keranjang, pemilihan meja, dan pembayaran dalam satu tampilan — terinspirasi dari desain food ordering app modern.

### Layout

```
┌─────────────────────────────────┬──────────────────────┐
│  🔍 Cari menu...   🔔 👤      │  Pesanan             │
│                                 │                      │
│  [🍕 Pizza] [🍔 Burger] [🍟]  │  • Seblak  x2 Rp40rb │
│                                 │  • Nasgor  x1 Rp30rb │
│  ── Makanan ──                 │                      │
│  ┌──────┐ ┌──────┐ ┌──────┐   │  Meja:      [▼ Meja] │
│  │  📷  │ │  📷  │ │  📷  │   │  Bayar:   [▼ QRIS]  │
│  │Seblak│ │Nasgor│ │Mie   │   │                      │
│  │Rp20rb│ │Rp30rb│ │Rp15rb│   │  Sub Total  Rp70.000 │
│  │[Pesan]│ │Pesan │ │Pesan │   │  Total      Rp70.000 │
│  └──────┘ └──────┘ └──────┘   │                      │
│                                 │  ┌──────────────────┐│
│  ── Minuman ──                  │  │  Buat Pesanan    ││
│  ┌──────┐ ┌──────┐             │  └──────────────────┘│
│  │ ...  │ │ ...  │             │                      │
│  └──────┘ └──────┘             │                      │
└─────────────────────────────────┴──────────────────────┘
```

### Komponen Halaman

| Area | Elemen | Deskripsi |
|---|---|---|
| **Top Bar** | Search | Input cari menu, filter berdasarkan nama |
| | Tombol Filter | Filter tambahan (opsional) |
| | Notifikasi | Badge jumlah item di keranjang |
| | User Avatar | Inisial user login |
| **Categories** | Chips horizontal | Kategori menu, klik untuk filter. Pilihan aktif di-highlight. Tombol ✕ untuk reset. |
| **Menu Grid** | Card menu | 3 kolom per baris. Card terdiri dari: gambar (tinggi 144px, object-cover), nama, harga, tombol "Pesan" atau +/− qty. Menu yang sudah di keranjang ditandai dengan ring oranye dan badge jumlah di pojok gambar. |
| **Cart Sidebar** | Header | "Pesanan" + badge jumlah item |
| | Cart Items | Daftar item: nama, harga, qty +/−. Scrollable jika banyak. |
| | Pilih Meja | Dropdown meja (hanya tersedia) |
| | Metode Bayar | Dropdown: Cash, Transfer, QRIS, Debit Card, Credit Card |
| | Summary | Sub Total → Total |
| | Tombol "Buat Pesanan" | Full-width oranye. Validasi meja & metode bayar sebelum submit. |

### Alur Pengguna

1. **Pilih Menu** — Klik tombol "Pesan" pada card menu → item masuk ke keranjang kanan
2. **Atur Quantity** — Gunakan tombol +/− baik di card menu maupun di keranjang
3. **Filter** — Klik kategori untuk filter menu, atau gunakan search
4. **Pilih Meja** — Dropdown di panel kanan
5. **Pilih Metode Bayar** — Dropdown di panel kanan
6. **Buat Pesanan** — Klik tombol "Buat Pesanan" → semua tersimpan dalam 1 transaksi

### Proses Simpan

Ketika tombol **"Buat Pesanan"** diklik:

1. **Validasi** — cart tidak kosong, meja terpilih, metode bayar terpilih
2. **Create `Pesanan`** — `user_id` (login), `table_id`, `order_date` (hari ini), `status` (pending), `total_price` (kalkulasi otomatis)
3. **Create `Detail_Pesanan`** — setiap item di keranjang
4. **Create `Payment`** — `payment_status` = Pending
5. **Update meja** — `status` → "tidak tersedia"
6. **Redirect** → halaman daftar Pesanan

Semua dalam **DB transaction** — jika ada error, data tidak akan tersimpan sebagian.

---

## State Management (Livewire Properties)

```php
public array $cart = [];            // Item di keranjang
public $table_id = '';              // ID meja terpilih
public $payment_method = '';        // Metode bayar
public $search = '';                // Kata kunci pencarian
public $activeCategoryId = null;    // Filter kategori aktif
```

### Struktur Cart Item

```php
[
    'menu_id'  => 1,
    'name'     => 'Nasi Goreng',
    'price'    => 25000,
    'quantity' => 2,
    'stock'    => 50,
]
```

### Computed Properties

| Property | Fungsi |
|---|---|
| `$this->categories` | Semua kategori with relasi menus |
| `$this->availableTables` | Meja dengan status `tersedia` |
| `$this->filteredMenus` | Menu yang difilter berdasarkan search & kategori, dikelompokkan per kategori |
| `$this->cartTotal` | Total harga semua item |
| `$this->cartCount` | Total jumlah item (sum of quantities) |

---

## File yang Terlibat

### Halaman Pesanan Baru
| File | Fungsi |
|---|---|
| `resources/views/pages/order/⚡create.blade.php` | Halaman single-page pemesanan (Livewire component) |
| `routes/web.php:9` | Route: `GET /order-baru → order.create` |

### Model (DIUBAH)
| File | Perubahan |
|---|---|
| `app/Models/Pesanan.php` | Tambah relasi `detailPesanans(): HasMany`, `payments(): HasMany` |
| `app/Models/Detail_Pesanan.php` | Tambah relasi `pesanan(): BelongsTo` |
| `app/Models/Table.php` | Fix relasi dari `Order` ke `Pesanan` |

### Navigasi & Dashboard
| File | Perubahan |
|---|---|
| `resources/views/layouts/app/sidebar.blade.php` | Tambah menu "Pesanan Baru" |
| `resources/views/dashboard.blade.php` | Tambah tombol "Buat Pesanan Baru" di hero |

---

## Catatan Penting

- Hanya **meja dengan status `tersedia`** yang muncul di dropdown
- Setelah pesanan dibuat, status meja otomatis berubah menjadi `tidak tersedia`
- `total_price` dihitung **otomatis** dari subtotal item — tidak perlu input manual
- `user_id` menggunakan **user yang sedang login** (kasir/admin)
- Halaman index/list yang sudah ada (Pesanan, Detail Pesanan, Payment) tetap dipertahankan untuk riwayat
- Semua operasi simpan dibungkus `DB::transaction` — aman dari data setengah jadi
