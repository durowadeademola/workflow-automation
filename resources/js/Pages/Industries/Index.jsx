import { Head, Link } from "@inertiajs/react";
import { UtensilsCrossed, ShoppingCart, Hotel, HeartPulse, Home, Briefcase } from "lucide-react";
import MainLayout from "@/Components/Layout/MainLayout";
import PageHero from "@/Components/Layout/PageHero";
import IconTile from "@/Components/Icons/IconTile";

const industries = [
    {
        title: "Restaurants & Cafés",
        description: "Turn WhatsApp into your smart ordering, booking, and feedback assistant.",
        features: ["WhatsApp order taking", "Table & delivery booking", "Payment collection", "Review automation"],
        href: "/industries/restaurants",
        icon: UtensilsCrossed,
    },
    {
        title: "E-commerce & Retail",
        description: "Automate order notifications, cart recovery, and customer support at scale.",
        features: ["Order confirmations", "Cart recovery", "Shipping updates", "Returns automation"],
        href: "/industries/ecommerce",
        icon: ShoppingCart,
    },
    {
        title: "Hotels & Hospitality",
        description: "Streamline bookings, check-ins, and guest communications effortlessly.",
        features: ["Booking confirmations", "Room service automation", "Guest feedback", "Upsell automation"],
        href: "/industries/hospitality",
        icon: Hotel,
    },
    {
        title: "Healthcare & Clinics",
        description: "Reduce no-shows, automate reminders, and streamline patient communication.",
        features: ["Appointment booking", "Automated reminders", "Lab result alerts", "Follow-up care"],
        href: "/industries/healthcare",
        icon: HeartPulse,
    },
    {
        title: "Real Estate",
        description: "Qualify leads automatically and keep prospects warm until they're ready to buy.",
        features: ["Lead qualification", "Listing broadcasts", "Viewing scheduling", "Follow-up sequences"],
        href: "/industries/real-estate",
        icon: Home,
    },
    {
        title: "Professional Services",
        description: "Automate client onboarding, invoicing, and communication workflows.",
        features: ["Client onboarding", "Invoice reminders", "Document collection", "Meeting scheduling"],
        href: "/industries/professional-services",
        icon: Briefcase,
    },
];

export default function IndustriesIndex() {
    return (
        <MainLayout>
            <Head title="Industries — Blueflow Automation" />

            <PageHero
                badge="Industries We Serve"
                title="Automation Built for Your Industry"
                subtitle="Pre-built automation solutions designed specifically for Nigerian businesses in your sector."
            />

            <section className="py-20 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {industries.map((industry) => (
                            <div
                                key={industry.title}
                                className="relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 flex flex-col"
                            >
                                <IconTile icon={industry.icon} color="blue" size="lg" className="mb-4" />
                                <h3 className="font-bold text-gray-900 text-lg mb-2">{industry.title}</h3>
                                <p className="text-sm text-gray-500 mb-4">{industry.description}</p>

                                <ul className="space-y-2 mb-6 flex-1">
                                    {industry.features.map((f) => (
                                        <li key={f} className="flex items-center gap-2 text-sm text-gray-600">
                                            <svg className="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                            </svg>
                                            {f}
                                        </li>
                                    ))}
                                </ul>

                                <Link
                                    href={industry.href}
                                    className="inline-flex items-center gap-1 text-sm font-semibold text-blue-700 hover:text-blue-800 transition-colors mt-auto"
                                >
                                    Learn More
                                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            <section className="py-16 bg-blue-600">
                <div className="max-w-3xl mx-auto px-4 text-center">
                    <h2 className="text-2xl font-extrabold text-white mb-3">Don't see your industry?</h2>
                    <p className="text-blue-100 text-sm mb-6">
                        We've automated processes for all kinds of businesses. Tell us what you need and we'll show you what's possible.
                    </p>
                    <Link
                        href="/contact"
                        className="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3 rounded-xl hover:bg-blue-50 transition-colors text-sm"
                    >
                        Talk to Our Team →
                    </Link>
                </div>
            </section>
        </MainLayout>
    );
}
