<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\back\gnr_m_patients;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApiInvoiceController extends Controller
{
    public function index(): JsonResponse
    {
        $patient = $this->patient();
        $invoices = Invoice::with(['items', 'payments'])->where('patient_id', $patient->id)->orderByDesc('issued_at')->get();
        return response()->json(['success' => true, 'invoices' => $invoices]);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $this->authorizeOwnership($invoice);
        return response()->json(['success' => true, 'invoice' => $invoice->load(['items', 'payments'])]);
    }

    public function pdf(Invoice $invoice)
    {
        $patient = $this->authorizeOwnership($invoice);
        $invoice->load(['items', 'payments']);
        $clinic = DB::table('gnr_m_clinics')->where('id', $invoice->clinic_id)->first();
        return Pdf::loadView('pdf.invoice', compact('invoice', 'patient', 'clinic'))->download($invoice->number.'.pdf');
    }

    private function patient(): gnr_m_patients
    {
        abort_unless(auth()->user()?->hasSystemRole('patient'), 403);
        return gnr_m_patients::where('user_id', auth()->id())->firstOrFail();
    }

    private function authorizeOwnership(Invoice $invoice): gnr_m_patients
    {
        $patient = $this->patient();
        abort_unless((int) $invoice->patient_id === (int) $patient->id, 403);
        return $patient;
    }
}
