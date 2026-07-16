<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static ?string $title = 'Subscription History';

    /**
     * Subscriptions have a strict lifecycle (created via Billing/Paystack,
     * activated/expired by SubscriptionService) — this tab is a read-only
     * audit view for admins, not a place to freely create/detach them.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Plan'),
                TextColumn::make('amount')
                    ->label('Charged')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('credit_applied')
                    ->label('Credit used')
                    ->money('NGN')
                    ->placeholder('—'),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'gray' => 'expired',
                        'danger' => 'cancelled',
                    ]),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('cancelled_at')
                    ->label('Cancelled')
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cancellation_reason')
                    ->label('Cancellation reason')
                    ->placeholder('—')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paystack_channel')
                    ->label('Channel')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('paystack_paid_at')
                    ->label('Paid at')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('paystack_reference')
                    ->label('Reference')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record) => SubscriptionResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
