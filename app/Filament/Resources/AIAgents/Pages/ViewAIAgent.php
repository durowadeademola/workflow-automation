<?php

namespace App\Filament\Resources\AIAgents\Pages;

use App\Filament\Resources\AIAgents\AIAgentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAIAgent extends ViewRecord
{
    protected static string $resource = AIAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //EditAction::make(),
        ];
    }
}
