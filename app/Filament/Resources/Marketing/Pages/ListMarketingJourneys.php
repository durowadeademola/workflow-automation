<?php

namespace App\Filament\Resources\Marketing\Pages;

use App\Filament\Resources\Marketing\MarketingJourneyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMarketingJourneys extends ListRecords
{
    protected static string $resource = MarketingJourneyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
