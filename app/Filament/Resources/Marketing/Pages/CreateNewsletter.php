<?php

namespace App\Filament\Resources\Marketing\Pages;

use App\Filament\Resources\Marketing\NewsletterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletter extends CreateRecord
{
    protected static string $resource = NewsletterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->is_admin) {
            $data['client_id'] = auth()->user()?->client_id;
        } else {
            // The form's "Send as" select uses '' for the agency option —
            // Newsletter.client_id needs an actual null, not an empty string.
            $data['client_id'] = $data['client_id'] ?: null;
        }

        return $data;
    }
}
