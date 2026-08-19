<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Repositories\Reviews\IReviewRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\back\doctors;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private IReviewRepository $reviewRepository;

    public function __construct(IReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }
  public function index()
    {
        // جلب المستخدمين الذين لديهم دور طبيب
        $doctors = \App\Models\User::role('doctor')
            // 🔴 التعديل هنا: استخدام النقطة لربط العيادة بالطبيب
            ->with(['doctor.gnr_m_clinics']) 
            ->withAvg('doctorReviews', 'rating')
            ->withCount('doctorReviews')
            ->orderByDesc('doctor_reviews_count')
            ->paginate(20);

        // إحصائيات التقييمات الحقيقية
        $summary = [
            'doctors' => \App\Models\User::role('doctor')->count(),
            'reviews' => \App\Models\Review::count(),
            'rated_doctors' => \App\Models\User::role('doctor')->has('doctorReviews')->count(),
        ];

        $ratingsAvailable = true; 

        return view('back.review.index', compact('doctors', 'summary', 'ratingsAvailable'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        try {

        $data = $this->reviewRepository->store($request);

            return redirect()->route('doctors.show', $data['data']);
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => $ex]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

    }
}
