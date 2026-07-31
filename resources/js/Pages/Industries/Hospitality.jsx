import { Head, Link } from "@inertiajs/react";
import { Hotel, CalendarCheck, HelpCircle, ClipboardList, UserCheck, BookOpen, BarChart3 } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Guests get instant answers on availability, rates, and amenities any time of day",
    "AI collects booking requests through natural conversation, with automatic conflict checking",
    "New inquiries captured automatically, along with what the guest actually wants",
    "Handoff to front desk staff during working hours — WhatsApp fallback outside them",
];

const features = [
    { icon: CalendarCheck, title: "AI Booking Requests", description: "Guests request a room or check availability directly through conversation with the AI — no forms, no phone tag. Conflicts are checked automatically." },
    { icon: HelpCircle, title: "Instant FAQ Answers", description: "Questions about rates, amenities, and policies are answered instantly from your own FAQ list — with zero AI cost and zero wait." },
    { icon: ClipboardList, title: "Guest Inquiry Capture", description: "When a visitor wants to leave their details, the AI collects their name, contact info, and what they're reaching out about." },
    { icon: UserCheck, title: "Working-Hours-Aware Handoff", description: "The AI knows your real front desk hours — handing off to staff when they're available, and gracefully pointing to WhatsApp otherwise." },
    { icon: BookOpen, title: "Your Own Knowledge Base", description: "Add details about your rooms, amenities, and policies so the AI answers with your property's actual information, not generic guesses." },
    { icon: BarChart3, title: "FAQ View Analytics", description: "See exactly what guests ask most, so you always know what to clarify or add." },
];

const steps = [
    { title: "Add the chat widget to your website", description: "Embed Blueflow's widget on your property's site and set your assistant's name, greeting, and real front desk hours." },
    { title: "Add your FAQs and property knowledge", description: "Enter your most common guest questions — rates, amenities, policies — and any other background the AI should know." },
    { title: "The AI starts answering and taking requests", description: "Guests ask questions and request bookings directly through conversation — automatically, any time of day." },
    { title: "Refine as you go", description: "Check which FAQs get the most views, add details for anything the AI couldn't answer, and adjust hours any time from your dashboard." },
];

const metrics = [
    { value: "24/7", label: "AI Availability" },
    { value: "0", label: "Forms To Fill Out" },
    { value: "Auto", label: "Conflict-Free Booking" },
    { value: "Live", label: "Working-Hours Awareness" },
];

export default function Hotels() {
    return (
        <>
            <Head title="Automation for Hotels & Hospitality">
                <meta
                    name="description"
                    content="Give guests instant answers and let the AI collect booking requests, any time of day. Blueflow helps Nigerian hotels and guesthouses respond faster."
                />
            </Head>
            <div className="min-h-screen bg-white dark:bg-gray-900">
                <Navbar />
                <main>
                    {/* Overview */}
                    <section className="py-20 bg-white dark:bg-gray-900">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                                <div>
                                    <span className="inline-block bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                                        Hotels & Hospitality
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-5">
                                        Instant Answers. Automatic Booking Requests.
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Guests expect a fast reply, whatever time they reach out. Blueflow's AI
                                        chat widget answers questions about availability, rates, and amenities
                                        instantly, collects booking requests through natural conversation, and
                                        only hands off to front desk staff when a conversation genuinely needs
                                        one — so your team can focus on the stay, not the phone.
                                    </p>
                                    <ul className="space-y-3">
                                        {highlights.map((h) => (
                                            <li key={h} className="flex items-start gap-3 text-gray-700 dark:text-gray-200 text-sm">
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
                                <SectionIllustration icon={Hotel} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">
                                Built for Nigerian Hotels & Guesthouses
                            </h2>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                {features.map((f) => (
                                    <div key={f.title} className="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                                        <IconTile icon={f.icon} color="blue" size="md" className="mb-3" />
                                        <h3 className="font-bold text-gray-900 dark:text-white mb-2">{f.title}</h3>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">{f.description}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* How it works */}
                    <section className="py-20 bg-white dark:bg-gray-900">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">How It Works</h2>
                            <div className="space-y-6">
                                {steps.map((step, i) => (
                                    <div key={step.title} className="flex items-start gap-5">
                                        <div className="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            {i + 1}
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-gray-900 dark:text-white mb-1">{step.title}</h3>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{step.description}</p>
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
                    <section className="py-20 bg-white dark:bg-gray-900">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">
                                Ready to Automate Your Hotel?
                            </h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-8">
                                Book a free demo and we'll show you exactly how Blueflow works for hospitality businesses like yours.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contact" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Schedule Free Demo
                                </Link>
                                <Link href="/#pricing" className="border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-semibold px-6 py-3 rounded-xl hover:border-blue-500 hover:text-blue-700 dark:hover:text-blue-300 transition-all">
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
