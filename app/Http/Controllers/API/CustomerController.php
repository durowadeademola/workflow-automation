<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        // Log the request to storage/logs/laravel.log to see exactly what n8n is sending
        \Log::info('Customer Logs Data:', $request->all());

        return response()->json([
            'status' => 'success',
            'data' => null,
        ]);
    }
}
