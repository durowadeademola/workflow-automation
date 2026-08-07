<?php

namespace App\Filament\Resources\Marketing;

use App\Filament\Resources\Marketing\Pages\CreateNewsletter;
use App\Filament\Resources\Marketing\Pages\EditNewsletter;
use App\Filament\Resources\Marketing\Pages\ListNewsletters;
use App\Filament\Resources\Marketing\Schemas\NewsletterForm;
use App\Filament\Resources\Marketing\Tables\NewslettersTable;
use App\Models\Newsletter;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * One-off broadcast emails — distinct from MarketingJourneyResource's drip
 * sequences. client_id null is Blueflow's own agency newsletter (audience:
 * NewsletterSubscriber); set is a client's own broadcast to their Customers.
 * Unlike AutomationWorkflow's client_id (where null is reserved for the
 * shared chat-widget-reply/crawler system rows on a different resource
 * entirely), null here is a real, admin-manageable row of this same
 * resource — so, unlike MarketingJourneyResource, the query isn't restricted
 * to whereNotNull('client_id').
 */
class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationLabel = 'Newsletters';

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 23;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::EnvelopeOpen;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) $user && ($user->is_admin || (($user->is_client || $user->is_agent) && $user->client?->hasFeature('marketing-automation')));
    }

    public static function form(Schema $schema): Schema
    {
        return NewsletterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewslettersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletters::route('/'),
            'create' => CreateNewsletter::route('/create'),
            'edit' => EditNewsletter::route('/{record}/edit'),
        ];
    }

    /**
     * Admins see every newsletter (Blueflow's own agency ones plus every
     * client's) for support visibility, same as MarketingJourneyResource;
     * a client only ever sees their own.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('client')->latest();

        if (! auth()->user()?->is_admin) {
            $query->where('client_id', auth()->user()?->client_id);
        }

        return $query;
    }
}
