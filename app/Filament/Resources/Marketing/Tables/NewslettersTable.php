<?php

namespace App\Filament\Resources\Marketing\Tables;

use App\Services\NewsletterSender;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewslettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('client.name')
                    ->label('Sent as')
                    ->default('Blueflow (agency)')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'sent' => 'success',
                        'sending' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('recipients_count')
                    ->label('Sent to'),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Send now')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->modalDescription('This sends immediately to every subscribed recipient and can\'t be undone.')
                    ->action(function ($record) {
                        $client = $record->client;

                        if ($client && $client->hasReachedEmailSendLimit()) {
                            Notification::make()
                                ->title('Email send limit reached')
                                ->body("This client's plan allows {$client->emailSendLimitForCurrentPeriod()} emails this period — nothing was sent.")
                                ->danger()
                                ->send();

                            return;
                        }

                        $result = app(NewsletterSender::class)->send($record);

                        Notification::make()
                            ->title("Sent to {$result['sent']} of {$result['eligible']} eligible recipient(s)")
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->visible(fn ($record) => $record->status === 'draft'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
