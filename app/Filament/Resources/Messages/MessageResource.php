<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages\CreateMessage;
use App\Filament\Resources\Messages\Pages\EditMessage;
use App\Filament\Resources\Messages\Pages\ListMessages;
use App\Filament\Resources\Messages\Pages\ViewMessage;
use App\Filament\Resources\Messages\Schemas\MessageForm;
use App\Filament\Resources\Messages\Tables\MessagesTable;
use App\Models\Message;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeftRight;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        /**
         * Type-whitelisted businesses see this either way — plus anyone with
         * the chat-widget service, since every widget conversation logs here
         * regardless of business type.
         */
        return $user
            && ($user->is_client || $user->is_agent)
            && (
                in_array(strtolower($user->client?->type), [
                    'online-store',
                    'real-estate',
                    'logistics',
                    'sme',
                    'ecommerce',
                ])
                || $user->client?->hasFeature('chat-widget')
            );
    }

    public static function form(Schema $schema): Schema
    {
        return MessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MessagesTable::configure($table);
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
            'index' => ListMessages::route('/'),
            'create' => CreateMessage::route('/create'),
            'edit' => EditMessage::route('/{record}/edit'),
            'view' => ViewMessage::route('/{record}'),
        ];
    }

    /**
     * One row per customer — whichever of their messages has the highest id
     * (i.e. their latest) — rather than every individual message. The full
     * thread for that customer is only a click away via the view page.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('customer')
            ->where('client_id', auth()->user()?->client_id)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('messages')
                    ->groupBy('customer_id');
            })
            ->orderBy('created_at', 'desc');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
