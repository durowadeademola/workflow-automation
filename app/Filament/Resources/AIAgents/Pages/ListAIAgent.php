<?php

namespace App\Filament\Resources\AIAgents\Pages;

use App\Filament\Resources\AIAgents\AIAgentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAIAgent extends ListRecords
{
    protected static string $resource = AIAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
