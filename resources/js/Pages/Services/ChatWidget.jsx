import { Head, Link } from "@inertiajs/react";
import { Bot, Palette, Users, Code2, BarChart3, MessagesSquare, MessageSquareText, CalendarCheck, Target, IdCard } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Answers visitors instantly using your own website content — no manual training required",
    "Hands off to a real member of your team the moment a visitor asks for one",
    "Books appointments right in the chat, with automatic conflict checking",
    "Picks up on what visitors are interested in and flags qualified leads for follow-up",
    "Fully branded to match your business — colors, greeting, avatar name, and more",
    "Two-minute setup: copy one snippet onto your site and you're live",
];

const features = [
    { icon: Bot, title: "AI That Knows Your Business", description: "Trained on your website's own content, so it answers real questions accurately instead of generic chatbot replies." },
    { icon: Users, title: "Human Handoff", description: "The moment a visitor asks for a person, it's automatically routed to whichever of your agents is least busy — no lost requests." },
    { icon: MessagesSquare, title: "Live Agent Inbox", description: "Your team replies to handed-off conversations from a dedicated inbox, and can hand a conversation back to the AI anytime." },
    { icon: CalendarCheck, title: "Appointment Booking", description: "Visitors can book an appointment right in the chat — the AI collects the details and checks for conflicts, so no two bookings ever land on the same slot." },
    { icon: Target, title: "Lead Qualification", description: "The AI naturally picks up on what a visitor's interested in — and their budget or timeline if mentioned — then flags them in your dashboard so you know who's worth following up with." },
    { icon: Palette, title: "Full Branding Control", description: "Set your assistant's name, brand color, greeting message, quick-reply buttons, and widget position — all from your dashboard." },
    { icon: Code2, title: "One-Snippet Install", description: "No developer needed. Copy your personalized embed code from Widget Settings and paste it before your site's closing </body> tag." },
    { icon: BarChart3, title: "Unified Conversation History", description: "Every visitor becomes a real customer record, with their full message history — same as WhatsApp and Telegram." },
    { icon: IdCard, title: "Every Visitor Tracked", description: "Each visitor gets their own customer record from their very first message — even before they share their name — so no conversation or follow-up opportunity slips through the cracks." },
];

const steps = [
    { title: "Register and choose Chat Widget", description: "Sign up and select Chat Widget as one of your services — takes about two minutes." },
    { title: "Customize your assistant", description: "From Widget Settings, set your assistant's name, brand color, greeting, and quick replies to match your business." },
    { title: "We integrate it on our end", description: "Our team wires your customized setup into place behind the scenes, then lets you know the moment it's ready to go live." },
    { title: "Copy your embed code", description: "Once you get the go-live notification, paste your personalized snippet onto your website — that's the entire technical setup." },
    { title: "Go live and start chatting", description: "Your AI assistant starts answering visitors immediately, and hands off to your team whenever one asks for a human." },
];

const metrics = [
    { value: "24/7", label: "Always Answering" },
    { value: "< 2 min", label: "Setup Time" },
    { value: "0", label: "Code Required" },
    { value: "100%", label: "Conversations Logged" },
];

export default function ChatWidget() {
    return (
        <>
            <Head title="Chat Widget — Blueflow Automation">
                <meta
                    name="description"
                    content="An AI-powered chat widget for your website that answers visitors instantly and hands off to a real agent whenever asked."
                />
            </Head>
            <div className="min-h-screen bg-white">
                <Navbar />
                <main>
                    {/* Overview */}
                    <section className="py-20 bg-white">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                                <div>
                                    <h2 className="text-3xl font-extrabold text-gray-900 mb-5">
                                        A Website Assistant That Never Sleeps
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Most website visitors leave without ever reaching out — not because they don't have
                                        questions, but because nobody's there to answer. Blueflow's Chat Widget puts an AI
                                        assistant on your site that answers instantly using your own content, and seamlessly
                                        hands the conversation to a real person the moment a visitor asks for one.
                                    </p>
                                    <ul className="space-y-3">
                                        {highlights.map((h) => (
                                            <li key={h} className="flex items-start gap-3 text-gray-700 text-sm">
                                                <span className="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </span>
                                                {h}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <SectionIllustration icon={MessageSquareText} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">Everything Included</h2>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                {features.map((f) => (
                                    <div key={f.title} className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                                        <IconTile icon={f.icon} color="blue" size="md" className="mb-3" />
                                        <h3 className="font-bold text-gray-900 mb-2">{f.title}</h3>
                                        <p className="text-sm text-gray-500">{f.description}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* How it works */}
                    <section className="py-20 bg-white">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">How It Works</h2>
                            <div className="space-y-6">
                                {steps.map((step, i) => (
                                    <div key={step.title} className="flex items-start gap-5">
                                        <div className="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            {i + 1}
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-gray-900 mb-1">{step.title}</h3>
                                            <p className="text-sm text-gray-500">{step.description}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Metrics */}
                    <section className="py-16 bg-blue-600">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-6">
                                {metrics.map((m) => (
                                    <div key={m.label} className="text-center">
                                        <p className="text-3xl font-extrabold text-white mb-1">{m.value}</p>
                                        <p className="text-xs text-blue-100">{m.label}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* CTA */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">Ready to Get Started?</h2>
                            <p className="text-gray-500 mb-8">
                                Create your account, pick Chat Widget, and you'll be live on your site in minutes.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/register?plan=chat-widget" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Create Account
                                </Link>
                                <Link href="/#pricing" className="border-2 border-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-xl hover:border-blue-500 hover:text-blue-700 transition-all">
                                    View Pricing
                                </Link>
                            </div>
                        </div>
                    </section>
                </main>
                <Footer />
            </div>
        </>
    );
}
