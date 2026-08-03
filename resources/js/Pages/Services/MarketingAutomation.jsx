import { Head, Link } from "@inertiajs/react";
import { Megaphone, Workflow, Zap, CalendarCheck, RefreshCw, UserCheck, Send } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";
import Pricing from "@/Components/Pricing";

const highlights = [
    "Multi-step journeys — Welcome, Reminder, Testimonial, Offer — sent automatically over days, not all at once",
    "Behavior-based triggers enroll customers automatically: appointment booked, booking abandoned, or gone quiet",
    "Personalization pulled straight from your chat widget and customer data — name, reason for reaching out, appointment details",
    "Built for email today, with WhatsApp, SMS, and Telegram support on the way",
];

const features = [
    { icon: Workflow, title: "Multi-Step Customer Journeys", description: "Build sequences like Welcome → Reminder → Testimonial → Offer, each step spaced out over hours or days, not sent all at once." },
    { icon: Zap, title: "Behavior-Based Triggers", description: "Automatically enroll a customer the moment they book an appointment, abandon a booking, or go quiet for a while — no manual work." },
    { icon: CalendarCheck, title: "Appointment & Event Reminders", description: "Built for clinics and service businesses — remind customers automatically ahead of their appointment." },
    { icon: RefreshCw, title: "Re-engagement Journeys", description: "Win back customers who've gone quiet with an automatic re-engagement sequence." },
    { icon: UserCheck, title: "Personalization", description: "Every message can include the customer's name, their reason for reaching out, or appointment details — pulled automatically, no manual merge needed." },
    { icon: Send, title: "Multi-Channel Ready", description: "Built to run over email, WhatsApp, SMS, or Telegram." },
];

const steps = [
    { title: "Build your journey", description: "Add steps like Welcome, Reminder, Testimonial, and Offer, each with its own wait time before the next one goes out." },
    { title: "Choose what triggers it", description: "Enroll customers manually, or set a behavior trigger — appointment booked, abandoned booking, or inactivity." },
    { title: "Personalize each message", description: "Use merge fields to include the customer's name and details automatically in every step." },
    { title: "Let it run automatically", description: "Once it's live, the journey runs itself — no manual follow-up needed." },
];

const metrics = [
    { value: "Auto", label: "Multi-Step Journeys" },
    { value: "3", label: "Behavior Triggers" },
    { value: "Live", label: "Personalization" },
    { value: "Email", label: "Channel Live Today" },
];

export default function MarketingAutomation({ plans = [] }) {
    return (
        <>
            <Head title="Marketing Automation - Blueflow Automation">
                <meta
                    name="description"
                    content="Automated multi-step customer journeys with behavior-based triggers and personalization — built for Nigerian businesses."
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
                                    <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-5">
                                        Nurture Every Customer, Automatically
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Most businesses either message everyone the same thing once, or don't follow
                                        up at all. Blueflow's Marketing Automation builds real customer journeys —
                                        multi-step sequences that trigger automatically based on what a customer
                                        actually does — so no interested lead or appointment ever gets forgotten.
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
                                <SectionIllustration icon={Megaphone} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">Everything Included</h2>
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

                    {/* Pricing — scoped to marketing-automation plans specifically,
                        not the chat-widget plans the homepage's #pricing shows. */}
                    <Pricing plans={plans} />

                    {/* CTA */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Ready to Automate Your Customer Journeys?</h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-8">
                                Book a free demo and see how Marketing Automation can turn your leads into loyal customers.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contact" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Schedule Free Demo
                                </Link>
                                <Link href="/register" className="border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-semibold px-6 py-3 rounded-xl hover:border-blue-500 hover:text-blue-700 dark:hover:text-blue-300 transition-all">
                                    Get Started
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
