<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\User;
use App\Notifications\SupportTicketReplied;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected string $view = 'filament.resources.support-tickets.pages.view-support-ticket';

    public string $replyContent = '';

    public function getThread(): Collection
    {
        return $this->record->messages()->with('user')->orderBy('created_at')->get();
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyContent' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        $ticket = $this->record;

        $ticket->messages()->create([
            'user_id' => $user->id,
            'from_admin' => (bool) $user->is_admin,
            'content' => $this->replyContent,
        ]);

        $ticket->update([
            // An admin reply answers it; a client/agent reply on an
            // answered/closed ticket brings it back to needing attention.
            'status' => $user->is_admin ? 'answered' : 'open',
            'closed_at' => null,
            'last_reply_at' => now(),
        ]);

        $recipients = $user->is_admin
            ? User::where('client_id', $ticket->client_id)
                ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                ->get()
            : User::where('is_admin', true)->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new SupportTicketReplied($ticket, $user->name));
        }

        $this->replyContent = '';
    }

    public function closeTicketAction(): Action
    {
        return Action::make('closeTicket')
            ->label('Close ticket')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Close this ticket?')
            ->modalDescription('You can reopen it later if you need to keep the conversation going.')
            ->modalSubmitActionLabel('Close ticket')
            ->action(function () {
                $this->record->update(['status' => 'closed', 'closed_at' => now()]);
            });
    }

    public function reopenTicket(): void
    {
        $this->record->update(['status' => 'open', 'closed_at' => null]);
    }
}
