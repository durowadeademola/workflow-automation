<?php

namespace App\Filament\Pages;

use App\Models\WidgetConversation;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class LiveChat extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Live Chat';

    protected string $view = 'filament.pages.live-chat';

    public ?int $selectedConversationId = null;

    public string $replyContent = '';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return (bool) $user && $user->is_agent;
    }

    public function getConversations()
    {
        $user = Auth::user();

        return WidgetConversation::query()
            ->where('status', '!=', 'closed')
            ->where('client_id', $user->client_id)
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
        $this->replyContent = '';
    }

    public function getSelectedConversation(): ?WidgetConversation
    {
        if (! $this->selectedConversationId) {
            return null;
        }

        $conversation = WidgetConversation::find($this->selectedConversationId);

        if (! $conversation) {
            return null;
        }

        $user = Auth::user();

        if ($conversation->client_id !== $user->client_id) {
            return null;
        }

        return $conversation;
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyContent' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = $this->getSelectedConversation();

        abort_unless($conversation, 404);

        $user = Auth::user();

        $conversation->messages()->create([
            'sender_type' => 'agent',
            'sender_name' => $user->name,
            'content' => $this->replyContent,
        ]);

        $conversation->update([
            'status' => 'active',
            'agent_id' => $conversation->agent_id ?? $user->id,
            'last_message_at' => now(),
        ]);

        $this->replyContent = '';
    }

    public function closeConversation(): void
    {
        $conversation = $this->getSelectedConversation();

        abort_unless($conversation, 404);

        $conversation->update(['status' => 'closed']);
        $this->selectedConversationId = null;
    }
}
