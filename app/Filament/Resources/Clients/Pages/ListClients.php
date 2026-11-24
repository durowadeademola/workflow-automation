<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

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
        $pending = (clone $query)->where('status', 'pending')->count();

        return "{$total} ".Str::plural('business', $total).", {$active} active, {$pending} waiting for approval.";
    }
}
