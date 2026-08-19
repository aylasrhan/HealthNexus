<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review; // تأكدي من استدعاء الموديل

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // التحقق من صحة البيانات القادمة من الموبايل
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'user_id'   => 'required|exists:users,id', 
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string',
        ]);

        // إنشاء التقييم في الداتابيز
        $review = Review::create([
            'doctor_id' => $request->doctor_id,
            'user_id'   => $request->user_id,
            'rating'    => $request->rating,
            'comment'   => $request->comment,
        ]);

        // إرجاع رد JSON ليفهمه تطبيق الفلاتر
        return response()->json([
            'status'  => true,
            'message' => 'تم إرسال التقييم بنجاح',
            'data'    => $review
        ], 201);
    }
}