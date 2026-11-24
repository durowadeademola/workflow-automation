import { useEffect, useState } from "react";
import {
    Workflow,
    LayoutDashboard,
    MessageSquare,
    CalendarCheck,
    Users,
    Settings,
    CheckCircle2,
    Sparkles,
} from "lucide-react";
import { useCountUp } from "@/hooks/useScrollAnimation";

const USER_MESSAGE = "Are you open on Saturdays?";
const AI_MESSAGE = "Yes! We're open Mon–Sat, 8am–6pm.";

/** Reveals `text` one character at a time, starting `delay`ms after `trigger`. */
function useTypewriter(text, trigger, speed = 35, delay = 0) {
    const [output, setOutput] = useState("");

    useEffect(() => {
        if (!trigger) return;
        let i = 0;
        let intervalId;
        const timeoutId = setTimeout(() => {
            intervalId = setInterval(() => {
                i += 1;
                setOutput(text.slice(0, i));
                if (i >= text.length) clearInterval(intervalId);
            }, speed);
        }, delay);
        return () => {
            clearTimeout(timeoutId);
            clearInterval(intervalId);
        };
    }, [trigger, text, speed, delay]);

    return output;
}

function TypingDots() {
    return (
        <div className="bg-gray-100 dark:bg-gray-700 rounded-lg rounded-tl-sm px-2.5 py-2 w-fit flex items-center gap-1">
            {[0, 150, 300].map((delay) => (
                <span
                    key={delay}
                    className="w-1 h-1 bg-gray-400 dark:bg-gray-400 rounded-full animate-bounce"
                    style={{ animationDelay: `${delay}ms` }}
                />
            ))}
        </div>
    );
}

/**
 * Sequences the floating chat preview like a real conversation: the visitor
 * message types out first, then an AI typing indicator, then the AI reply
 * types out — rather than both bubbles just appearing fully-formed at once.
 */
function ChatPreviewMessages({ trigger }) {
    const userTyped = useTypewriter(USER_MESSAGE, trigger, 35, 400);
    const [aiPhase, setAiPhase] = useState("waiting");

    useEffect(() => {
        if (!trigger) return;
        const userDoneAt = 400 + USER_MESSAGE.length * 35 + 300;
        const indicatorTimer = setTimeout(() => setAiPhase("indicator"), userDoneAt);
        const textTimer = setTimeout(() => setAiPhase("text"), userDoneAt + 1000);
        return () => {
            clearTimeout(indicatorTimer);
            clearTimeout(textTimer);
        };
    }, [trigger]);

    const aiTyped = useTypewriter(AI_MESSAGE, aiPhase === "text", 30, 0);

    return (
        <>
            {userTyped && (
                <div className="bg-blue-600 text-white text-[9px] rounded-lg rounded-tr-sm px-2.5 py-1.5 mb-1.5 ml-auto w-fit max-w-[85%]">
                    {userTyped}
                </div>
            )}
            {aiPhase === "indicator" && <TypingDots />}
            {aiPhase === "text" && (
                <div className="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-[9px] rounded-lg rounded-tl-sm px-2.5 py-1.5 w-fit max-w-[90%] leading-snug">
                    {aiTyped}
                </div>
            )}
        </>
    );
}

const RAIL_ICONS = [Workflow, LayoutDashboard, MessageSquare, CalendarCheck, Users, Settings];

/**
 * The flow drawn on the canvas, in the canvas SVG's own 0–520 × 0–316
 * coordinates — kept low enough that the "Book appointment" branch clears the
 * floating chat card, which overlaps the canvas's top-right corner. `icon` names one of the glyphs in FlowIcon; `delay` staggers
 * each node's fade-in so the flow reads left to right.
 */
const FLOW_NODES = [
    { x: 14, y: 143, kind: "Trigger", title: "New chat message", icon: "chat", tint: "indigo", delay: 0 },
    { x: 186, y: 143, kind: "AI agent", title: "Understand intent", icon: "spark", tint: "navy", delay: 150 },
    { x: 358, y: 115, kind: "Action", title: "Book appointment", icon: "calendar", tint: "sky", delay: 300 },
    { x: 358, y: 205, kind: "Action", title: "Qualify lead", icon: "person", tint: "amber", delay: 400 },
];

const FLOW_EDGES = [
    "M164 169 C 175 169, 175 169, 184 169",
    "M336 169 C 347 169, 345 141, 356 141",
    "M336 169 C 347 169, 345 231, 356 231",
];

const TINTS = {
    indigo: { box: "fill-indigo-50 dark:fill-indigo-500/15", glyph: "stroke-indigo-700 dark:stroke-indigo-300" },
    navy: { box: "fill-slate-800 dark:fill-slate-100", glyph: "stroke-white fill-white dark:stroke-slate-900 dark:fill-slate-900" },
    sky: { box: "fill-sky-50 dark:fill-sky-500/15", glyph: "stroke-sky-700 dark:stroke-sky-300" },
    amber: { box: "fill-amber-50 dark:fill-amber-500/15", glyph: "stroke-amber-700 dark:stroke-amber-300" },
};

/** Small line icons drawn relative to the node's 28×28 icon box at (bx, by). */
function FlowIcon({ name, bx, by, className }) {
    const c = { cx: bx + 14, cy: by + 14 };
    switch (name) {
        case "chat":
            return (
                <path
                    className={className}
                    fill="none"
                    strokeWidth="1.6"
                    strokeLinejoin="round"
                    d={`M${bx + 7} ${by + 8} h14 a2.5 2.5 0 0 1 2.5 2.5 v7 a2.5 2.5 0 0 1 -2.5 2.5 h-8 l-4 3.5 v-3.5 h-2 a2.5 2.5 0 0 1 -2.5 -2.5 v-7 a2.5 2.5 0 0 1 2.5 -2.5 z`}
                />
            );
        case "spark":
            return (
                <path
                    className={className}
                    strokeWidth="0"
                    d={`M${c.cx} ${c.cy - 8} l2.3 5.7 l5.7 2.3 l-5.7 2.3 l-2.3 5.7 l-2.3 -5.7 l-5.7 -2.3 l5.7 -2.3 z`}
                />
            );
        case "calendar":
            return (
                <g className={className} fill="none" strokeWidth="1.6" strokeLinecap="round">
                    <rect x={bx + 7} y={by + 8} width="14" height="13" rx="2.5" />
                    <path d={`M${bx + 7} ${by + 12.5} h14 M${bx + 11} ${by + 6} v4 M${bx + 17} ${by + 6} v4`} />
                </g>
            );
        case "person":
            return (
                <g className={className} fill="none" strokeWidth="1.6" strokeLinecap="round">
                    <circle cx={c.cx} cy={by + 11} r="3.6" />
                    <path d={`M${bx + 7} ${by + 22} a7 5.5 0 0 1 14 0`} />
                </g>
            );
        default:
            return null;
    }
}

const RUN_STATS = [
    { label: "Runs today", value: 1284 },
    { label: "Appointments booked", value: 96 },
    { label: "Hours saved", value: 42 },
];

function RunStat({ label, value, trigger }) {
    const count = useCountUp(value, 1000, 0, trigger);
    return (
        <div className="px-3 py-2 first:pl-0 border-l first:border-l-0 border-gray-100 dark:border-gray-800 min-w-0">
            <p className="text-[8px] text-gray-500 dark:text-gray-400 truncate">{label}</p>
            <p className="text-[15px] font-bold text-gray-900 dark:text-white tabular-nums">{count.toLocaleString()}</p>
        </div>
    );
}

/**
 * A stylized mockup of a Blueflow automation workflow for the homepage hero
 * — a workflow-builder canvas (trigger → AI agent → booking / lead branches)
 * and today's run stats, with the floating chat and activity cards standing
 * in for live results. Built from layered shapes/icons rather than a real
 * screenshot, so it never goes stale as the actual UI evolves, and never
 * leaks a real client's data. Kept short on purpose: the activity cards hang
 * off the card's bottom edge and must stay inside the hero's clipped area.
 *
 * `trigger` starts the count-up stats and the left-to-right fade-in of the
 * flow — pass the same reveal-on-scroll boolean Hero.jsx already uses so
 * everything animates in together.
 */
export default function HeroDashboardPreview({ trigger = true }) {
    return (
        <div className="relative">
            {/* Main workflow card */}
            <div className="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                {/* Header bar */}
                <div className="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                    <div className="flex items-center gap-2">
                        <div className="w-6 h-6 bg-blue-600 rounded-md flex items-center justify-center text-white text-[10px] font-bold">
                            BA
                        </div>
                        <span className="font-semibold text-gray-800 dark:text-gray-100">Blueflow</span>
                        <span className="text-gray-300 dark:text-gray-600">/</span>
                        <span className="text-gray-500 dark:text-gray-400">Workflows</span>
                        <span className="text-gray-300 dark:text-gray-600">/</span>
                        <span className="font-semibold text-gray-800 dark:text-gray-100 truncate">New lead follow-up</span>
                    </div>
                    <span className="flex items-center gap-1 shrink-0 rounded-full bg-emerald-50 dark:bg-emerald-500/15 px-2 py-0.5 text-[9px] font-semibold text-emerald-700 dark:text-emerald-400">
                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                        Live
                    </span>
                </div>

                <div className="flex">
                    {/* Icon rail */}
                    <div className="hidden sm:flex w-11 shrink-0 flex-col items-center gap-2 border-r border-gray-100 dark:border-gray-800 py-3">
                        {RAIL_ICONS.map((Icon, i) => (
                            <div
                                key={i}
                                className={`w-7 h-7 rounded-lg flex items-center justify-center ${
                                    i === 0
                                        ? "bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400"
                                        : "text-gray-400 dark:text-gray-500"
                                }`}
                            >
                                <Icon className="w-3.5 h-3.5" strokeWidth={2} />
                            </div>
                        ))}
                    </div>

                    <div className="flex-1 min-w-0">
                        {/* Flow canvas */}
                        <div
                            className="border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-950/40"
                            style={{
                                backgroundImage: "radial-gradient(circle, rgb(148 163 184 / 0.35) 1px, transparent 1px)",
                                backgroundSize: "14px 14px",
                            }}
                        >
                            <svg viewBox="0 0 520 316" className="block w-full h-auto" role="img" aria-label="Workflow: new chat message, AI agent understands intent, then books an appointment or qualifies the lead">
                                <defs>
                                    <marker id="hero-flow-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto">
                                        <path d="M0 0 L10 5 L0 10 z" className="fill-slate-400 dark:fill-slate-500" />
                                    </marker>
                                </defs>

                                <g
                                    fill="none"
                                    strokeWidth="1.6"
                                    className="stroke-slate-400 dark:stroke-slate-500"
                                    style={{ opacity: trigger ? 1 : 0, transition: "opacity 0.6s ease 500ms" }}
                                >
                                    {FLOW_EDGES.map((d) => (
                                        <path key={d} d={d} markerEnd="url(#hero-flow-arrow)" />
                                    ))}
                                </g>

                                {FLOW_NODES.map((node) => {
                                    const tint = TINTS[node.tint];
                                    const bx = node.x + 10;
                                    const by = node.y + 12;
                                    return (
                                        <g
                                            key={node.title}
                                            style={{
                                                opacity: trigger ? 1 : 0,
                                                transform: trigger ? "translateY(0)" : "translateY(6px)",
                                                transition: `opacity 0.5s ease ${node.delay}ms, transform 0.5s ease ${node.delay}ms`,
                                            }}
                                        >
                                            <rect
                                                x={node.x}
                                                y={node.y}
                                                width="150"
                                                height="52"
                                                rx="9"
                                                className="fill-white dark:fill-gray-800 stroke-gray-200 dark:stroke-gray-700"
                                                strokeWidth="1"
                                            />
                                            <rect x={bx} y={by} width="28" height="28" rx="7" className={tint.box} />
                                            <FlowIcon name={node.icon} bx={bx} by={by} className={tint.glyph} />
                                            <text x={node.x + 46} y={node.y + 22} fontSize="7.5" fontWeight="600" letterSpacing="0.4" className="fill-gray-500 dark:fill-gray-400">
                                                {node.kind.toUpperCase()}
                                            </text>
                                            <text x={node.x + 46} y={node.y + 37} fontSize="10.5" fontWeight="600" className="fill-gray-900 dark:fill-white">
                                                {node.title}
                                            </text>
                                        </g>
                                    );
                                })}
                            </svg>
                        </div>

                        <div className="px-4 py-3">
                            {/* Run stats */}
                            <div className="grid grid-cols-3">
                                {RUN_STATS.map((s) => (
                                    <RunStat key={s.label} {...s} trigger={trigger} />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Floating chat preview card */}
            <div className="hidden sm:block absolute -top-3 -right-2 sm:-right-4 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 p-3 rotate-[3deg]">
                <div className="flex items-center gap-1.5 mb-2">
                    <div className="w-4 h-4 bg-blue-600 rounded-full flex items-center justify-center text-white text-[7px] font-bold">A</div>
                    <span className="text-[9px] font-semibold text-gray-700 dark:text-gray-200">Dolores &middot; AI Assistant</span>
                </div>
                <ChatPreviewMessages trigger={trigger} />
            </div>

            {/* Floating activity notifications */}
            <div className="hidden sm:block absolute -bottom-8 -right-2 sm:-right-5 w-56 space-y-2 rotate-[-2deg]">
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 px-3 py-2 flex items-center gap-2.5">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500 shrink-0" />
                    <div className="min-w-0">
                        <p className="text-[9px] font-semibold text-gray-800 dark:text-gray-100">Appointment booked</p>
                        <p className="text-[8px] text-gray-400 dark:text-gray-500 truncate">General checkup &middot; just now</p>
                    </div>
                </div>
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 px-3 py-2 flex items-center gap-2.5">
                    <Sparkles className="w-4 h-4 text-blue-500 shrink-0" />
                    <div className="min-w-0">
                        <p className="text-[9px] font-semibold text-gray-800 dark:text-gray-100">Lead captured</p>
                        <p className="text-[8px] text-gray-400 dark:text-gray-500 truncate">New patient inquiry &middot; 2m ago</p>
                    </div>
                </div>
            </div>
        </div>
    );
}
