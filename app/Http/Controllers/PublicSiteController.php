<?php

namespace App\Http\Controllers;

use App\Models\back\cln_m_services as MedicalService;
use App\Models\back\doctors as Doctor;
use App\Models\back\gnr_m_clinics as Clinic;

class PublicSiteController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'clinics' => Clinic::query()
                ->where('act', 1)
                ->withCount('doctor')
                ->orderBy('name_ar')
                ->take(6)
                ->get(),
            'doctors' => Doctor::query()
                ->where('act', 1)
                ->with('gnr_m_clinics')
                ->orderByDesc('famous')
                // ->orderByDesc('total_rate')
                ->take(4)
                ->get(),
            'services' => MedicalService::query()
                ->where('act', 1)
                ->with('gnr_m_clinics')
                ->orderBy('name_ar')
                ->take(6)
                ->get(),
            'statistics' => [
                'clinics' => Clinic::where('act', 1)->count(),
                'doctors' => Doctor::where('act', 1)->count(),
                'services' => MedicalService::where('act', 1)->count(),
            ],
        ]);
    }

    public function about()
    {
        return view('public.about', [
            'statistics' => [
                'clinics' => Clinic::where('act', 1)->count(),
                'doctors' => Doctor::where('act', 1)->count(),
                'services' => MedicalService::where('act', 1)->count(),
            ],
        ]);
    }

    public function departments()
    {
        return view('public.departments', [
            'clinics' => Clinic::query()
                ->where('act', 1)
                ->withCount('doctor')
                ->orderBy('name_ar')
                ->get(),
        ]);
    }

    public function doctors()
    {
        return view('public.doctors', [
            'doctors' => Doctor::query()
                ->where('act', 1)
                ->with('gnr_m_clinics')
                ->orderByDesc('famous')
                ->orderBy('name_ar')
                ->paginate(12),
        ]);
    }

    public function services()
    {
        return view('public.services', [
            'services' => MedicalService::query()
                ->where('act', 1)
                ->with('gnr_m_clinics')
                ->orderBy('name_ar')
                ->paginate(18),
        ]);
    }
}
