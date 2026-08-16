<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'service_id', 'description', 'quantity', 'unit_price', 'total', 'sort_order'];
}
