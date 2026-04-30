<?php

namespace App\Http\Controllers;

use App\Jobs\RunNucleiScan;
use App\Models\Domain;
use App\Models\ScanResult;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function trigger(Request $request)
    {
        $domain = Domain::findOrFail($request->domain_id);

        dispatch(new RunNucleiScan($domain));

        return response()->json([
            'status'  => 'queued',
            'domain'  => $domain->url,
        ]);
    }

    public function results(Domain $domain)
    {
        return response()->json(
            ScanResult::where('domain_id', $domain->id)
                ->latest()
                ->get()
        );
    }
}