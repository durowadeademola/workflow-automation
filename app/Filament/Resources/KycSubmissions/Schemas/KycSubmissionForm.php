<?php

namespace App\Filament\Resources\KycSubmissions\Schemas;

use App\Models\KycSubmission;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KycSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Submission Details')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->disabled(),
                        TextInput::make('full_name')
                            ->label('Full legal name')
                            ->disabled(),
                        Select::make('document_type')
                            ->options(KycSubmission::DOCUMENT_TYPES)
                            ->disabled(),
                        TextInput::make('document_number')
                            ->disabled(),
                        DateTimePicker::make('submitted_at')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Documents')
                    ->schema([
                        FileUpload::make('document_front')
                            ->label('Document (front)')
                            ->disk('local')
                            ->directory('kyc')
                            ->image()
                            ->openable()
                            ->downloadable()
                            ->disabled(),
                        FileUpload::make('document_back')
                            ->label('Document (back)')
                            ->disk('local')
                            ->directory('kyc')
                            ->image()
                            ->openable()
                            ->downloadable()
                            ->disabled(),
                        FileUpload::make('selfie')
                            ->disk('local')
                            ->directory('kyc')
                            ->image()
                            ->openable()
                            ->downloadable()
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Review')
                    ->schema([
                        TextInput::make('status')
                            ->disabled(),
                        TextInput::make('reviewer.name')
                            ->label('Reviewed by')
                            ->disabled(),
                        DateTimePicker::make('reviewed_at')
                            ->disabled(),
                        Textarea::make('rejection_reason')
                            ->label('Rejection reason')
                            ->visible(fn (?KycSubmission $record) => $record?->status === 'rejected')
                            ->columnSpanFull()
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
