<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getSubheading(): ?string
    {
        $query = static::getResource()::getEloquentQuery();
        $total = (clone $query)->count();
        $new = (clone $query)->where('status', 'new')->count();

        return "{$total} ".Str::plural('lead', $total).", {$new} not yet contacted.";
    }
}
