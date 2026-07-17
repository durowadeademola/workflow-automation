<?php

namespace App\Filament\Resources\SupportTickets\Tables;

use App\Models\SupportTicket;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->visible(fn () => (bool) auth()->user()?->is_admin)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->searchable()
                    ->wrap(),
                BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'open',
                        'success' => 'answered',
                        'gray' => 'closed',
                    ])
                    ->formatStateUsing(fn (string $state) => SupportTicket::STATUSES[$state] ?? $state),
                TextColumn::make('user.name')
                    ->label('Opened by')
                    ->placeholder('—'),
                TextColumn::make('updated_at')
                    ->label('Last activity')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(SupportTicket::STATUSES),
            ])
            ->recordUrl(fn (SupportTicket $record) => static::viewUrl($record))
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (SupportTicket $record) => static::viewUrl($record)),
            ]);
    }

    private static function viewUrl(SupportTicket $record): string
    {
        $panel = auth()->user()?->is_admin ? 'admin' : 'user';

        return "/{$panel}/support-tickets/{$record->id}";
    }
}
