<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/admin/login');
});

// Route::get('/', function () {
//     return Inertia::render('Home');
// });
