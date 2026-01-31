<?php

namespace App\Filament\Resources\AIAgents\Pages;

use App\Filament\Resources\AIAgents\AIAgentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAIAgent extends EditRecord
{
    protected static string $resource = AIAgentResource::class;

    protected function getFormActions(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
            // ForceDeleteAction::make(),
            // RestoreAction::make(),
        ];
    }
}
