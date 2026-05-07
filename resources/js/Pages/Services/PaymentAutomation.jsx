import { Head, Link } from "@inertiajs/react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";

const highlights = [
    "Accept payments via Paystack, Flutterwave, and bank transfer — all automated",
    "Send invoices, payment links, and receipts without lifting a finger",
    "Automatic payment reminders that recover failed and overdue payments",
    "Real-time reconciliation — know exactly what's been paid and what's outstanding",
];

const features = [
    { icon: "💳", title: "Payment Link Generation", description: "Automatically generate and send Paystack or Flutterwave payment links via WhatsApp, email, or SMS the moment an order is placed." },
    { icon: "🧾", title: "Automated Invoicing", description: "Professional invoices created and delivered instantly — with due dates, line items, and your branding built in." },
    { icon: "🔔", title: "Overdue Reminders", description: "Smart follow-up sequences chase unpaid invoices for you — politely at first, more firmly as deadlines pass." },
    { icon: "✅", title: "Instant Reconciliation", description: "Every payment is matched to an order automatically. Your books stay clean without any manual data entry." },
    { icon: "📊", title: "Revenue Dashboard", description: "See daily collections, outstanding balances, and payment trends at a glance — built for Nigerian payment realities." },
    { icon: "🔗", title: "Full System Sync", description: "Payments trigger downstream actions — update your CRM, notify your team on WhatsApp, and fulfil orders automatically." },
];

const steps = [
    { title: "Connect your payment gateway", description: "We link your existing Paystack or Flutterwave account to Blueflow in minutes — no new accounts needed." },
    { title: "Configure your payment flows", description: "We set up automated invoicing, payment link triggers, and reminder sequences tailored to how your business collects money." },
    { title: "Integrate with your sales channels", description: "Payments connect to your WhatsApp, CRM, and order management so every transaction is tracked end-to-end." },
    { title: "Go live and get paid faster", description: "From day one, customers receive payment requests automatically and your team stops chasing invoices manually." },
];

const metrics = [
    { value: "3x", label: "Faster Collections" },
    { value: "90%", label: "Fewer Missed Payments" },
    { value: "₦0", label: "Manual Reconciliation Cost" },
    { value: "100%", label: "Payment Visibility" },
];

export default function PaymentAutomation() {
    return (
        <>
            <Head title="Payment Automation - Blueflow Automation">
                <meta
                    name="description"
                    content="Stop chasing payments manually. Blueflow automates invoicing, payment links, reminders, and reconciliation for Nigerian businesses."
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
                                        Get Paid Faster Without the Back-and-Forth
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Chasing payments is one of the biggest time drains for Nigerian businesses.
                                        Between sending invoices manually, following up on overdue balances, and
                                        reconciling bank transfers, hours disappear every week. Blueflow's Payment
                                        Automation handles every step — from generating payment links to confirming
                                        receipt — so your cash flow stays healthy and your team stays focused.
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
                                    💳
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
                            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">Ready to Get Paid Faster?</h2>
                            <p className="text-gray-500 mb-8">
                                Book a free demo and see how Payment Automation can clean up your cash flow from day one.
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
