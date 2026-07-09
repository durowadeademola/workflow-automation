<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Domain;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $domains = $user?->is_admin
            ? Domain::all()
            : Domain::where('client_id', $user?->client_id)->get();

        return response()->json($domains);
    }
}