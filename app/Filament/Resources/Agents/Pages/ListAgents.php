<?php

namespace App\Filament\Resources\Agents\Pages;

use App\Filament\Resources\Agents\AgentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListAgents extends ListRecords
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        $query = static::getResource()::getEloquentQuery();
        $total = (clone $query)->count();
        $active = (clone $query)->where('status', 'active')->count();

        return "{$total} ".Str::plural('agent', $total).", {$active} active.";
    }
}
