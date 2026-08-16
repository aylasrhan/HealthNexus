<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = ['visit_id', 'patient_id', 'doctor_id', 'status', 'issued_at', 'notes'];
    protected $casts = ['issued_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class)->orderBy('sort_order');
    }
}
