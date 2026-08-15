<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\back\cln_x_visits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function syncFromVisit(cln_x_visits $visit, ?int $createdBy = null): Invoice
    {
        return DB::transaction(function () use ($visit, $createdBy) {
            $invoice = Invoice::firstOrCreate(['visit_id' => $visit->id], [
                'number' => 'PENDING-'.Str::uuid(),
                'patient_id' => $visit->patient,
                'clinic_id' => $visit->clinic,
                'doctor_id' => $visit->doctor_id,
                'issued_at' => now(),
                'created_by' => $createdBy,
            ]);

            if (str_starts_with($invoice->number, 'PENDING-')) {
                $invoice->number = 'INV-'.now()->format('Y').'-'.str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT);
                $invoice->save();
            }

            $services = DB::table('cln_x_visits_services as selected')
                ->join('cln_m_services as services', 'services.id', '=', 'selected.service')
                ->where('selected.visit_id', $visit->id)
                ->select('services.id', 'services.name_ar', 'services.name_en', 'services.price')
                ->distinct()->orderBy('services.id')->get();

            foreach ($services as $index => $service) {
                $item = $invoice->items()->firstOrCreate(['service_id' => $service->id], [
                    'description' => $service->name_ar ?: $service->name_en,
                    'quantity' => 1,
                    'unit_price' => $service->price ?? 0,
                    'total' => $service->price ?? 0,
                    'sort_order' => $index,
                ]);
                if (!$item->wasRecentlyCreated) {
                    $item->update([
                        'description' => $service->name_ar ?: $service->name_en,
                        'sort_order' => $index,
                        'total' => round((float) $item->quantity * (float) $item->unit_price, 2),
                    ]);
                }
            }
            $invoice->items()->whereNotIn('service_id', $services->pluck('id'))->delete();

            return $this->recalculate($invoice);
        });
    }

    public function recalculate(Invoice $invoice): Invoice
    {
        $subtotal = round((float) $invoice->items()->sum('total'), 2);
        $paid = round((float) $invoice->payments()->sum('amount'), 2);
        $total = max(0, round($subtotal - (float) $invoice->discount + (float) $invoice->tax, 2));
        $balance = max(0, round($total - $paid, 2));
        $status = $paid <= 0 ? 'unpaid' : ($balance > 0 ? 'partial' : 'paid');
        $invoice->update(compact('subtotal', 'total', 'paid', 'balance', 'status'));

        return $invoice->fresh(['items', 'payments']);
    }
}
