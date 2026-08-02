import { useEffect, useState } from "react";
import {
    LayoutDashboard,
    Users,
    CalendarCheck,
    UserCircle2,
    MessageSquare,
    Star,
    Headset,
    CreditCard,
    Paintbrush,
    BookOpen,
    ShieldCheck,
    Settings,
    Bell,
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

const NAV_ITEMS = [
    { icon: LayoutDashboard, label: "Dashboard", active: true },
    { icon: Users, label: "Agents" },
    { icon: CalendarCheck, label: "Appointments" },
    { icon: UserCircle2, label: "Customers" },
    { icon: MessageSquare, label: "Messages" },
    { icon: Star, label: "Reviews" },
    { icon: Headset, label: "Support Tickets" },
    { icon: CreditCard, label: "Billing" },
    { icon: Paintbrush, label: "Widget Settings" },
    { icon: BookOpen, label: "Knowledge Base" },
    { icon: ShieldCheck, label: "KYC Verification" },
    { icon: Settings, label: "Settings" },
];

const STATS = [
    { label: "Subscription", value: "Professional" },
    { label: "Appointments Booked", value: 128 },
    { label: "Leads Qualified", value: 42 },
];

const CHART_BARS = [40, 65, 50, 80, 60, 90, 70];

function StatValue({ value, trigger }) {
    const isNumeric = typeof value === "number";
    const count = useCountUp(isNumeric ? value : 0, 1000, 0, trigger);
    return (
        <p className="text-[11px] font-bold text-gray-900 dark:text-white truncate">
            {isNumeric ? count : value}
        </p>
    );
}

/**
 * A stylized mockup of Blueflow's own client dashboard for the homepage
 * hero — built from layered shapes/icons rather than a real screenshot, so
 * it never goes stale as the actual dashboard UI evolves, and never leaks
 * a real client's data. Nav items mirror the actual Filament `/user` panel
 * menu (see app/Providers/Filament/UserPanelProvider.php resources).
 *
 * `trigger` starts the count-up stats and staggered bar-growth animation —
 * pass the same reveal-on-scroll boolean Hero.jsx already uses so everything
 * animates in together instead of the numbers/bars just appearing static.
 */
export default function HeroDashboardPreview({ trigger = true }) {
    return (
        <div className="relative">
            {/* Main dashboard card */}
            <div className="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                {/* Header bar */}
                <div className="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                    <div className="flex items-center gap-2">
                        <div className="w-6 h-6 bg-blue-600 rounded-md flex items-center justify-center text-white text-[10px] font-bold">
                            BA
                        </div>
                        <span className="text-xs font-semibold text-gray-800 dark:text-gray-100">Blueflow</span>
                    </div>
                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <Bell className="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                            <span className="absolute -top-1.5 -right-1.5 w-3 h-3 bg-red-500 rounded-full text-[7px] text-white flex items-center justify-center">3</span>
                        </div>
                        <div className="w-5 h-5 bg-gray-800 dark:bg-gray-200 rounded-full" />
                    </div>
                </div>

                <div className="flex">
                    {/* Sidebar */}
                    <div className="hidden sm:block w-[132px] shrink-0 border-r border-gray-100 dark:border-gray-800 py-3 px-2 space-y-0.5">
                        {NAV_ITEMS.map((item) => (
                            <div
                                key={item.label}
                                className={`flex items-center gap-2 px-2 py-1.5 rounded-lg text-[10px] font-medium ${
                                    item.active
                                        ? "bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400"
                                        : "text-gray-500 dark:text-gray-400"
                                }`}
                            >
                                <item.icon className="w-3 h-3 shrink-0" strokeWidth={2} />
                                <span className="truncate">{item.label}</span>
                            </div>
                        ))}
                    </div>

                    {/* Main content */}
                    <div className="flex-1 min-w-0 p-4">
                        <p className="text-[13px] font-bold text-gray-900 dark:text-white mb-3">Dashboard</p>

                        <div className="grid grid-cols-3 gap-2 mb-4">
                            {STATS.map((s) => (
                                <div key={s.label} className="bg-gray-50 dark:bg-gray-800 rounded-lg p-2 border border-gray-100 dark:border-gray-700">
                                    <p className="text-[8px] text-gray-400 dark:text-gray-500 mb-1 truncate">{s.label}</p>
                                    <StatValue value={s.value} trigger={trigger} />
                                </div>
                            ))}
                        </div>

                        <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700">
                            <p className="text-[8px] text-gray-400 dark:text-gray-500 mb-2">Appointments Booked Trend</p>
                            <div className="flex items-end gap-1.5 h-14">
                                {CHART_BARS.map((h, i) => (
                                    <div
                                        key={i}
                                        className="flex-1 bg-blue-500 dark:bg-blue-500 rounded-sm opacity-80"
                                        style={{
                                            height: trigger ? `${h}%` : "0%",
                                            transition: `height 0.7s ease ${i * 90}ms`,
                                        }}
                                    />
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
