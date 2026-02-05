<?php

namespace App\Filament\Resources\AIAgents;

use App\Filament\Resources\AIAgents\Pages\CreateAIAgent;
use App\Filament\Resources\AIAgents\Pages\EditAIAgent;
use App\Filament\Resources\AIAgents\Pages\ListAIAgent;
use App\Filament\Resources\AIAgents\Pages\ViewAIAgent;
use App\Filament\Resources\AIAgents\Schemas\AIAgentForm;
use App\Filament\Resources\AIAgents\Tables\AIAgentsTable;
use App\Models\AIAgent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AIAgentResource extends Resource
{
    protected static ?string $model = AIAgent::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'AI';

    protected static ?string $modelLabel = 'AI Insight';

    protected static ?string $pluralModelLabel = 'AI Insights';

    protected static ?int $navigationSort = 10;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        /** * We check if the user exists, is a client, and if their
         * associated client profile has the right type.
         */
        return $user && ($user->is_client || $user->is_agent);
        // && in_array(strtolower($user->client?->type), [
        //     'online-store',
        //     'real-estate',
        //     'sme',
        //     'ecommerce',
        // ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return AIAgentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AIAgentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAIAgent::route('/'),
            'view' => ViewAIAgent::route('/{record}'),
            // 'create' => CreateAIAgent::route('/create'),
            // 'edit' => EditAIAgent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('client_id', auth()->user()?->client_id);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
