<?php

namespace App\Filament\Resources\Marketing\Pages;

use App\Filament\Resources\Marketing\MarketingJourneyResource;
use App\Models\Client;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Str;

class CreateMarketingJourney extends CreateRecord
{
    protected static string $resource = MarketingJourneyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->is_admin) {
            $data['client_id'] = auth()->user()?->client_id;
        }

        // Globally unique across every client (automation_workflows.slug is
        // unique table-wide, shared with the system chat-widget-reply/
        // crawler rows) — never shown to the client, purely internal.
        $data['slug'] = 'marketing-'.$data['client_id'].'-'.Str::slug($data['name']).'-'.Str::random(6);

        // Never let a client-created journey collide with the trigger
        // dispatchers built for the shared system workflows (webhook route,
        // RunScheduledWorkflows' cron scan) — journeys are only ever
        // advanced by AdvanceMarketingJourneys.
        $data['trigger_type'] = 'manual';

        return $data;
    }

    protected function beforeCreate(): void
    {
        $client = Client::find($this->form->getState()['client_id'] ?? null);

        if ($client?->hasReachedJourneyLimit()) {
            Notification::make()
                ->title('Journey limit reached')
                ->body("This client's plan allows {$client->activeJourneyLimitForCurrentPlan()} active journeys — deactivate one or upgrade the plan to add another.")
                ->danger()
                ->send();

            throw new Halt;
        }
    }
}
