import { Head, Link } from "@inertiajs/react";
import { Bot, ClipboardList, CalendarCheck, CreditCard, BarChart3, Users, MessageCircle } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Handle unlimited simultaneous conversations without hiring more staff",
    "Integrate directly with your POS, CRM, and payment systems",
    "AI-powered responses that sound human and understand context",
    "Works with your existing WhatsApp Business number",
];

const features = [
    { icon: Bot, title: "AI Chatbot", description: "Smart responses to common questions, orders, and inquiries — trained on your specific business." },
    { icon: ClipboardList, title: "Order Management", description: "Take and confirm orders automatically with real-time inventory checks and payment collection." },
    { icon: CalendarCheck, title: "Booking System", description: "Automated appointment scheduling, confirmations, and reminders via WhatsApp." },
    { icon: CreditCard, title: "Payment Collection", description: "Send Paystack/Flutterwave payment links directly in conversation and confirm instantly." },
    { icon: BarChart3, title: "Analytics Dashboard", description: "Track every conversation, conversion, and revenue generated through WhatsApp." },
    { icon: Users, title: "Team Inbox", description: "Route complex queries to the right human agent with full conversation history." },
];

const steps = [
    { title: "Connect your WhatsApp Business number", description: "We link your existing number to the Blueflow platform via the official WhatsApp Business API." },
    { title: "Build your automation flows", description: "Our team designs custom conversation flows tailored to your business — menus, bookings, FAQs." },
    { title: "Integrate with your tools", description: "We connect WhatsApp to your POS, payment gateway, CRM, and any other system you use." },
    { title: "Go live and start automating", description: "Launch in days. Customers interact naturally while automation handles everything behind the scenes." },
];

const metrics = [
    { value: "100%", label: "Messages Captured" },
    { value: "< 1s", label: "Response Time" },
    { value: "3x", label: "Higher Conversion" },
    { value: "4hrs", label: "Saved Daily" },
];

export default function WhatsappAutomation() {
    return (
        <>
            <Head title="WhatsApp Automation - Blueflow">
                <meta name="description" content="Automate your WhatsApp..." />
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
                                        Your Business, Open 24/7 on WhatsApp
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Nigeria has over 90 million WhatsApp users — your customers are already there.
                                        Blueflow's WhatsApp Automation transforms your business number into an intelligent
                                        assistant that takes orders, handles bookings, answers questions, and processes
                                        payments around the clock.
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
                                <SectionIllustration icon={MessageCircle} accent="blue" />
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

                    {/* CTA */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Ready to Get Started?</h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-8">
                                Book a free demo and see how WhatsApp Automation can transform your business.
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
