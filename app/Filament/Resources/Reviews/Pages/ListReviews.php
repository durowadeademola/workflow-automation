<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Leave a Review')
                // A client only ever needs one live/pending review of the
                // service itself — a prior rejection doesn't block trying
                // again. An admin only ever manages submissions, never
                // authors their own.
                ->visible(fn () => ! auth()->user()?->is_admin
                    && ! \App\Models\Review::where('client_id', auth()->user()?->client_id)
                        ->whereIn('status', ['pending', 'approved'])
                        ->exists()),
        ];
    }
}
