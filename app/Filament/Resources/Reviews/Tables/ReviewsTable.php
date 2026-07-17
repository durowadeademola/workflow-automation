<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color('warning'),
                TextColumn::make('description')
                    ->limit(60)
                    ->tooltip(fn (Review $record) => $record->description),
                TextColumn::make('company')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('location')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Review::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->visible(fn () => (bool) auth()->user()?->is_admin)
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(Review::STATUSES),
                TernaryFilter::make('is_featured')
                    ->label('Featured?')
                    ->visible(fn () => (bool) auth()->user()?->is_admin),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record) => auth()->user()?->is_admin && $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Review approved')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Review $record) => auth()->user()?->is_admin && $record->status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update(['status' => 'rejected', 'is_featured' => false]);
                        Notification::make()->title('Review rejected')->success()->send();
                    }),
                Action::make('toggleFeatured')
                    ->label(fn (Review $record) => $record->is_featured ? 'Unfeature' : 'Feature on homepage')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn (Review $record) => auth()->user()?->is_admin && $record->status === 'approved')
                    ->action(fn (Review $record) => $record->update(['is_featured' => ! $record->is_featured])),
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->is_admin),
                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->is_admin),
            ]);
    }
}
