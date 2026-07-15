<?php

namespace App\Filament\Resources\AgentProfiles;

use App\Filament\Resources\AgentProfiles\Pages\CreateAgentProfile;
use App\Filament\Resources\AgentProfiles\Pages\EditAgentProfile;
use App\Filament\Resources\AgentProfiles\Pages\ListAgentProfiles;
use App\Filament\Resources\AgentProfiles\Schemas\AgentProfileForm;
use App\Filament\Resources\AgentProfiles\Tables\AgentProfilesTable;
use App\Models\AgentProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * A simple admin-only catalog of the AI agents/models deployed across
 * Blueflow's services (chat widget, WhatsApp, etc.) — record-keeping only,
 * nothing here drives actual behavior. Not to be confused with AIAgent /
 * AIAgentResource, which logs individual AI prompt/response events and is a
 * completely different, pre-existing feature.
 */
class AgentProfileResource extends Resource
{
    protected static ?string $model = AgentProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Sparkles;

    protected static ?string $navigationLabel = 'AI Agents';

    protected static ?string $modelLabel = 'AI Agent';

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->is_admin;
    }

    public static function form(Schema $schema): Schema
    {
        return AgentProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgentProfilesTable::configure($table);
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
            'index' => ListAgentProfiles::route('/'),
            'create' => CreateAgentProfile::route('/create'),
            'edit' => EditAgentProfile::route('/{record}/edit'),
        ];
    }
}
