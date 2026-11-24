<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

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
        $active = (clone $query)->where('status', 'active')->where('end_date', '>=', now())->count();
        $expiring = (clone $query)->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->count();

        return "{$total} ".Str::plural('subscription', $total).", {$active} active, {$expiring} expiring within 7 days.";
    }
}
