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
        $ratingsAvailable = Schema::hasColumn('doctors', 'revisions_num')
            && Schema::hasColumn('doctors', 'total_rate');

        $doctorsQuery = doctors::query()->with(['user', 'gnr_m_clinics']);
        $doctors = ($ratingsAvailable
            ? $doctorsQuery->orderByDesc('revisions_num')
            : $doctorsQuery->orderBy('name_ar'))
            ->paginate(20);

        $summary = [
            'doctors' => doctors::query()->count(),
            'reviews' => $ratingsAvailable ? (int) doctors::query()->sum('revisions_num') : null,
            'rated_doctors' => $ratingsAvailable ? doctors::query()->where('revisions_num', '>', 0)->count() : null,
        ];

        return view('back.review.index', compact('doctors', 'summary', 'ratingsAvailable'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $doctor = $request['doctor'];
        return view('back.review.create',compact('doctor'));
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
