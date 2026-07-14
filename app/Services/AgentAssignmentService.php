<?php

namespace App\Services;

use App\Models\User;
use App\Models\WidgetConversation;

class AgentAssignmentService
{
    /**
     * Picks the best-matched agent for a new handoff request: the eligible
     * agent (active, belonging to this client) currently carrying the
     * fewest open conversations, breaking ties by whoever was assigned
     * longest ago (or never) — a least-busy, round-robin-on-ties approach.
     * Returns null if the client has no active agents at all.
     */
    public static function pickAgentFor(int $clientId): ?User
    {
        $agents = User::where('client_id', $clientId)
            ->where('is_agent', true)
            ->whereHas('agent', fn ($query) => $query->where('status', 'active'))
            ->get();

        if ($agents->isEmpty()) {
            return null;
        }

        $openCounts = WidgetConversation::where('client_id', $clientId)
            ->whereIn('status', ['waiting', 'active'])
            ->whereNotNull('agent_id')
            ->selectRaw('agent_id, COUNT(*) as open_count, MAX(created_at) as last_assigned_at')
            ->groupBy('agent_id')
            ->get()
            ->keyBy('agent_id');

        return $agents
            ->map(function (User $agent) use ($openCounts) {
                $stats = $openCounts->get($agent->id);

                return [
                    'agent' => $agent,
                    'open_count' => $stats->open_count ?? 0,
                    'last_assigned_at' => $stats->last_assigned_at ?? '',
                ];
            })
            ->sort(fn ($a, $b) => $a['open_count'] <=> $b['open_count']
                ?: $a['last_assigned_at'] <=> $b['last_assigned_at'])
            ->first()['agent'];
    }
}
