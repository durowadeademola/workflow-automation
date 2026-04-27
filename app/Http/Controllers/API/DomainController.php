<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Domain;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $domains = Domain::all();

        return response()->json([$domains]);
    }
}