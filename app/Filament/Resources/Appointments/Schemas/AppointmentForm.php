<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Appointment Details')
                    ->description('Appointments booked through your AI chat widget appear here automatically — you can also log one manually below.')
                    ->schema([
                        Hidden::make('client_id')
                            ->default(auth()->user()?->client_id),
                        Hidden::make('source')
                            ->default('Manual'),
                        TextInput::make('name')
                            ->label('Visitor name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                        DateTimePicker::make('scheduled_at')
                            ->label('Date & time')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->unique(
                                modifyRuleUsing: fn (Unique $rule) => $rule
                                    ->where('client_id', auth()->user()?->client_id)
                                    ->where('status', '!=', 'cancelled'),
                                ignoreRecord: true,
                            )
                            ->validationMessages([
                                'unique' => 'That date and time is already booked for someone else.',
                            ]),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason / notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
