<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentAppointments extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Booked Appointments';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) $user && $user->is_agent;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Appointment::query()
                ->where('client_id', auth()->user()?->client_id)
                ->where('status', '!=', 'cancelled')
                ->where('scheduled_at', '>=', now()))
            ->columns([
                TextColumn::make('name')
                    ->label('Visitor')
                    ->searchable(),
                TextColumn::make('scheduled_at')
                    ->label('Date & time')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Manual' ? 'gray' : 'info'),
                TextColumn::make('created_at')
                    ->label('Booked on')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('scheduled_at', 'asc')
            ->recordActions([
                EditAction::make()
                    ->form([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason'),
                    ]),
            ]);
    }
}
