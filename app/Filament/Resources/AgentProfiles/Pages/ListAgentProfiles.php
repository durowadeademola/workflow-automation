<?php

namespace App\Filament\Resources\AgentProfiles\Pages;

use App\Filament\Resources\AgentProfiles\AgentProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAgentProfiles extends ListRecords
{
    protected static string $resource = AgentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
