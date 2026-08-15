<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['number', 'visit_id', 'patient_id', 'clinic_id', 'doctor_id', 'subtotal', 'discount', 'tax', 'total', 'paid', 'balance', 'status', 'issued_at', 'due_at', 'notes', 'created_by'];
    protected $casts = ['issued_at' => 'datetime', 'due_at' => 'date'];

    public function items() { return $this->hasMany(InvoiceItem::class)->orderBy('sort_order'); }
    public function payments() { return $this->hasMany(InvoicePayment::class)->orderByDesc('paid_at'); }
}
