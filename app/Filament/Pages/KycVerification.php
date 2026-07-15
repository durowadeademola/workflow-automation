<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\KycSubmission;
use App\Models\User;
use App\Notifications\KycSubmitted;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class KycVerification extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Identification;

    protected static ?string $navigationLabel = 'KYC Verification';

    protected static ?int $navigationSort = 25;

    protected string $view = 'filament.pages.kyc-verification';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return (bool) $user && $user->is_client;
    }

    public function getClient(): ?Client
    {
        return Auth::user()->client;
    }

    public function mount(): void
    {
        $this->form->fill([
            'full_name' => $this->getClient()?->name,
        ]);
    }

    public function getLatestSubmission(): ?KycSubmission
    {
        return $this->getClient()?->latestKyc();
    }

    /**
     * A pending or already-approved submission can't be resubmitted over —
     * only "never submitted" or "rejected" leave the form open.
     */
    public function canSubmit(): bool
    {
        $latest = $this->getLatestSubmission();

        return ! $latest || $latest->status === 'rejected';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->label('Full legal name')
                    ->required()
                    ->maxLength(255),
                Select::make('document_type')
                    ->label('Document type')
                    ->options(KycSubmission::DOCUMENT_TYPES)
                    ->required(),
                TextInput::make('document_number')
                    ->label('Document number')
                    ->required()
                    ->maxLength(100),
                FileUpload::make('document_front')
                    ->label('Document (front)')
                    ->disk('local')
                    ->directory('kyc')
                    ->image()
                    ->openable()
                    ->downloadable()
                    ->required(),
                FileUpload::make('document_back')
                    ->label('Document (back)')
                    ->helperText('Only needed for two-sided IDs, e.g. a driver\'s license.')
                    ->disk('local')
                    ->directory('kyc')
                    ->image()
                    ->openable()
                    ->downloadable(),
                FileUpload::make('selfie')
                    ->label('Selfie holding the document')
                    ->helperText('Optional, but speeds up review.')
                    ->disk('local')
                    ->directory('kyc')
                    ->image()
                    ->openable()
                    ->downloadable(),
            ]);
    }

    public function submit(): void
    {
        $client = $this->getClient();

        if (! $client) {
            Notification::make()->title('Your account is not linked to a business.')->danger()->send();

            return;
        }

        abort_unless($this->canSubmit(), 403);

        $data = $this->form->getState();

        $submission = KycSubmission::create([
            ...$data,
            'client_id' => $client->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->form->fill();

        $admins = User::where('is_admin', true)->get();

        if ($admins->isNotEmpty()) {
            NotificationFacade::send($admins, new KycSubmitted($submission));
        }

        Notification::make()
            ->title('Submitted — we\'ll review your documents shortly.')
            ->success()
            ->send();
    }
}
