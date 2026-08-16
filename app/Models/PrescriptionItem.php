<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = ['prescription_id', 'medication_name', 'dosage', 'frequency', 'duration', 'route', 'instructions', 'notes', 'sort_order'];
}
