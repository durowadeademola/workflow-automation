<?php

use App\Http\Controllers\WorkflowStudio\WorkflowController;
use App\Http\Controllers\WorkflowStudio\WorkflowStepController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Workflow Studio API
|--------------------------------------------------------------------------
|
| JSON API behind the admin panel's Workflow Studio page
| (App\Filament\Pages\WorkflowStudio, a React app in
| resources/js/workflow-studio). Admin-only via the normal `web` session —
| the `admin` middleware matters because the session is shared with the
| client/agent panel.
|
*/

Route::prefix('workflow-studio/api')
    ->name('workflow-studio.api.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/step-types', [WorkflowController::class, 'stepTypes'])->name('step-types');
        Route::get('/workflows', [WorkflowController::class, 'index'])->name('workflows.index');
        Route::post('/workflows', [WorkflowController::class, 'store'])->name('workflows.store');
        Route::get('/workflows/{workflow}', [WorkflowController::class, 'show'])->name('workflows.show');
        Route::put('/workflows/{workflow}', [WorkflowController::class, 'update'])->name('workflows.update');
        Route::delete('/workflows/{workflow}', [WorkflowController::class, 'destroy'])->name('workflows.destroy');
        Route::post('/workflows/{workflow}/test-run', [WorkflowController::class, 'testRun'])->name('workflows.test-run');

        Route::post('/workflows/{workflow}/steps', [WorkflowStepController::class, 'store'])->name('steps.store');
        Route::post('/workflows/{workflow}/steps/reorder', [WorkflowStepController::class, 'reorder'])->name('steps.reorder');
        Route::put('/steps/{step}', [WorkflowStepController::class, 'update'])->name('steps.update');
        Route::delete('/steps/{step}', [WorkflowStepController::class, 'destroy'])->name('steps.destroy');
    });

// Workflow Studio used to be a standalone app with its own login at /workflow-studio; its old
// pages (/workflow-studio, /workflow-studio/login, …) now lead to the admin page. The API above
// shares the prefix, so `api/…` is excluded here rather than relying on registration order.
Route::redirect('/workflow-studio/{any?}', '/admin/workflow-studio')->where('any', '^(?!api(/|$)).*');
