<?php

namespace App\Workflow\Steps;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * Ports n8n's "Build RAG Prompt" code node verbatim — same handoff/
 * appointment/lead/registration instructions, same system-prompt structure
 * for the with-context and no-context cases, same trailing SOURCE: marker
 * instruction. Deliberately not templated/config-driven: this IS the
 * business logic, same as the JS node it replaces.
 */
class ChatPromptBuilderStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $results = $context->get('steps.search.results', []);
        $hasContext = ! empty($results);

        $sourceUrl = '';
        $ragContext = '';

        if ($hasContext) {
            $ragContext = collect($results)
                ->map(fn ($chunk, $i) => '[Source ' . ($i + 1) . " - {$chunk['url']}]\n{$chunk['content']}")
                ->implode("\n\n");

            $sourceUrl = $results[0]['url'] ?? '';
        }

        $history = $context->get('trigger.history', []);
        $historyText = collect($history)
            ->slice(-6)
            ->map(fn ($m) => ($m['role'] === 'assistant' ? 'Assistant' : 'User') . ": {$m['content']}")
            ->implode("\n");

        $baseKnowledge = $context->get('trigger.systemPrompt') ?? '';
        $businessName = $context->get('trigger.businessName') ?? 'AI Assistant';
        $message = $context->get('trigger.message', '');

        $kbEntries = $context->get('trigger.knowledgeBase', []);
        $knowledgeBaseText = collect($kbEntries)
            ->map(fn ($e) => ($e['type'] ?? '') === 'faq' ? "Q: {$e['title']}\nA: {$e['content']}" : "{$e['title']}\n{$e['content']}")
            ->implode("\n\n");
        $knowledgeBaseBlock = $knowledgeBaseText !== ''
            ? "--- BUSINESS KNOWLEDGE BASE (provided directly by the business - always trust this over the website content below if they ever conflict) ---\n{$knowledgeBaseText}\n--- END KNOWLEDGE BASE ---\n"
            : '';

        $handoffInstruction = "- Hand the visitor off to a real team member if: they explicitly ask to speak with a human, a real person, an agent, or support staff; OR they seem frustrated and unable to get what they need from you; OR - most importantly - you don't actually know the answer to their question even after checking the website knowledge, the business knowledge base, and your own general knowledge. Never guess or invent an answer you're not genuinely confident in just to avoid saying you don't know - handing off is always better than a fabricated answer. When any of this applies, respond warmly letting them know you're connecting them with the team right now, then on its own new line add exactly: HANDOFF:REQUESTED\n"
            . "- IMPORTANT exception: if your own previous message already told the visitor the team is currently offline and asked them for their name and phone number, do NOT emit HANDOFF:REQUESTED again just because they asked for a human earlier in the conversation. Once they reply with a name and phone number, this becomes a registration, not a handoff - you MUST follow the registration instruction below exactly, including actually emitting the REGISTER:DETAILS marker with their real values on its own line. Thanking them in plain language without also emitting that marker accomplishes nothing - without the marker, their details are never actually saved anywhere and no one will know to follow up with them.";

        $today = now()->format('Y-m-d');
        $appointmentInstruction = "- If the visitor wants to book an appointment, meeting, or consultation, collect their full name and an exact date and time through natural conversation, asking for anything missing one thing at a time. Today's date is {$today} - use it to resolve relative dates like \"tomorrow\" or \"next Monday\" yourself; never ask the visitor to do that math. Never invent a date/time they didn't actually give you. Once you have BOTH their name AND a specific date and time, confirm the details back to them in your reply, then on its own new line add exactly (with real values, valid JSON, 24-hour time): APPOINTMENT:BOOK {\"name\":\"<full name>\",\"scheduled_at\":\"<YYYY-MM-DD HH:MM:SS>\",\"reason\":\"<brief reason if they gave one, else empty string>\"}";

        $leadInstruction = "- While chatting, naturally notice what the visitor seems interested in (their intent), and if they mention it, their budget or timeline. Don't interrogate them with a checklist - just note what comes up naturally. Once you have a reasonably clear sense of their intent, on its own new line add exactly once (valid JSON): LEAD:QUALIFIED {\"intent\":\"<what they're interested in, brief>\",\"budget\":\"<their budget if mentioned, else empty string>\",\"timeline\":\"<their timeline if mentioned, else empty string>\"}";

        $registerInstruction = "- If the visitor wants to register, sign up, or leave their details (including if they click a \"Register with us\" quick reply), collect their full name and telephone number through natural conversation, asking for anything missing one thing at a time. Email is optional - ask for it too, but don't hold up completing registration if they only give a phone number. You may also ask what they're interested in, but don't insist on it. Once you have their full name AND a phone number, confirm the details back to them in your reply, then on its own new line add exactly (with real values, valid JSON): REGISTER:DETAILS {\"name\":\"<full name>\",\"phone\":\"<phone number>\",\"email\":\"<email if given, else empty string>\",\"interest\":\"<brief interest if mentioned, else empty string>\"}";

        if ($hasContext) {
            $systemPrompt = "You are a helpful AI assistant for {$businessName}.\n{$baseKnowledge}\n{$knowledgeBaseBlock}Use the following information from the business website to answer the visitor's question as accurately as possible:\n--- WEBSITE KNOWLEDGE ---\n{$ragContext}\n--- END KNOWLEDGE ---\n"
                . "Guidelines:\n- Answer naturally and conversationally\n- If the context partially answers the question, use what you have and be helpful\n- Never guess or make up an answer you're not genuinely confident in - if the website knowledge, business knowledge base, and your own general knowledge don't actually answer the question, say so honestly and hand off to the team instead of guessing (see below)\n- Keep replies concise - 2-3 sentences max unless detail is needed\n- Always end with an offer to help further\n"
                . "{$handoffInstruction}\n{$appointmentInstruction}\n{$leadInstruction}\n{$registerInstruction}\n"
                . "- At the end of your response add exactly this on a new line: SOURCE:{$sourceUrl}";
        } else {
            $systemPrompt = "You are a helpful AI assistant for {$businessName}.\n{$baseKnowledge}\n{$knowledgeBaseBlock}You don't have specific website content for this question, but use your general knowledge about this type of business to give a helpful response.\n"
                . "Guidelines:\n- Be genuinely helpful even without specific context\n- Answer general questions about the business type naturally\n- Never guess or make up specific details you don't actually know (exact prices, availability, policies, etc.) - if you don't genuinely know the answer, say so honestly and hand off to the team instead of guessing (see below)\n- Keep replies concise and friendly\n"
                . "{$handoffInstruction}\n{$appointmentInstruction}\n{$leadInstruction}\n{$registerInstruction}";
        }

        $userMessage = $historyText !== '' ? "{$historyText}\nUser: {$message}" : $message;

        return [
            'systemPrompt' => $systemPrompt,
            'userMessage' => $userMessage,
            'chunksFound' => $hasContext ? count($results) : 0,
            'sourceUrl' => $sourceUrl,
        ];
    }
}
