<?php

namespace App\Filament\Resources\KnowledgeBase;

use App\Filament\Resources\KnowledgeBase\Pages\CreateKnowledgeBaseEntry;
use App\Filament\Resources\KnowledgeBase\Pages\EditKnowledgeBaseEntry;
use App\Filament\Resources\KnowledgeBase\Pages\ListKnowledgeBaseEntries;
use App\Filament\Resources\KnowledgeBase\Schemas\KnowledgeBaseEntryForm;
use App\Filament\Resources\KnowledgeBase\Tables\KnowledgeBaseEntriesTable;
use App\Models\KnowledgeBaseEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KnowledgeBaseEntryResource extends Resource
{
    protected static ?string $model = KnowledgeBaseEntry::class;

    protected static ?string $navigationLabel = 'Knowledge Base';

    protected static ?int $navigationSort = 21;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    /**
     * Unlike WidgetSettings (brand/persona config, owner-only), the
     * knowledge base is day-to-day content agents are well placed to keep
     * current too. Admins can see and manage every client's entries for
     * support purposes.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) $user && ($user->is_admin || (($user->is_client || $user->is_agent) && $user->client?->hasFeature('chat-widget')));
    }

    public static function form(Schema $schema): Schema
    {
        return KnowledgeBaseEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeBaseEntriesTable::configure($table);
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
            'index' => ListKnowledgeBaseEntries::route('/'),
            'create' => CreateKnowledgeBaseEntry::route('/create'),
            'edit' => EditKnowledgeBaseEntry::route('/{record}/edit'),
        ];
    }

    /**
     * Admins see every client's entries; a client only ever sees their own
     * business's.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with('client')
            ->orderBy('sort_order')
            ->orderBy('created_at');

        if (! auth()->user()?->is_admin) {
            $query->where('client_id', auth()->user()?->client_id);
        }

        return $query;
    }
}
