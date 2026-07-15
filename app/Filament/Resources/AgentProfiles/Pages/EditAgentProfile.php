<?php

namespace App\Filament\Resources\AgentProfiles\Pages;

use App\Filament\Resources\AgentProfiles\AgentProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAgentProfile extends EditRecord
{
    protected static string $resource = AgentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
