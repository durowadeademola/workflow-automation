<?php

namespace App\Filament\Resources\Marketing\Tables;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MarketingJourneysTable
{
    private const TRIGGER_LABELS = [
        '' => 'Manual only',
        'appointment_booked' => 'Appointment booked',
        'abandoned_booking' => 'Abandoned booking',
        're_engagement' => 'Re-engagement',
    ];

    private const SEGMENTS = [
        'all' => 'All contacts',
        'qualified_leads' => 'Qualified leads only',
        'registered_not_booked' => 'Registered, not yet booked',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('trigger_event')
                    ->label('Trigger')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => self::TRIGGER_LABELS[$state ?? ''] ?? $state),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('steps_count')
                    ->label('Steps')
                    ->counts('steps'),
                TextColumn::make('active_enrollments_count')
                    ->label('Active enrollees')
                    ->counts(['enrollments' => fn ($query) => $query->where('status', 'active')]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('enrollSegment')
                    ->label('Enroll a segment')
                    ->icon('heroicon-o-user-plus')
                    ->color('gray')
                    ->schema([
                        Select::make('segment')
                            ->label('Audience')
                            ->options(self::SEGMENTS)
                            ->default('all')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $client = $record->client;

                        if ($client?->hasReachedJourneyLimit()) {
                            Notification::make()
                                ->title('Journey limit reached')
                                ->body("This client's plan allows {$client->activeJourneyLimitForCurrentPlan()} active journeys.")
                                ->danger()
                                ->send();

                            throw new Halt;
                        }

                        $customers = self::resolveSegment($record, $data['segment']);
                        $enrolled = 0;

                        foreach ($customers as $customer) {
                            if (\App\Workflow\JourneyEnrollment::enrollIfEligible($record, $customer)) {
                                $enrolled++;
                            }
                        }

                        Notification::make()
                            ->title("Enrolled {$enrolled} contact(s)")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function resolveSegment($workflow, string $segment)
    {
        $query = Customer::where('client_id', $workflow->client_id)
            ->where('subscribed_to_marketing', true);

        return match ($segment) {
            'qualified_leads' => $query->where('is_qualified_lead', true)->get(),
            'registered_not_booked' => $query->whereNotNull('registered_at')->whereDoesntHave('appointments')->get(),
            default => $query->get(),
        };
    }
}
