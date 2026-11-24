<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
        $admins = (clone $query)->where('is_admin', true)->count();
        $clients = (clone $query)->where('is_client', true)->count();
        $agents = (clone $query)->where('is_agent', true)->count();

        return "{$total} ".Str::plural('user', $total).": {$admins} ".Str::plural('admin', $admins)
            .", {$clients} ".Str::plural('client', $clients).", {$agents} ".Str::plural('agent', $agents).'.';
    }
}
