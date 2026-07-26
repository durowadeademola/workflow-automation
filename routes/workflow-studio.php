<?php

use App\Http\Controllers\WorkflowStudio\WorkflowController;
use App\Http\Controllers\WorkflowStudio\WorkflowStepController;
use App\Http\Controllers\WorkflowStudio\WorkflowStudioAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Workflow Studio
|--------------------------------------------------------------------------
|
| Deliberately separate from both Filament and the public Inertia site —
| not linked from any nav, its own session-based auth (see
| EnsureWorkflowStudioAuthenticated / config/workflow_studio.php), entirely
| independent of the `users` table.
|
*/

Route::prefix('workflow-studio')->name('workflow-studio.')->group(function () {
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('/login', [WorkflowStudioAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [WorkflowStudioAuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('workflow-studio.auth')->group(function () {
        Route::post('/logout', [WorkflowStudioAuthController::class, 'logout'])->name('logout');

        // Registered before the catch-all SPA route below so these take
        // precedence — Laravel matches routes in registration order.
        Route::prefix('api')->name('api.')->group(function () {
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

        Route::get('/{any?}', fn () => view('workflow-studio.app'))->where('any', '.*')->name('app');
    });
});
