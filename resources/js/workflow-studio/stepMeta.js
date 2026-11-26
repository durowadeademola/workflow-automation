import {
    BookOpen,
    Bot,
    CalendarCheck,
    Database,
    FileSearch,
    Globe,
    Headset,
    Mail,
    MessageCircle,
    MessageSquare,
    Scissors,
    Sparkles,
    Trash2,
    UserCheck,
    UserPlus,
    Workflow,
    Zap,
    Clock,
    Webhook,
    Hand,
} from 'lucide-react';

/**
 * How each step type looks on the canvas: an icon, a tint for its icon box
 * (see .ws-tint-* in style.css) and the small uppercase category line above
 * the step's name. Unknown types (a step registered in config('workflow.steps')
 * after this map was written) fall back to a neutral "Step".
 */
const STEP_META = {
    rag_search: { icon: FileSearch, tint: 'indigo', kind: 'Knowledge' },
    chat_prompt_builder: { icon: MessageSquare, tint: 'navy', kind: 'AI' },
    llm_call: { icon: Sparkles, tint: 'navy', kind: 'AI agent' },
    extract_markers: { icon: Bot, tint: 'navy', kind: 'AI' },
    chat_response_builder: { icon: MessageSquare, tint: 'navy', kind: 'AI' },
    dispatch_handoff: { icon: Headset, tint: 'rose', kind: 'Action' },
    dispatch_appointment: { icon: CalendarCheck, tint: 'sky', kind: 'Action' },
    dispatch_lead: { icon: UserCheck, tint: 'amber', kind: 'Action' },
    dispatch_registration: { icon: UserPlus, tint: 'amber', kind: 'Action' },
    delete_old_chunks: { icon: Trash2, tint: 'slate', kind: 'Knowledge' },
    fetch_pages: { icon: Globe, tint: 'indigo', kind: 'Knowledge' },
    extract_and_chunk: { icon: Scissors, tint: 'indigo', kind: 'Knowledge' },
    generate_embeddings: { icon: Sparkles, tint: 'indigo', kind: 'Knowledge' },
    store_chunks: { icon: Database, tint: 'indigo', kind: 'Knowledge' },
    crawl_summary: { icon: BookOpen, tint: 'indigo', kind: 'Knowledge' },
    send_email: { icon: Mail, tint: 'violet', kind: 'Send' },
    send_whatsapp: { icon: MessageCircle, tint: 'green', kind: 'Send' },
    send_sms: { icon: MessageSquare, tint: 'green', kind: 'Send' },
    send_telegram: { icon: MessageCircle, tint: 'sky', kind: 'Send' },
};

const FALLBACK = { icon: Workflow, tint: 'slate', kind: 'Step' };

export function stepMeta(type) {
    return STEP_META[type] ?? FALLBACK;
}

export const TRIGGERS = {
    manual: { icon: Hand, label: 'Manual' },
    webhook: { icon: Webhook, label: 'Webhook' },
    scheduled: { icon: Clock, label: 'Scheduled' },
    model_event: { icon: Zap, label: 'Model event' },
};

export function triggerMeta(type) {
    return TRIGGERS[type] ?? { icon: Zap, label: type };
}

/** "extract_markers_2" → "Extract markers 2". */
export function humanize(key) {
    const text = String(key ?? '').replace(/[_-]+/g, ' ').trim();
    return text.charAt(0).toUpperCase() + text.slice(1);
}

/** "Llm Call Step" (the API's class-name label) → "LLM call". */
export function typeLabel(stepTypes, type) {
    const raw = stepTypes.find((t) => t.type === type)?.label ?? humanize(type);
    const words = raw.replace(/\s*Step$/, '').split(' ');
    return words
        .map((w, i) => (w.toLowerCase() === 'llm' || w.toLowerCase() === 'rag' ? w.toUpperCase() : i === 0 ? w : w.toLowerCase()))
        .join(' ');
}
