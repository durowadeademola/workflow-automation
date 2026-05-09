import { Head, Link } from "@inertiajs/react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";

const highlights = [
    "New client onboarding runs automatically — documents, contracts, and welcome sequences without manual effort",
    "Invoices sent on schedule with automated reminders that get clients paying on time",
    "Proposal follow-ups happen automatically so no opportunity ever goes cold",
    "Clients book meetings and consultations via WhatsApp without back-and-forth emails",
];

const features = [
    { icon: "🚀", title: "Client Onboarding Automation", description: "New clients receive a welcome message, onboarding checklist, document request, and kickoff meeting invite automatically the moment they sign up." },
    { icon: "🧾", title: "Automated Invoicing", description: "Invoices generated and sent automatically at project milestones, monthly retainer dates, or any schedule you define — branded and professional every time." },
    { icon: "⏰", title: "Payment Reminder Sequences", description: "Polite but persistent WhatsApp and email reminders for due and overdue invoices — reducing late payments without awkward conversations." },
    { icon: "📅", title: "Meeting & Consultation Scheduling", description: "Clients book calls and meetings via a simple WhatsApp message based on your real-time availability — no phone tag, no scheduling assistant needed." },
    { icon: "📝", title: "Contract & Document Collection", description: "Send contracts for e-signature and track completion automatically. Chase missing documents with timed follow-ups so projects never stall." },
    { icon: "💬", title: "Client Check-In Messages", description: "Scheduled check-in messages keep relationships warm between projects and ensure clients think of you first when the next need arises." },
];

const steps = [
    { title: "Map your current client workflows", description: "We document your onboarding process, billing schedule, proposal flow, and communication touchpoints to identify every automation opportunity." },
    { title: "Build your onboarding and billing automations", description: "We configure your welcome sequences, invoice schedules, payment reminders, and contract workflows tailored to how your business operates." },
    { title: "Set up scheduling and follow-up flows", description: "We connect your calendar for meeting bookings and build follow-up sequences for proposals, check-ins, and client re-engagement." },
    { title: "Launch and win back your time", description: "Go live within days. Admin that used to take hours each week runs automatically — freeing you to focus on delivery and business development." },
];

const metrics = [
    { value: "10hrs", label: "Saved Per Week" },
    { value: "40%", label: "Faster Invoice Payment" },
    { value: "90%", label: "Client Retention Rate" },
    { value: "3x", label: "Proposal Close Rate" },
];

const testimonial = {
    quote: "We automated our entire client intake process — the welcome email, document requests, contract, and first invoice all go out automatically now. What used to take our team three days of back-and-forth happens in a few hours without anyone touching it.",
    name: "Seun Adeleke",
    role: "Managing Partner, Adeleke & Associates — Lagos",
};

export default function ProfessionalServices() {
    return (
        <>
            <Head title="Automation for Professional Services">
                <meta
                    name="description"
                    content="Automate client onboarding, invoicing, proposals, and follow-ups for your professional services business. SmartFlow helps Nigerian consultants and agencies win back hours every week."
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
                                    <span className="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                                        Professional Services
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 mb-5">
                                        Win Back 10 Hours a Week. Get Paid Faster.
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Consultants, lawyers, accountants, and agencies in Nigeria spend a
                                        disproportionate amount of time on tasks that have nothing to do with
                                        their actual expertise — chasing documents, sending invoice reminders,
                                        following up on proposals, and manually onboarding new clients.
                                        SmartFlow automates all of it so you can spend your time on billable
                                        work and business development instead of administrative follow-up.
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
                                    💼
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">
                                Built for Nigerian Consultants, Agencies & Firms
                            </h2>
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

                    {/* Testimonial */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <div className="text-4xl mb-6">💬</div>
                            <blockquote className="text-xl font-medium text-gray-800 leading-relaxed mb-6">
                                "{testimonial.quote}"
                            </blockquote>
                            <p className="text-sm font-semibold text-gray-900">{testimonial.name}</p>
                            <p className="text-sm text-gray-500">{testimonial.role}</p>
                        </div>
                    </section>

                    {/* CTA */}
                    <section className="py-20 bg-white">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">
                                Ready to Automate Your Practice?
                            </h2>
                            <p className="text-gray-500 mb-8">
                                Book a free demo and we'll show you exactly how SmartFlow works for professional services businesses like yours.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <a
                                    href="https://forms.gle/rG4Jf1xoguD67mH26"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors"
                                >
                                    Schedule Free Demo
                                </a>
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
