<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\back\cln_x_visits;
use App\Models\back\gnr_m_patients;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;

class ApiPrescriptionController extends Controller
{
    public function show(cln_x_visits $visit): JsonResponse
    {
        $patient = $this->ownedPatient($visit);
        $prescription = Prescription::with('items')->where('visit_id', $visit->id)->where('status', 'issued')->firstOrFail();

        return response()->json(['success' => true, 'prescription' => $prescription, 'patient' => [
            'id' => $patient->id,
            'name' => trim($patient->f_name.' '.$patient->l_name),
        ]]);
    }

    public function pdf(cln_x_visits $visit)
    {
        $patient = $this->ownedPatient($visit);
        $prescription = Prescription::with('items')->where('visit_id', $visit->id)->where('status', 'issued')->firstOrFail();

        return Pdf::loadView('pdf.prescription', compact('visit', 'patient', 'prescription'))
            ->download('prescription-'.$prescription->id.'.pdf');
    }

    private function ownedPatient(cln_x_visits $visit): gnr_m_patients
    {
        abort_unless(auth()->user()?->hasSystemRole('patient'), 403);
        $patient = gnr_m_patients::where('user_id', auth()->id())->firstOrFail();
        abort_unless((int) $visit->patient === (int) $patient->id, 403);

        return $patient;
    }
}
