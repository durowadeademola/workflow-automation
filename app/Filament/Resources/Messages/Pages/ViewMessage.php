<?php

namespace App\Filament\Resources\Messages\Pages;

use App\Filament\Resources\Messages\MessageResource;
use App\Models\Message;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Collection;

class ViewMessage extends ViewRecord
{
    protected static string $resource = MessageResource::class;

    protected string $view = 'filament.resources.messages.pages.view-message';

    /**
     * The bound record is just whichever message happened to be that
     * customer's latest — the entry point into the thread, not the thing
     * being displayed. The full conversation is fetched separately below.
     */
    public function getThread(): Collection
    {
        return Message::query()
            ->where('customer_id', $this->record->customer_id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }
}
