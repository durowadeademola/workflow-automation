<?php

namespace App\Filament\Resources\NewsletterSubscribers\Pages;

use App\Filament\Resources\NewsletterSubscribers\NewsletterSubscriberResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNewsletterSubscriber extends EditRecord
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * The form only exposes a simple on/off toggle — this keeps
     * subscribed_at/unsubscribed_at consistent whichever way it's flipped,
     * the same bookkeeping the public subscribe/unsubscribe routes do.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $wasSubscribed = $this->record->subscribed;
        $nowSubscribed = $data['subscribed'] ?? false;

        if ($nowSubscribed && ! $wasSubscribed) {
            $data['subscribed_at'] = now();
            $data['unsubscribed_at'] = null;
        } elseif (! $nowSubscribed && $wasSubscribed) {
            $data['unsubscribed_at'] = now();
        }

        return $data;
    }
}
