<?php

namespace App\Http\Controllers;

use App\Models\back\Appointment;
use App\Models\back\cln_x_visits;
use App\Models\back\doctors;
use App\Models\back\gnr_m_patients;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user->hasSystemRole('doctor')) {
            return $this->doctorDashboard($user);
        }

        if ($user->hasSystemRole('patient')) {
            return $this->patientDashboard($user);
        }

        abort_unless($user->hasSystemRole('super_admin', 'secretary'), 403);

        $today = Carbon::today();
        $appointments = Appointment::query()->where('is_deleted', 0);

        $todayAppointments = (clone $appointments)
            ->with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('time')
            ->limit(7)
            ->get();

        $stats = [
            'patients' => gnr_m_patients::query()->count(),
            'doctors' => doctors::query()->where('act', 1)->count(),
            'today_appointments' => (clone $appointments)->whereDate('appointment_date', $today)->count(),
            'pending_appointments' => (clone $appointments)->where('status', 0)->count(),
            'completed_appointments' => (clone $appointments)->where('status', 1)->count(),
            'visits' => cln_x_visits::query()->count(),
        ];

        $appointmentTrend = Appointment::query()
            ->where('is_deleted', 0)
            ->whereBetween('appointment_date', [$today->copy()->subDays(6), $today])
            ->selectRaw('DATE(appointment_date) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $trend = collect(range(6, 0))->map(function (int $daysAgo) use ($today, $appointmentTrend) {
            $date = $today->copy()->subDays($daysAgo);

            return [
                'label' => $date->locale('ar')->translatedFormat('D'),
                'value' => (int) ($appointmentTrend[$date->toDateString()] ?? 0),
            ];
        });

        $recentPatients = gnr_m_patients::query()
            ->with('user')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'todayAppointments' => $todayAppointments,
            'trend' => $trend,
            'recentPatients' => $recentPatients,
        ]);
    }

    private function doctorDashboard($user): View
    {
        $today = Carbon::today();
        $doctor = doctors::with('gnr_m_clinics')->where('user_id', $user->id)->first();
        $doctorIds = array_values(array_unique(array_filter([(int) $user->id, (int) $doctor?->id])));
        $appointments = Appointment::query()->whereIn('appointment_with', $doctorIds)->where('is_deleted', 0);

        $todayAppointments = (clone $appointments)->with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)->orderBy('time')->get();
        $upcomingAppointments = (clone $appointments)->with(['patient', 'doctor'])
            ->whereDate('appointment_date', '>', $today)->orderBy('appointment_date')->orderBy('time')->limit(7)->get();
        $patientUserIds = (clone $appointments)->distinct()->pluck('appointment_for');
        $patientIds = gnr_m_patients::whereIn('user_id', $patientUserIds)->pluck('id');

        $visitIds = collect();
        if ($doctor?->id) {
            foreach (['cln_x_prev_com', 'cln_x_prev_dia', 'cln_x_prev_icd10', 'cln_x_prev_not'] as $table) {
                $visitIds = $visitIds->merge(DB::table($table)->where('doc', $doctor->id)->pluck('visit'));
            }
        }
        $visitIds = $visitIds->filter()->unique()->values();

        $stats = [
            'today_appointments' => $todayAppointments->count(),
            'pending' => (clone $appointments)->where('status', 0)->count(),
            'patients' => $patientIds->count(),
            'visits' => $visitIds->count(),
            'unanswered_questions' => $doctor ? DB::table('question')->where('section', $doctor->subgrp)->where(fn ($q) => $q->whereNull('answer')->orWhere('answer', ''))->count() : 0,
        ];

        $appointmentTrend = (clone $appointments)->whereBetween('appointment_date', [$today->copy()->subDays(6), $today])
            ->selectRaw('DATE(appointment_date) day, COUNT(*) total')->groupBy('day')->pluck('total', 'day');
        $trend = collect(range(6, 0))->map(function (int $daysAgo) use ($today, $appointmentTrend) {
            $date = $today->copy()->subDays($daysAgo);
            return ['label' => $date->locale('ar')->translatedFormat('D'), 'value' => (int) ($appointmentTrend[$date->toDateString()] ?? 0)];
        });

        return view('dashboard-doctor', compact('user', 'doctor', 'stats', 'todayAppointments', 'upcomingAppointments', 'trend'));
    }

    private function patientDashboard($user): View
    {
        $today = Carbon::today();
        $patient = gnr_m_patients::where('user_id', $user->id)->first();
        $appointments = Appointment::query()->where('appointment_for', $user->id)->where('is_deleted', 0);
        $nextAppointments = (clone $appointments)->with('doctor')->whereDate('appointment_date', '>=', $today)
            ->orderBy('appointment_date')->orderBy('time')->limit(5)->get();
        $stats = [
            'upcoming' => (clone $appointments)->whereDate('appointment_date', '>=', $today)->count(),
            'completed' => (clone $appointments)->where('status', 1)->count(),
            'visits' => $patient ? cln_x_visits::where('patient', $patient->id)->count() : 0,
            'questions' => DB::table('question')->where('user_id', $user->id)->count(),
        ];

        return view('dashboard-patient', compact('user', 'patient', 'stats', 'nextAppointments'));
    }
}
