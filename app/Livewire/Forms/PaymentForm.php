<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Payment;

class PaymentForm extends Form
{
    public ?Payment $payment = null;

    public $pesanan_id = '';

    public $payment_method = '';

    public $payment_total = '';

    public $payment_date = '';

    public $payment_status = '';

    public function rules()
    {
        return [
            'pesanan_id' => ['required', 'exists:pesanans,id'],
            'payment_method' => ['required', 'string', 'max:255'],
            'payment_total' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'payment_status' => ['required', 'string'],
        ];
    }

    public function store()
    {
        $this->validate();

        Payment::create([
            'pesanan_id' => $this->pesanan_id,
            'payment_method' => $this->payment_method,
            'payment_total' => $this->payment_total,
            'payment_date' => $this->payment_date,
            'payment_status' => $this->payment_status,
        ]);

        $this->reset();
    }

    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        $this->pesanan_id = $payment->pesanan_id;
        $this->payment_method = $payment->payment_method;
        $this->payment_total = $payment->payment_total;
        $this->payment_date = $payment->payment_date?->format('Y-m-d');
        $this->payment_status = $payment->payment_status;
    }

    public function update()
    {
        $this->validate();

        $this->payment->update([
            'pesanan_id' => $this->pesanan_id,
            'payment_method' => $this->payment_method,
            'payment_total' => $this->payment_total,
            'payment_date' => $this->payment_date,
            'payment_status' => $this->payment_status,
        ]);

        $this->reset();
    }

    public function save()
    {
        if ($this->payment) {
            $this->update();
        } else {
            $this->store();
        }
    }
}