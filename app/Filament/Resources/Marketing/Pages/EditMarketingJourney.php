<?php

namespace App\Filament\Resources\Marketing\Pages;

use App\Filament\Resources\Marketing\MarketingJourneyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMarketingJourney extends EditRecord
{
    protected static string $resource = MarketingJourneyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
