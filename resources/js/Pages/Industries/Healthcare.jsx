import { Head, Link } from "@inertiajs/react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";

const highlights = [
    "Patients book, reschedule, and cancel appointments via WhatsApp — no phone calls needed",
    "Automated reminders sent 24 hours and 1 hour before every appointment",
    "Post-visit follow-up messages with care instructions sent automatically",
    "Lab result notifications and prescription refill reminders without staff involvement",
];

const features = [
    { icon: "📅", title: "WhatsApp Appointment Booking", description: "Patients check doctor availability and book appointments directly on WhatsApp at any time of day — your reception desk handles only complex cases." },
    { icon: "🔔", title: "Automated Reminder Sequences", description: "Every patient gets a reminder 24 hours before and again 1 hour before their appointment — drastically cutting no-shows without any manual effort." },
    { icon: "📋", title: "Digital Intake Forms", description: "New patients complete their medical history, symptoms, and insurance details via WhatsApp before they arrive — saving time at the desk and in the consultation." },
    { icon: "🧪", title: "Lab Results Notifications", description: "Patients are automatically notified when their results are ready for collection or review — no more calling the clinic repeatedly to check." },
    { icon: "💊", title: "Medication & Follow-Up Reminders", description: "Scheduled reminders for prescriptions, follow-up appointments, and chronic condition check-ins sent automatically based on each patient's care plan." },
    { icon: "⭐", title: "Patient Feedback Collection", description: "Automated satisfaction surveys sent after every visit help you identify issues early and build a strong reputation with consistent positive reviews." },
];

const steps = [
    { title: "Connect your WhatsApp Business number", description: "We link your clinic's number to SmartFlow and configure your doctors, consultation types, and available appointment slots." },
    { title: "Build your booking and reminder flows", description: "We design conversation flows for new and returning patients, appointment confirmations, reminders, and rescheduling requests." },
    { title: "Set up your follow-up sequences", description: "We configure post-visit messages, lab result alerts, prescription reminders, and any chronic care follow-up schedules your clinic needs." },
    { title: "Launch and reduce no-shows immediately", description: "Go live within days. Appointment reminders alone typically cut no-shows by 50–60% in the first month." },
];

const metrics = [
    { value: "60%", label: "Fewer No-Shows" },
    { value: "₦300K", label: "Revenue Recovered Monthly" },
    { value: "3hrs", label: "Less Admin Daily" },
    { value: "4.9★", label: "Patient Satisfaction" },
];

const testimonial = {
    quote: "Our no-show rate dropped by more than half within the first six weeks. The reception team used to spend half their day on the phone chasing appointments. Now that time goes into actually helping patients when they arrive.",
    name: "Dr. Emeka Nwosu",
    role: "Medical Director, Greenleaf Clinic — Abuja",
};

export default function Healthcare() {
    return (
        <>
            <Head title="Automation for Healthcare & Clinics">
                <meta
                    name="description"
                    content="Reduce no-shows, automate appointment reminders, and streamline patient communications for your clinic. SmartFlow helps Nigerian healthcare providers run more efficiently."
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
                                        Healthcare & Clinics
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 mb-5">
                                        Fewer No-Shows. Less Admin. Better Patient Care.
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Nigerian clinics lose hundreds of thousands of naira every month to
                                        no-shows, while reception staff spend most of their day answering calls,
                                        scheduling appointments, and chasing patients manually. SmartFlow
                                        automates the entire patient communication journey — from booking to
                                        reminders to post-visit follow-ups — so your team can focus on delivering
                                        quality care instead of managing logistics.
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
                                    🏥
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">
                                Built for Nigerian Clinics & Healthcare Providers
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
                                Ready to Reduce No-Shows and Free Up Your Team?
                            </h2>
                            <p className="text-gray-500 mb-8">
                                Book a free demo and we'll show you exactly how SmartFlow works for clinics and healthcare providers like yours.
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
