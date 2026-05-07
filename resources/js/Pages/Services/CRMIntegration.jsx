import { Head, Link } from "@inertiajs/react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";

const highlights = [
    "Centralise every customer interaction — WhatsApp, email, calls, and social — in one place",
    "Automatically log deals, follow-ups, and customer data without manual entry",
    "AI-powered lead scoring that tells you who to call first",
    "Works with tools you already use: Google Workspace, Paystack, WhatsApp, and more",
];

const features = [
    { icon: "🗂️", title: "Unified Customer View", description: "Every call, message, purchase, and interaction in one timeline — no more switching between apps to find context." },
    { icon: "🤖", title: "Automated Data Entry", description: "Stop wasting time on manual logging. Blueflow captures leads, updates deal stages, and syncs contact info automatically." },
    { icon: "📈", title: "Pipeline Management", description: "Visual sales pipeline that moves deals forward automatically based on customer behaviour and triggers you define." },
    { icon: "🔔", title: "Smart Follow-up Reminders", description: "Never let a hot lead go cold. AI detects when a deal is stalling and prompts your team to act before it's too late." },
    { icon: "📊", title: "Revenue Analytics", description: "Track deal velocity, conversion rates, and team performance with dashboards built for Nigerian business realities." },
    { icon: "🔗", title: "Deep Integrations", description: "Connect your CRM to WhatsApp, email, Paystack, Google Sheets, and your existing tools — no developer needed." },
];

const steps = [
    { title: "Audit your current customer data", description: "We map where your customer information lives today — spreadsheets, WhatsApp, email — and design a clean migration plan." },
    { title: "Configure your pipeline and workflows", description: "Our team sets up deal stages, automated triggers, and follow-up sequences tailored to your sales process." },
    { title: "Connect your existing tools", description: "We integrate your CRM with WhatsApp, payment systems, email, and any other platform your team already uses." },
    { title: "Train your team and go live", description: "A 1-hour onboarding session gets your team up to speed. Most businesses see results within the first week." },
];

const metrics = [
    { value: "60%", label: "Less Manual Work" },
    { value: "2x", label: "Faster Deal Closure" },
    { value: "0", label: "Leads Slipping Through" },
    { value: "1 week", label: "To Go Live" },
];

export default function CRMIntegration() {
    return (
        <>
            <Head title="CRM Integration - Blueflow Automation">
                <meta
                    name="description"
                    content="Connect all your customer touchpoints and automate your sales pipeline. Blueflow CRM Integration is built for Nigerian businesses ready to scale."
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
                                        Stop Losing Customers to Disorganisation
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Most Nigerian businesses manage customers across WhatsApp chats, scattered
                                        spreadsheets, and memory. Leads fall through the cracks. Follow-ups get
                                        forgotten. Revenue walks out the door. Blueflow's CRM Integration brings
                                        every customer touchpoint into a single, automated system — so your team
                                        always knows who to talk to, when, and what to say.
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
                                <div className="bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl p-16 flex items-center justify-center text-8xl">
                                    🗂️
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">Everything Included</h2>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                {features.map((f) => (
                                    <div key={f.title} className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                        <div className="text-3xl mb-3">{f.icon}</div>
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
                            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">Ready to Get Organised?</h2>
                            <p className="text-gray-500 mb-8">
                                Book a free demo and see how CRM Integration can bring order to your customer management.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/demo" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Schedule Free Demo
                                </Link>
                                <Link href="/pricing" className="border-2 border-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-xl hover:border-blue-500 hover:text-blue-700 transition-all">
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
