import { Head, Link } from "@inertiajs/react";
import { UtensilsCrossed, CalendarCheck, CreditCard, Star, Megaphone, BarChart3, Quote } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Take orders and reservations automatically via WhatsApp — even at 2am",
    "Send payment links and confirm instantly without manual follow-up",
    "Automated feedback collection after every visit to protect your reputation",
    "Loyalty reminders and promo blasts that bring customers back consistently",
];

const features = [
    { icon: UtensilsCrossed, title: "WhatsApp Order Taking", description: "Customers send their order via WhatsApp. The system confirms, calculates the total, and sends a payment link — all without staff involvement." },
    { icon: CalendarCheck, title: "Table & Delivery Booking", description: "Automated reservation management with confirmation messages, reminders 1 hour before, and instant rescheduling." },
    { icon: CreditCard, title: "Instant Payment Collection", description: "Paystack and Flutterwave payment links sent automatically at checkout. No more waiting for transfers or handling cash disputes." },
    { icon: Star, title: "Review & Feedback Automation", description: "Every customer gets a follow-up message after their visit asking for a Google review or quick feedback — building your reputation on autopilot." },
    { icon: Megaphone, title: "Promo & Loyalty Campaigns", description: "Send targeted WhatsApp blasts for new menu items, weekend specials, or loyalty rewards to customers who haven't visited in a while." },
    { icon: BarChart3, title: "Sales & Peak Hour Reports", description: "Daily and weekly reports showing your busiest hours, top-selling items, and revenue trends — delivered straight to your phone." },
];

const steps = [
    { title: "Connect your WhatsApp Business number", description: "We link your existing number to Blueflow and configure your digital menu, pricing, and order flow." },
    { title: "Set up your payment and booking flows", description: "We integrate Paystack or Flutterwave and configure your reservation system with your table capacity and hours." },
    { title: "Build your customer database", description: "Every customer who orders or books is captured automatically — building a list you can market to again and again." },
    { title: "Launch and start filling seats", description: "Go live in days. Your restaurant runs smoother, your staff focus on service, and revenue grows without extra headcount." },
];

const metrics = [
    { value: "3x", label: "More Repeat Customers" },
    { value: "0", label: "Missed Orders" },
    { value: "< 1s", label: "Response Time" },
    { value: "5hrs", label: "Staff Time Saved Daily" },
];

const testimonial = {
    quote: "We used to miss orders when the line got busy. Now WhatsApp handles everything automatically and we've grown our repeat customer base significantly.",
    name: "Adaeze O.",
    role: "Owner, Lagos Restaurant",
};

export default function RestaurantsCafes() {
    return (
        <>
            <Head title="Automation for Restaurants & Cafés">
                <meta
                    name="description"
                    content="Automate orders, bookings, payments, and customer follow-ups for your restaurant or café. Blueflow helps Nigerian food businesses run smarter and serve more customers."
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
                                        Restaurants & Cafés
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 mb-5">
                                        Serve More Customers Without Hiring More Staff
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Running a restaurant in Nigeria means managing orders on WhatsApp, handling
                                        cash and transfers, chasing reviews, and keeping regulars coming back —
                                        all while trying to keep food quality high. Blueflow automates the
                                        operational side so your team can focus entirely on delivering a great
                                        experience. More orders processed, fewer errors, happier customers.
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
                                <SectionIllustration icon={UtensilsCrossed} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">Built for Food Businesses</h2>
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
                            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">Ready to Automate Your Restaurant?</h2>
                            <p className="text-gray-500 mb-8">
                                Book a free demo and we'll show you exactly how Blueflow works for food businesses like yours.
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
