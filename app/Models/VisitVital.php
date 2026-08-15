<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitVital extends Model
{
    protected $fillable = ['visit_id', 'temperature', 'systolic_pressure', 'diastolic_pressure', 'pulse', 'respiratory_rate', 'oxygen_saturation', 'weight', 'height', 'bmi', 'blood_sugar', 'recorded_by', 'measured_at'];
    protected $casts = ['measured_at' => 'datetime'];
}
