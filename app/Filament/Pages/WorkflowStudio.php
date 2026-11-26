<?php

namespace App\Filament\Pages;

use App\Models\AutomationWorkflow;
use App\Models\AutomationWorkflowRun;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;

/**
 * Workflow Studio: the admin-only builder for the native workflow engine (app/Workflow). The page is just
 * a mount point: the builder is a React app (resources/js/workflow-studio) talking to the JSON
 * API in routes/workflow-studio.php.
 */
class WorkflowStudio extends Page
{
    // Lucide's "workflow" icon, the same one the homepage hero card shows (resources/svg/workflow.svg,
    // registered as the `bf` icon set in AppServiceProvider).
    protected static string|BackedEnum|null $navigationIcon = 'bf-workflow';

    protected static ?string $navigationLabel = 'Workflow Studio';

    protected static ?string $title = 'Workflow Studio';

    // Directly under Dashboard (-3).
    protected static ?int $navigationSort = -2;

    protected static ?string $slug = 'workflow-studio';

    protected string $view = 'filament.pages.workflow-studio';

    protected Width|string|null $maxContentWidth = Width::Full;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->is_admin;
    }

    public function getSubheading(): ?string
    {
        $total = AutomationWorkflow::count();
        $active = AutomationWorkflow::where('is_active', true)->count();
        $runs = AutomationWorkflowRun::count();

        return "{$total} ".Str::plural('workflow', $total).", {$active} active, ".number_format($runs).' '.Str::plural('run', $runs).' so far.';
    }
}
