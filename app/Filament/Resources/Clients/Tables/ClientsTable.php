<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Exports\ClientExporter;
use App\Models\User;
use App\Notifications\ClientRejected;
use App\Notifications\WidgetReady;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(ClientExporter::class),
            ])
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('telephone'),
                BadgeColumn::make('type')
                    ->colors(['danger']),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => ['inactive', 'rejected'],
                    ]),
                IconColumn::make('widget_ready')
                    ->label('Widget live?')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending approval',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'rejected' => 'Rejected',
                    ]),
                TernaryFilter::make('widget_ready')
                    ->label('Widget ready?'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalDescription('This will let the client and any of their agents log in.')
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);

                        Notification::make()
                            ->title('Client approved')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('reason')
                            ->label('Reason for rejection')
                            ->helperText('Included in the email sent to the client.')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['reason'],
                        ]);

                        $recipients = User::where('client_id', $record->id)
                            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                            ->get();

                        if ($recipients->isNotEmpty()) {
                            NotificationFacade::send($recipients, new ClientRejected($record));
                        }

                        Notification::make()
                            ->title('Client rejected')
                            ->success()
                            ->send();
                    }),
                Action::make('markWidgetReady')
                    ->label('Mark Widget Ready')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'active'
                        && $record->hasFeature('chat-widget')
                        && ! $record->widget_ready)
                    ->requiresConfirmation()
                    ->modalDescription('This tells the client their widget is ready to embed and go live — make sure the n8n workflow and webhook URL are actually wired up first.')
                    ->action(function ($record) {
                        $record->update([
                            'widget_ready' => true,
                            'widget_ready_at' => now(),
                        ]);

                        $recipients = User::where('client_id', $record->id)
                            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                            ->get();

                        if ($recipients->isNotEmpty()) {
                            NotificationFacade::send($recipients, new WidgetReady());
                        }

                        Notification::make()
                            ->title('Client notified — widget marked ready')
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
}
