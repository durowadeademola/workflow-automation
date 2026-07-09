import { Head, Link } from "@inertiajs/react";
import { Hotel, ClipboardList, BellRing, Gift, Star, BarChart3, Quote } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Accept direct bookings via WhatsApp 24/7 — no OTA commission on any reservation",
    "Automated confirmation, check-in reminders, and guest info sent without staff involvement",
    "Upsell room upgrades, airport pickup, and dining reservations automatically",
    "Post-stay review requests sent at the perfect moment to boost your ratings",
];

const features = [
    { icon: Hotel, title: "Direct WhatsApp Booking", description: "Guests check availability, see room options with photos and pricing, and confirm their booking directly on WhatsApp — no commission, no middlemen." },
    { icon: ClipboardList, title: "Booking Confirmations & Reminders", description: "Professional confirmation messages sent instantly on every booking, followed by a reminder 24 hours before arrival with directions and check-in details." },
    { icon: BellRing, title: "In-Stay Guest Requests", description: "Guests request room service, housekeeping, extra towels, or report issues via WhatsApp. Staff get instant notifications with no phone calls needed." },
    { icon: Gift, title: "Upsell Automation", description: "Automatically offer room upgrades, spa bookings, restaurant reservations, and airport transfers at the right moment during the guest journey." },
    { icon: Star, title: "Review Collection", description: "Every guest receives a follow-up message after checkout asking for a Google or TripAdvisor review — building your reputation consistently." },
    { icon: BarChart3, title: "Occupancy & Revenue Dashboard", description: "Real-time view of room occupancy, revenue per booking, channel performance, and your busiest seasons — all in one place." },
];

const steps = [
    { title: "Connect your WhatsApp Business number", description: "We link your hotel's number to Blueflow and configure your room types, availability calendar, and pricing tiers." },
    { title: "Build your booking and guest journey flows", description: "We design conversation flows for enquiries, confirmations, check-in instructions, in-stay requests, and post-stay follow-ups." },
    { title: "Integrate your payment and property systems", description: "We connect Paystack or Flutterwave for deposits, and link your existing property management system if you have one." },
    { title: "Launch and start filling rooms directly", description: "Go live in days. Every booking, guest message, and follow-up is handled automatically — your staff focus on delivering a great stay." },
];

const metrics = [
    { value: "25%", label: "Higher Occupancy" },
    { value: "0%", label: "OTA Commission" },
    { value: "40%", label: "Direct Bookings" },
    { value: "4.9★", label: "Avg Guest Rating" },
];

const testimonial = {
    quote: "We used to pay Booking.com on almost every reservation. Now most of our bookings come directly through WhatsApp. We kept all that commission and our guests actually prefer the experience.",
    name: "Mrs. Funke Adeleke",
    role: "General Manager, Grandeur Hotel — Port Harcourt",
};

export default function Hotels() {
    return (
        <>
            <Head title="Automation for Hotels & Hospitality">
                <meta
                    name="description"
                    content="Automate bookings, guest communications, upsells, and reviews for your hotel. Blueflow helps Nigerian hospitality businesses fill more rooms and earn more per guest."
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
                                        Hotels & Hospitality
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 mb-5">
                                        Fill More Rooms and Stop Paying OTA Commissions
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Nigerian hotels are losing 15–30% of every booking to Booking.com,
                                        Jumia Travel, and other platforms. Meanwhile, guests are already on
                                        WhatsApp — the simplest booking channel you have. Blueflow turns your
                                        WhatsApp number into a direct booking engine that handles enquiries,
                                        confirmations, guest requests, and reviews automatically, so your team
                                        can focus on delivering an exceptional stay.
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
                                <SectionIllustration icon={Hotel} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">
                                Built for Nigerian Hotels & Guesthouses
                            </h2>
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

                    {/* Testimonial */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <div className="w-12 h-12 mx-auto mb-6 bg-blue-50 rounded-2xl flex items-center justify-center"><Quote className="w-6 h-6 text-blue-600" strokeWidth={1.75} /></div>
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
                                Ready to Automate Your Hotel?
                            </h2>
                            <p className="text-gray-500 mb-8">
                                Book a free demo and we'll show you exactly how Blueflow works for hospitality businesses like yours.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contact" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Schedule Free Demo
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
