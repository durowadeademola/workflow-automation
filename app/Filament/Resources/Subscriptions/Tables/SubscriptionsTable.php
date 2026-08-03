<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\User;
use App\Notifications\RefundProcessed;
use App\Notifications\RefundRejected;
use App\Services\PaystackService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service')
                    ->label('Service')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($record) => $record->serviceLabel()),
                TextColumn::make('name')
                    ->label('Plan'),
                TextColumn::make('amount')
                    ->label('Charged')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('credit_applied')
                    ->label('Credit used')
                    ->money('NGN')
                    ->placeholder('—')
                    ->toggleable(),
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
                BadgeColumn::make('refund_status')
                    ->label('Refund')
                    ->placeholder('—')
                    ->colors([
                        'warning' => 'requested',
                        'success' => 'processed',
                        'danger' => 'rejected',
                    ]),
                TextColumn::make('refund_amount')
                    ->label('Refund amount')
                    ->money('NGN')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('paystack_channel')
                    ->label('Channel')
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('paystack_paid_at')
                    ->label('Paid at')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('paystack_amount_charged')
                    ->label('Paystack amount')
                    ->money('NGN')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paystack_transaction_id')
                    ->label('Paystack txn ID')
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paystack_reference')
                    ->label('Reference')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('service')
                    ->options([
                        'chat-widget' => 'Chat Widget',
                        'marketing-automation' => 'Marketing Automation',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('refund_status')
                    ->label('Refund status')
                    ->options([
                        'requested' => 'Requested',
                        'processed' => 'Processed',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('processRefund')
                    ->label('Process Refund')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn ($record) => $record->refund_status === 'requested')
                    ->requiresConfirmation()
                    ->modalDescription(fn ($record) => 'This will refund ₦'.number_format($record->refund_amount).' via Paystack to the client\'s original payment method. This cannot be undone.')
                    ->action(function ($record) {
                        try {
                            $result = app(PaystackService::class)->refundTransaction(
                                $record->paystack_transaction_id,
                                $record->refund_amount * 100,
                            );

                            $record->update([
                                'refund_status' => 'processed',
                                'refund_reviewed_at' => now(),
                                'refund_processed_at' => now(),
                                'refund_reference' => $result['data']['id'] ?? null,
                            ]);

                            $recipients = User::where('client_id', $record->client_id)
                                ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                                ->get();

                            if ($recipients->isNotEmpty()) {
                                LaravelNotification::send($recipients, new RefundProcessed($record));
                            }

                            Notification::make()->title('Refund processed')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Refund failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Action::make('rejectRefund')
                    ->label('Reject Refund')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->refund_status === 'requested')
                    ->requiresConfirmation()
                    ->modalDescription('The client keeps their money in their pocket but not the widget — declining restores their access for the remainder of the original period instead.')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Reason (shown to the client)')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $restoring = $record->refund_original_end_date && $record->refund_original_end_date->isFuture();

                        $record->update([
                            'refund_status' => 'rejected',
                            'refund_reviewed_at' => now(),
                            'refund_rejection_reason' => $data['rejection_reason'],
                            'cancelled_at' => $restoring ? null : $record->cancelled_at,
                            'cancellation_reason' => $restoring ? null : $record->cancellation_reason,
                            'end_date' => $restoring ? $record->refund_original_end_date : $record->end_date,
                        ]);

                        $recipients = User::where('client_id', $record->client_id)
                            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                            ->get();

                        if ($recipients->isNotEmpty()) {
                            LaravelNotification::send($recipients, new RefundRejected($record));
                        }

                        Notification::make()->title('Refund rejected')->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
