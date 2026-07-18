<?php

namespace App\Filament\Resources\KnowledgeBase\Pages;

use App\Filament\Resources\KnowledgeBase\KnowledgeBaseEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledgeBaseEntry extends CreateRecord
{
    protected static string $resource = KnowledgeBaseEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->is_admin) {
            $data['client_id'] = auth()->user()?->client_id;
        }

        return $data;
    }
}
