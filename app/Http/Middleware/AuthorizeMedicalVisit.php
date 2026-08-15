<?php

namespace App\Http\Middleware;

use App\Models\back\cln_x_visits;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeMedicalVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->hasSystemRole('doctor'), 403);

        $visitId = $request->integer('visit');
        if (!$visitId && $request->filled('input')) {
            $table = match ($request->route()?->getName()) {
                'com.destroy' => 'cln_x_prev_com',
                'str.destroy' => 'cln_x_prev_str',
                'cln.destroy' => 'cln_x_prev_cln',
                'note.destroy' => 'cln_x_prev_not',
                default => null,
            };
            $visitId = $table ? (int) DB::table($table)->where('id', $request->integer('input'))->value('visit') : 0;
        }

        if ($visitId) {
            $visit = cln_x_visits::findOrFail($visitId);
            abort_if($request->filled('patient') && $request->integer('patient') !== (int) $visit->patient, 422);
            abort_unless($request->user()->can('writeMedicalFile', $visit), 403);
        } elseif (!in_array($request->method(), ['GET', 'HEAD'], true)) {
            abort(422, 'يلزم تحديد الزيارة الطبية.');
        }

        return $next($request);
    }
}
