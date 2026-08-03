<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Filament\Exports\CustomerExporter;
use App\Filament\Imports\CustomerImporter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(CustomerImporter::class)
                    ->options(fn () => ['client_id' => auth()->user()?->client_id]),
                ExportAction::make()
                    ->exporter(CustomerExporter::class),
            ])
            ->columns([
                TextColumn::make('display_name')
                    ->label('Name / ID')
                    ->formatStateUsing(fn (string $state, $record): string => "{$state} (#{$record->id})")
                    ->searchable(query: fn ($query, $search) => $query->orWhere('name', 'like', "%{$search}%")),
                TextColumn::make('email')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telephone')
                    ->placeholder('—')
                    ->searchable(),
                IconColumn::make('is_qualified_lead')
                    ->label('Qualified lead')
                    ->boolean(),
                TextColumn::make('lead_intent')
                    ->label('Interested in')
                    ->limit(40)
                    ->placeholder('—')
                    ->searchable()
                    ->tooltip(fn ($record) => $record->lead_intent),
                TextColumn::make('platform')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Telegram' => 'primary',
                        'WhatsApp' => 'success',
                        'Website' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('subscribed_to_marketing')
                    ->label('Subscribed')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('platform')
                    ->options([
                        'Telegram' => 'Telegram',
                        'WhatsApp' => 'WhatsApp',
                        'Website' => 'Website',
                    ]),
                TernaryFilter::make('is_qualified_lead')
                    ->label('Qualified lead?'),
                TernaryFilter::make('subscribed_to_marketing')
                    ->label('Subscribed to marketing?')
                    ->trueLabel('Subscribed')
                    ->falseLabel('Unsubscribed')
                    ->placeholder('All'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('quickStatus')
                    ->label('Quick Edit')
                    ->icon('heroicon-o-bolt')
                    ->modalHeading('Edit customer')
                    ->modalWidth('lg') // Keeps the modal small and clean
                    ->fillForm(fn ($record) => [
                        'status' => $record->status,
                        'platform' => $record->platform,
                    ])
                    ->schema([
                        Select::make('status')
                            ->options([
                                'OPEN' => 'OPEN',
                                'ASSIGNED' => 'ASSIGNED',
                                'CLOSED' => 'CLOSED',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('platform')->options([
                            'Telegram' => 'Telegram',
                            'WhatsApp' => 'WhatsApp',
                            'Website' => 'Website',
                        ]),
                    ])
                    ->action(fn ($record, array $data) => $record->update($data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
