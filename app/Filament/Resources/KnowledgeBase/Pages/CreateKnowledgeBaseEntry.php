<?php

namespace App\Filament\Resources\KnowledgeBase\Pages;

use App\Filament\Resources\KnowledgeBase\KnowledgeBaseEntryResource;
use App\Models\Client;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

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

    /**
     * FAQs answer instantly with no AI/n8n call involved (see
     * WidgetFaqController), so without a cap a client could stuff in
     * unlimited free-form content and mostly avoid ever using the paid AI
     * conversation. Articles aren't capped — they feed the AI's own RAG
     * context rather than bypass it, so they're not part of that concern.
     */
    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        if (($data['type'] ?? null) !== 'faq') {
            return;
        }

        $client = Client::find($data['client_id'] ?? null);

        if ($client?->hasReachedFaqLimit()) {
            Notification::make()
                ->title('FAQ limit reached')
                ->body("This client's plan allows {$client->faqLimitForCurrentPlan()} FAQs — remove one or upgrade their plan to add another.")
                ->danger()
                ->send();

            throw new Halt;
        }
    }
}
