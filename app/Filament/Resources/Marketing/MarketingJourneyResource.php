<?php

namespace App\Filament\Resources\Marketing;

use App\Filament\Resources\Marketing\Pages\CreateMarketingJourney;
use App\Filament\Resources\Marketing\Pages\EditMarketingJourney;
use App\Filament\Resources\Marketing\Pages\ListMarketingJourneys;
use App\Filament\Resources\Marketing\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Resources\Marketing\Schemas\MarketingJourneyForm;
use App\Filament\Resources\Marketing\Tables\MarketingJourneysTable;
use App\Models\AutomationWorkflow;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Client-facing CRUD over AutomationWorkflow/AutomationWorkflowStep rows
 * that belong to a specific client (client_id set) — the shared system
 * workflows (chat-widget-reply, website-crawler, client_id null) are never
 * reachable here, only through Workflow Studio.
 */
class MarketingJourneyResource extends Resource
{
    protected static ?string $model = AutomationWorkflow::class;

    protected static ?string $navigationLabel = 'Marketing Journeys';

    protected static ?string $modelLabel = 'Marketing Journey';

    protected static ?string $pluralModelLabel = 'Marketing Journeys';

    protected static ?int $navigationSort = 22;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Megaphone;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) $user && ($user->is_admin || (($user->is_client || $user->is_agent) && $user->client?->hasFeature('marketing-automation')));
    }

    public static function form(Schema $schema): Schema
    {
        return MarketingJourneyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketingJourneysTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketingJourneys::route('/'),
            'create' => CreateMarketingJourney::route('/create'),
            'edit' => EditMarketingJourney::route('/{record}/edit'),
        ];
    }

    /**
     * Never shows the shared system workflows (client_id null) — admins see
     * every client's journeys for support; a client only ever sees their own.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->whereNotNull('client_id')
            ->with('client')
            ->latest();

        if (! auth()->user()?->is_admin) {
            $query->where('client_id', auth()->user()?->client_id);
        }

        return $query;
    }
}
