<?php

namespace App\Filament\Resources\KycSubmissions;

use App\Filament\Resources\KycSubmissions\Pages\ListKycSubmissions;
use App\Filament\Resources\KycSubmissions\Pages\ViewKycSubmission;
use App\Filament\Resources\KycSubmissions\Schemas\KycSubmissionForm;
use App\Filament\Resources\KycSubmissions\Tables\KycSubmissionsTable;
use App\Models\KycSubmission;
use App\Models\User;
use App\Notifications\KycApproved;
use App\Notifications\KycRejected;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification;

class KycSubmissionResource extends Resource
{
    protected static ?string $model = KycSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Identification;

    protected static ?string $navigationLabel = 'KYC Submissions';

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()?->is_admin;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return KycSubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KycSubmissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Shared between the table row action and the view page's header action
     * so approve/reject behave identically wherever they're triggered from.
     */
    public static function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Approve')
            ->icon(Heroicon::Check)
            ->color('success')
            ->visible(fn (KycSubmission $record) => $record->isPending())
            ->requiresConfirmation()
            ->action(function (KycSubmission $record) {
                $record->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

                static::notifyClient($record, new KycApproved);

                FilamentNotification::make()->title('Submission approved.')->success()->send();
            });
    }

    public static function rejectAction(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon(Heroicon::XMark)
            ->color('danger')
            ->visible(fn (KycSubmission $record) => $record->isPending())
            ->schema([
                Textarea::make('reason')
                    ->label('Reason for rejection')
                    ->required()
                    ->rows(3),
            ])
            ->action(function (KycSubmission $record, array $data) {
                $record->update([
                    'status' => 'rejected',
                    'rejection_reason' => $data['reason'],
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

                static::notifyClient($record, new KycRejected($record));

                FilamentNotification::make()->title('Submission rejected.')->success()->send();
            });
    }

    private static function notifyClient(KycSubmission $record, $notification): void
    {
        $recipients = User::where('client_id', $record->client_id)
            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, $notification);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKycSubmissions::route('/'),
            'view' => ViewKycSubmission::route('/{record}'),
        ];
    }
}
