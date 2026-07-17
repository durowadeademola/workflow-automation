<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\NewSupportTicket;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;

    /**
     * "message" isn't a column on SupportTicket itself — it's the ticket's
     * first SupportTicketMessage, created together with the ticket so a
     * ticket is never left with an empty thread.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        $ticket = SupportTicket::create([
            'client_id' => $user->client_id,
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $ticket->messages()->create([
            'user_id' => $user->id,
            'from_admin' => (bool) $user->is_admin,
            'content' => $data['message'],
        ]);

        $admins = User::where('is_admin', true)->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewSupportTicket($ticket));
        }

        return $ticket;
    }

    protected function getRedirectUrl(): string
    {
        $panel = auth()->user()?->is_admin ? 'admin' : 'user';

        return "/{$panel}/support-tickets/{$this->getRecord()->id}";
    }
}
