<?php

namespace App\Filament\Pages;

use App\Http\Controllers\API\WidgetConversationController;
use App\Models\WidgetConversation;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class LiveChat extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Live Chat';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.live-chat';

    public ?int $selectedConversationId = null;

    public string $replyContent = '';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return (bool) $user && $user->is_agent && $user->client?->hasFeature('chat-widget');
    }

    public function getConversations()
    {
        $user = Auth::user();

        return WidgetConversation::query()
            ->with('agent')
            ->whereNotIn('status', ['closed', 'returned_to_ai'])
            ->where('client_id', $user->client_id)
            ->orderByRaw('agent_id = ? desc', [$user->id])
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
        abort_if(in_array($conversation->status, ['closed', 'returned_to_ai']), 409);

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

        WidgetConversationController::mirrorToMessages($conversation, $this->replyContent, fromCustomer: false);

        $this->replyContent = '';
    }

    public function closeConversation(): void
    {
        $conversation = $this->getSelectedConversation();

        abort_unless($conversation, 404);

        $conversation->update(['status' => 'closed']);
        $this->selectedConversationId = null;
    }

    /**
     * Ends the human session and signals the widget to resume talking to
     * the AI directly — for when the visitor's question is answered but
     * they might want to keep chatting (unlike closeConversation, which
     * frames things as "this is over").
     */
    public function returnToAI(): void
    {
        $conversation = $this->getSelectedConversation();

        abort_unless($conversation, 404);

        $handoffMessage = "You're now back with our AI assistant — feel free to keep chatting!";

        $conversation->messages()->create([
            'sender_type' => 'agent',
            'sender_name' => Auth::user()->name,
            'content' => $handoffMessage,
        ]);

        $conversation->update([
            'status' => 'returned_to_ai',
            'last_message_at' => now(),
        ]);

        WidgetConversationController::mirrorToMessages($conversation, $handoffMessage, fromCustomer: false);

        $this->selectedConversationId = null;
    }
}
