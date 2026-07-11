# Alur Pemesanan - Sistem Manajemen Restoran

## Arsitektur

Aplikasi menggunakan **Laravel 13 + Livewire v4 + Flux UI v2**.

Tidak ada Controller tradisional. Seluruh CRUD dan logika bisnis ditangani oleh **Livewire anonymous components** yang didefinisikan langsung di file Blade (`⚡` prefix).

---

## Alur Pesanan Baru (`/order-baru`)

### Tampilan

Halaman `/order-baru` adalah wizard 3 langkah dalam satu halaman:

```
┌─────────────────────────────────────────────────────┐
│  1 ● Pilih Menu  ────  2 ○ Pilih Meja  ────  3 ○   │
│                                                     │
│  ┌────────────────────────┐  ┌────────────────────┐ │
│  │  Kategori Makanan      │  │  Keranjang         │ │
│  │  ┌──────┐ ┌──────┐    │  │  Nasi Goreng x2    │ │
│  │  │ Foto │ │ Foto │    │  │  Es Teh x1         │ │
│  │  │ Nasi │ │ Mie  │    │  │                    │ │
│  │  │+Tambah││+Tambah│   │  │  Total: Rp 55.000  │ │
│  │  └──────┘ └──────┘    │  │                    │ │
│  │  Kategori Minuman      │  │ [Lanjut ke Meja]   │ │
│  │  ┌──────┐ ┌──────┐    │  └────────────────────┘ │
│  │  │ ...  │ │ ...  │    │                         │
│  └────────────────────────┘                         │
└─────────────────────────────────────────────────────┘
```

### Langkah 1: Pilih Menu

- Menu ditampilkan dalam **grid card** dikelompokkan per kategori
- Setiap card menampilkan: foto, nama, harga
- Klik **"+ Tambah"** untuk memasukkan menu ke keranjang
- Setelah di keranjang, muncul tombol **`+` / `-`** untuk atur jumlah
- Keranjang (sidebar kanan) menampilkan semua item, quantity, dan total实时
- Tombol **"Lanjut ke Meja"** di bagian bawah keranjang

### Langkah 2: Pilih Meja

- Dropdown **"Nomor Meja"** — hanya menampilkan meja dengan status `tersedia`
- Tabel **Ringkasan Pesanan** — semua item menu, harga, jumlah, subtotal
- Sidebar kanan: rincian total per item dan total keseluruhan
- Tombol **"Lanjut ke Pembayaran"** dan **"Kembali"**

### Langkah 3: Pembayaran

- Dropdown **"Metode Bayar"**: Cash, Transfer, QRIS, Debit Card, Credit Card
- Tabel **Ringkasan Pesanan** (lengkap)
- Sidebar kanan: konfirmasi meja, tanggal, metode bayar, total bayar
- Tombol **"Buat Pesanan"** untuk menyelesaikan

### Proses Simpan

Ketika tombol **"Buat Pesanan"** diklik, sistem menjalankan dalam 1 transaksi:

1. **Create `Pesanan`**
   - `user_id` = user yang sedang login
   - `table_id` = meja yang dipilih
   - `order_date` = hari ini
   - `status` = `pending`
   - `total_price` = hasil kalkulasi dari semua item

2. **Create `Detail_Pesanan`** (per item di keranjang)
   - `pesanan_id` = dari pesanan yang baru dibuat
   - `menu_id`, `jumlah`, `subtotal`

3. **Create `Payment`**
   - `pesanan_id` = dari pesanan yang baru dibuat
   - `payment_method` = metode yang dipilih
   - `payment_total` = total pesanan
   - `payment_date` = hari ini
   - `payment_status` = `Pending`

4. **Update meja** → `status` = `tidak tersedia`

5. Redirect ke halaman **daftar Pesanan** dengan flash message sukses

---

## File yang Terlibat

### Wizard (BARU)
| File | Fungsi |
|---|---|
| `resources/views/pages/order/⚡create.blade.php` | Halaman wizard 3 langkah (Livewire component) |
| `routes/web.php:9` | Route: `GET /order-baru → order.create` |

### Model (DIUBAH)
| File | Perubahan |
|---|---|
| `app/Models/Pesanan.php` | Tambah relasi `detailPesanans(): HasMany`, `payments(): HasMany` |
| `app/Models/Detail_Pesanan.php` | Tambah relasi `pesanan(): BelongsTo` |
| `app/Models/Table.php` | Fix `pesanans()` — sebelumnya refer ke `Order` (tidak ada) |

### Navigasi (DIUBAH)
| File | Perubahan |
|---|---|
| `resources/views/layouts/app/sidebar.blade.php` | Tambah menu "Pesanan Baru" |
| `resources/views/dashboard.blade.php` | Tambah tombol "Buat Pesanan Baru" di hero |

---

## Data Flow Diagram

```
User → /order-baru
         │
         ├─ Step 1: Pilih Menu
         │    ├─ UI: grid card per kategori + cart sidebar
         │    └─ State: $cart (array of items)
         │
         ├─ Step 2: Pilih Meja
         │    ├─ UI: dropdown meja (tersedia) + ringkasan
         │    └─ State: $table_id
         │
         ├─ Step 3: Pembayaran
         │    ├─ UI: pilih metode bayar + konfirmasi
         │    └─ State: $payment_method
         │
         └─ Save → Pesanan + Detail_Pesanan + Payment + Update Table
```

---

## State Management (Livewire Properties)

```php
public int $step = 1;           // Langkah aktif (1, 2, atau 3)
public array $cart = [];        // Item di keranjang
public string $table_id = '';   // ID meja terpilih
public string $payment_method = ''; // Metode bayar
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
| `$this->cartTotal` | Total harga semua item (price × quantity) |
| `$this->cartCount` | Total jumlah item (sum of quantities) |

---

## Menambahkan Menu ke Keranjang

Metode `addToCart($menuId)`:
1. Cari menu berdasarkan ID
2. Jika sudah ada di keranjang → increment quantity
3. Jika belum ada → tambah item baru dengan quantity = 1

Metode `updateQuantity($menuId, $delta)`:
1. Cari item di keranjang
2. Jika new quantity ≤ 0 → hapus item
3. Jika new quantity ≤ stock → update quantity

---

## Catatan Penting

- Hanya **meja dengan status `tersedia`** yang muncul di dropdown Langkah 2
- Setelah pesanan dibuat, status meja otomatis berubah menjadi `tidak tersedia`
- `total_price` dihitung **otomatis** dari subtotal item — tidak perlu input manual
- `user_id` menggunakan **user yang sedang login** (kasir/admin)
- Halaman index/list yang sudah ada (Pesanan, Detail Pesanan, Payment) tetap dipertahankan untuk riwayat dan manajemen
