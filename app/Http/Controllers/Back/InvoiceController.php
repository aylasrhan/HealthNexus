<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\back\cln_x_visits;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoices) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['unpaid', 'partial', 'paid'])],
            'search' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
        $query = DB::table('invoices')
            ->join('gnr_m_patients as patients', 'patients.id', '=', 'invoices.patient_id')
            ->leftJoin('gnr_m_clinics as clinics', 'clinics.id', '=', 'invoices.clinic_id')
            ->select('invoices.*', 'patients.f_name', 'patients.l_name', 'clinics.name_ar as clinic_name')
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('invoices.status', $status))
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(function ($nested) use ($search) {
                $nested->where('invoices.number', 'like', "%{$search}%")
                    ->orWhere('patients.f_name', 'like', "%{$search}%")
                    ->orWhere('patients.l_name', 'like', "%{$search}%");
            }))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('invoices.issued_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('invoices.issued_at', '<=', $to));

        $filtered = (clone $query)->select('invoices.id', 'invoices.total', 'invoices.paid', 'invoices.balance');
        $summary = DB::query()->fromSub($filtered, 'filtered')
            ->selectRaw('COUNT(*) invoice_count, COALESCE(SUM(total),0) total, COALESCE(SUM(paid),0) paid, COALESCE(SUM(balance),0) balance')->first();
        $invoices = $query->orderByDesc('invoices.id')->paginate(20)->withQueryString();

        return view('back.invoices.index', compact('invoices', 'summary', 'filters'));
    }

    public function store(cln_x_visits $visit)
    {
        $invoice = $this->invoices->syncFromVisit($visit, auth()->id());
        return redirect()->route('invoices.show', $invoice)->with('success', 'تم إنشاء الفاتورة من خدمات الزيارة.');
    }

    public function servicePrices(Request $request)
    {
        $clinicId = $request->integer('clinic') ?: null;
        $clinics = DB::table('gnr_m_clinics')->orderBy('name_ar')->get(['id', 'name_ar']);
        $services = DB::table('cln_m_services')->when($clinicId, fn ($q) => $q->where('clinic', $clinicId))
            ->orderBy('clinic')->orderBy('name_ar')->paginate(50)->withQueryString();
        return view('back.invoices.service-prices', compact('clinics', 'services', 'clinicId'));
    }

    public function updateServicePrices(Request $request)
    {
        $validated = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['required', 'numeric', 'min:0', 'max:999999999'],
        ]);
        DB::transaction(function () use ($validated) {
            foreach ($validated['prices'] as $serviceId => $price) {
                DB::table('cln_m_services')->where('id', (int) $serviceId)->update(['price' => round((float) $price, 2)]);
            }
        });
        return back()->with('success', 'تم تحديث أسعار الخدمات.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'payments']);
        $patient = DB::table('gnr_m_patients')->where('id', $invoice->patient_id)->first();
        $clinic = DB::table('gnr_m_clinics')->where('id', $invoice->clinic_id)->first();
        $doctor = DB::table('doctors')->where('id', $invoice->doctor_id)->first();
        return view('back.invoices.show', compact('invoice', 'patient', 'clinic', 'doctor'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'tax' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'due_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            foreach ($validated['items'] as $itemId => $values) {
                $item = $invoice->items()->findOrFail($itemId);
                $quantity = round((float) $values['quantity'], 2);
                $unitPrice = round((float) $values['unit_price'], 2);
                $item->update(['quantity' => $quantity, 'unit_price' => $unitPrice, 'total' => round($quantity * $unitPrice, 2)]);
            }
            $invoice->update([
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'due_at' => $validated['due_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
            $newTotal = max(0, round((float) $invoice->items()->sum('total') - (float) $invoice->discount + (float) $invoice->tax, 2));
            abort_if($newTotal < (float) $invoice->paid, 422, 'لا يمكن جعل إجمالي الفاتورة أقل من المبلغ المدفوع.');
            $this->invoices->recalculate($invoice);
        });

        return back()->with('success', 'تم تحديث الفاتورة.');
    }

    public function payment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(['cash', 'card', 'transfer'])],
            'reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $locked = Invoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            abort_if((float) $validated['amount'] > (float) $locked->balance, 422, 'قيمة الدفعة أكبر من المبلغ المتبقي.');
            InvoicePayment::create($validated + ['invoice_id' => $locked->id, 'paid_at' => now(), 'received_by' => auth()->id()]);
            $this->invoices->recalculate($locked);
        });

        return back()->with('success', 'تم تسجيل الدفعة بنجاح.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['items', 'payments']);
        $patient = DB::table('gnr_m_patients')->where('id', $invoice->patient_id)->first();
        $clinic = DB::table('gnr_m_clinics')->where('id', $invoice->clinic_id)->first();
        return Pdf::loadView('pdf.invoice', compact('invoice', 'patient', 'clinic'))->download($invoice->number.'.pdf');
    }
}
