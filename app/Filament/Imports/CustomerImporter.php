<?php

namespace App\Filament\Imports;

use App\Models\Client;
use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;

/**
 * Bulk-adds/updates a client's own Customers from a CSV — mainly so a
 * business can seed its Marketing Automation contact list in one go instead
 * of creating each Customer by hand, though it works the same for any client
 * that can see this resource at all (chat-widget/ecommerce ones too).
 */
class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMappingForNewRecordsOnly()
                ->ignoreBlankState()
                ->rules(['max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('phone')
                ->ignoreBlankState()
                ->rules(['max:50']),
            ImportColumn::make('lead_intent')
                ->label('Interested in')
                ->ignoreBlankState()
                ->rules(['max:255']),
            ImportColumn::make('is_qualified_lead')
                ->label('Qualified lead')
                ->boolean()
                ->ignoreBlankState(),
            ImportColumn::make('subscribed_to_marketing')
                ->label('Subscribed to marketing')
                ->boolean()
                ->ignoreBlankState(),
        ];
    }

    /**
     * Matches by email within the importing client's own contacts — not the
     * base Importer's default (primary-key lookup, which a contacts CSV
     * would never have) — so re-importing an updated list updates existing
     * customers instead of creating duplicates. A brand-new row still
     * respects the plan's contact limit; updating an existing one doesn't,
     * since it isn't adding a new contact.
     */
    public function resolveRecord(): ?Customer
    {
        $clientId = $this->getOptions()['client_id'] ?? null;
        $email = $this->data['email'] ?? null;

        $existing = $email
            ? Customer::where('client_id', $clientId)->where('email', $email)->first()
            : null;

        if ($existing) {
            return $existing;
        }

        $client = Client::find($clientId);

        // Only a client actually on Marketing Automation has a contact
        // limit to enforce — for everyone else this table is just their
        // general contacts/leads list, with no cap tied to it.
        if ($client?->hasFeature('marketing-automation') && $client->hasReachedMarketingContactLimit()) {
            throw ValidationException::withMessages([
                'email' => "Contact limit reached ({$client->marketingContactLimitForCurrentPlan()} contacts on your current plan) — this row was skipped.",
            ]);
        }

        return new Customer(['client_id' => $clientId]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
