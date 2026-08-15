<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $fillable = ['invoice_id', 'amount', 'method', 'reference', 'paid_at', 'received_by', 'notes'];
    protected $casts = ['paid_at' => 'datetime'];
}
