<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'user_id',
        'rating',
        'comment',
    ];

    // علاقة الطبيب (وهو مستخدم في جدول users)
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // علاقة المريض الذي قام بالتقييم
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}