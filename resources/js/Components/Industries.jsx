import { useState } from "react";
import { Link } from "@inertiajs/react";
import { useScrollAnimation, useCountUp } from "@/hooks/useScrollAnimation";

const industries = [
    {
        id: "restaurants",
        label: "Restaurants & Cafés",
        title: "Restaurants & Cafés",
        description: "Never miss a reservation. Turn WhatsApp into your smart booking assistant.",
        features: [
            "Automated reservation confirmations",
            "Menu updates and daily specials",
            "Order tracking and delivery updates",
            "Customer feedback collection",
        ],
        metric: { value: 40, suffix: "%", label: "More Reservations" },
        metricNote: "Average result from restaurants & cafés using Blueflow",
        href: "/industries/restaurants",
    },
    {
        id: "ecommerce",
        label: "E-commerce & Retail",
        title: "E-commerce & Retail",
        description: "Automate order notifications, cart recovery, and customer support at scale.",
        features: [
            "Automated order confirmations",
            "Cart abandonment recovery",
            "Shipping updates via WhatsApp",
            "Returns & refunds automation",
        ],
        metric: { value: 3, suffix: "x", label: "Faster Support" },
        metricNote: "Average customer response improvement for e-commerce stores",
        href: "/industries/ecommerce",
    },
    {
        id: "hotels",
        label: "Hotels & Hospitality",
        title: "Hotels & Hospitality",
        description: "Streamline bookings, check-ins, and guest communications effortlessly.",
        features: [
            "Booking confirmations & reminders",
            "Room service automation",
            "Guest feedback collection",
            "Upsell and cross-sell automation",
        ],
        metric: { value: 25, suffix: "%", label: "Higher Occupancy" },
        metricNote: "Average occupancy improvement across hospitality clients",
        href: "/industries/hospitality",
    },
    {
        id: "healthcare",
        label: "Healthcare & Clinics",
        title: "Healthcare & Clinics",
        description: "Reduce no-shows, automate appointment reminders, and collect patient feedback.",
        features: [
            "Appointment scheduling via WhatsApp",
            "Automated patient reminders",
            "Lab results notifications",
            "Follow-up care messages",
        ],
        metric: { value: 60, suffix: "%", label: "Fewer No-Shows" },
        metricNote: "Average reduction in missed appointments for clinics",
        href: "/industries/healthcare",
    },
    {
        id: "real-estate",
        label: "Real Estate",
        title: "Real Estate",
        description: "Qualify leads automatically and keep prospects warm until they're ready to buy.",
        features: [
            "Lead qualification automation",
            "Property listing broadcasts",
            "Viewing appointment scheduling",
            "Follow-up sequences",
        ],
        metric: { value: 2, suffix: "x", label: "More Qualified Leads" },
        metricNote: "Average lead qualification improvement for real estate agents",
        href: "/industries/real-estate",
    },
    {
        id: "professional-services",
        label: "Professional Services",
        title: "Professional Services",
        description: "Automate client onboarding, invoicing, and communication workflows.",
        features: [
            "Client onboarding automation",
            "Invoice & payment reminders",
            "Document collection workflows",
            "Meeting scheduling & follow-ups",
        ],
        metric: { value: 5, suffix: "hrs", label: "Saved Per Week" },
        metricNote: "Average time saved per professional services business",
        href: "/industries/professional-services",
    },
];

function MetricCard({ value, suffix, label, note, trigger }) {
    const count = useCountUp(value, 1600, 0, trigger);

    return (
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-10 text-center max-w-xs w-full">
            <p className="text-6xl font-extrabold text-blue-600 mb-2">
                {count}{suffix}
            </p>
            <p className="text-xl font-bold text-gray-900 dark:text-white mb-3">{label}</p>
            <p className="text-sm text-gray-500 dark:text-gray-400">{note}</p>
        </div>
    );
}

export default function Industries() {
    const [active, setActive] = useState("restaurants");
    const industry = industries.find((i) => i.id === active);

    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [contentRef, contentVisible] = useScrollAnimation(0.1);

    return (
        <section className="py-20 bg-white dark:bg-gray-900">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    ref={headingRef}
                    className="text-center mb-12"
                    style={{
                        opacity: headingVisible ? 1 : 0,
                        transform: headingVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease, transform 0.6s ease",
                    }}
                >
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Built for Your Industry
                    </h2>
                    <p className="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        Pre-built solutions designed specifically for Nigerian businesses in your sector
                    </p>
                </div>

                {/* Tabs */}
                <div
                    className="flex flex-wrap justify-center gap-2 mb-10"
                    style={{
                        opacity: headingVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 200ms",
                    }}
                >
                    {industries.map((ind) => (
                        <button
                            key={ind.id}
                            onClick={() => setActive(ind.id)}
                            className={`text-sm font-medium px-4 py-2 rounded-full transition-all ${
                                active === ind.id
                                    ? "bg-blue-600 text-white shadow-md"
                                    : "bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-blue-700 dark:hover:text-blue-300"
                            }`}
                        >
                            {ind.label}
                        </button>
                    ))}
                </div>

                {/* Tab Content */}
                <div
                    ref={contentRef}
                    className="bg-gradient-to-br from-blue-50 dark:from-blue-900/40 to-emerald-50 dark:to-emerald-900/40 rounded-3xl p-8 md:p-12"
                    style={{
                        opacity: contentVisible ? 1 : 0,
                        transform: contentVisible ? "translateY(0)" : "translateY(40px)",
                        transition: "opacity 0.7s ease 100ms, transform 0.7s ease 100ms",
                    }}
                >
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                        <div>
                            <h3 className="text-2xl font-extrabold text-gray-900 dark:text-white mb-3">{industry.title}</h3>
                            <p className="text-gray-600 dark:text-gray-300 mb-6">{industry.description}</p>
                            <h4 className="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                                What You Get:
                            </h4>
                            <ul className="space-y-3 mb-8">
                                {industry.features.map((f) => (
                                    <li key={f} className="flex items-center gap-3 text-gray-700 dark:text-gray-200">
                                        <span className="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                        {f}
                                    </li>
                                ))}
                            </ul>
                            <Link
                                href={industry.href}
                                className="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors"
                            >
                                See {industry.title} Solutions
                            </Link>
                        </div>

                        <div className="flex items-center justify-center">
                            <MetricCard
                                value={industry.metric.value}
                                suffix={industry.metric.suffix}
                                label={industry.metric.label}
                                note={industry.metricNote}
                                trigger={contentVisible}
                            />
                        </div>
                    </div>
                </div>

                <p
                    className="text-center text-gray-500 dark:text-gray-400 mt-6 text-sm"
                    style={{
                        opacity: contentVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 400ms",
                    }}
                >
                    Don't see your industry? We've got you covered.{" "}
                    <Link href="/industries" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                        View All Industries
                    </Link>
                </p>
            </div>
        </section>
    );
}
