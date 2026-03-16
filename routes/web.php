<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return redirect('/admin/login');
// });

// Route::get('/start', function () {
//     return redirect()->away('https://forms.gle/rG4Jf1xoguD67mH26');
// });

Route::get('/', function () {
    return Inertia::render('Home');
});
