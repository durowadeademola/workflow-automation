<?php

namespace App\Filament\Resources\KnowledgeBase\Pages;

use App\Filament\Resources\KnowledgeBase\KnowledgeBaseEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditKnowledgeBaseEntry extends EditRecord
{
    protected static string $resource = KnowledgeBaseEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Only blocks converting an existing Article into a FAQ — editing an
     * FAQ that's already counted against the cap must never be blocked by
     * its own existence, or nobody could ever fix a typo once at the limit.
     */
    protected function beforeSave(): void
    {
        $wasFaq = $this->record->type === 'faq';
        $isFaq = ($this->form->getState()['type'] ?? null) === 'faq';

        if ($isFaq && ! $wasFaq && $this->record->client?->hasReachedFaqLimit()) {
            $client = $this->record->client;

            Notification::make()
                ->title('FAQ limit reached')
                ->body("This client's plan allows {$client->faqLimitForCurrentPlan()} FAQs — remove one or upgrade their plan before converting this into one.")
                ->danger()
                ->send();

            throw new Halt;
        }
    }
}
