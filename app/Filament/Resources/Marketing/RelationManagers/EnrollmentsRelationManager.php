<?php

namespace App\Filament\Resources\Marketing\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Read-only visibility into who's currently moving through a journey and
 * where they are — for support/debugging, not for manually editing an
 * enrollment's state.
 */
class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $title = 'Enrollments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('customer.display_name')
                    ->label('Customer'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'info',
                        'completed' => 'success',
                        'exited' => 'gray',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('current_step_order')
                    ->label('Current step')
                    ->formatStateUsing(fn (int $state) => 'Step '.($state + 1)),
                TextColumn::make('next_run_at')
                    ->label('Next send')
                    ->dateTime()
                    ->placeholder('Due now'),
                TextColumn::make('exit_reason')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('enrolled_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'exited' => 'Exited',
                        'failed' => 'Failed',
                    ]),
            ])
            ->defaultSort('enrolled_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
