<?php

namespace App\Http\Controllers;

use App\Jobs\RunNucleiScan;
use App\Models\Domain;
use App\Models\ScanResult;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScanController extends Controller
{
    public function trigger(Request $request)
    {
        $validated = $request->validate([
            'domain_id' => ['required', Rule::exists(Domain::class, 'id')],
        ]);

        $domain = Domain::findOrFail($validated['domain_id']);

        $this->authorizeDomain($request, $domain);

        dispatch(new RunNucleiScan($domain));

        return response()->json([
            'status'  => 'queued',
            'domain'  => $domain->url,
        ]);
    }

    public function results(Request $request, Domain $domain)
    {
        $this->authorizeDomain($request, $domain);

        return response()->json(
            ScanResult::where('domain_id', $domain->id)
                ->latest()
                ->get()
        );
    }

    private function authorizeDomain(Request $request, Domain $domain): void
    {
        $user = $request->user();

        abort_unless(
            $user?->is_admin || $user?->client_id === $domain->client_id,
            403,
            'You do not have access to this domain.'
        );
    }
}