<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Pesanan;
use Illuminate\Validation\Rule;

class PesananForm extends Form
{
    public ?int $id = null;

    public string $user_id = '';

    public string $table_id = '';

    public string $order_date = '';

    public string $status = 'pending';

    public int $total_price = 0;

    public ?Pesanan $pesanan = null;

    public function rules(): array
    {
        return [
            'id' => [
                'nullable',
                'integer',
            ],

            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'table_id' => [
                'required',
                'exists:tables,id',
            ],

            'order_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'diproses',
                    'selesai',
                    'dibatalkan',
                ]),
            ],

            'total_price' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }

    public function store()
    {
        $this->validate();

        Pesanan::create([
            'user_id' => $this->user_id,
            'table_id' => $this->table_id,
            'order_date' => $this->order_date,
            'status' => $this->status,
            'total_price' => $this->total_price,
        ]);

        $this->reset();

        $this->status = 'pending';
        $this->total_price = 0;
    }

    public function setPesanan(Pesanan $pesanan): void
    {
        $this->pesanan = $pesanan;

        $this->id = $pesanan->id;
        $this->user_id = $pesanan->user_id;
        $this->table_id = $pesanan->table_id;
        $this->order_date = $pesanan->order_date;
        $this->status = $pesanan->status;
        $this->total_price = $pesanan->total_price;
    }

    public function update()
    {
        $this->validate();

        $this->pesanan->update([
            'user_id' => $this->user_id,
            'table_id' => $this->table_id,
            'order_date' => $this->order_date,
            'status' => $this->status,
            'total_price' => $this->total_price,
        ]);
    }
}
