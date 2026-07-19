import { Head, Link } from "@inertiajs/react";
import { MessageSquareText, MessageCircle, Contact, Mail, CreditCard, Workflow } from "lucide-react";
import MainLayout from "@/Components/Layout/MainLayout";
import PageHero from "@/Components/Layout/PageHero";
import IconTile from "@/Components/Icons/IconTile";

const services = [
    {
        title: "Chat Widget",
        description: "An AI assistant embedded on your website that answers visitors instantly, 24/7 — and hands off to a human when asked.",
        features: ["Trained on your website content", "Human handoff on request", "Fully customizable branding", "Live agent inbox"],
        href: "/services/chat-widget",
        popular: true,
        icon: MessageSquareText,
    },
    {
        title: "WhatsApp Automation",
        description: "Turn WhatsApp into your 24/7 sales, support, and booking assistant. Never miss a customer message again.",
        features: ["Order processing", "Booking management", "Customer support", "Payment integration"],
        href: "/services/whatsapp-automation",
        icon: MessageCircle,
        comingSoon: true,
    },
    {
        title: "CRM Integration",
        description: "Centralise customer data and automate your entire relationship management pipeline.",
        features: ["Lead capture", "Pipeline automation", "Follow-up sequences", "Analytics"],
        href: "/services/crm-integration",
        icon: Contact,
        comingSoon: true,
    },
    {
        title: "Email Automation",
        description: "Send the right message to the right customer at the right time — automatically.",
        features: ["Campaign builder", "Smart segmentation", "Cart recovery", "A/B testing"],
        href: "/services/email-automation",
        icon: Mail,
        comingSoon: true,
    },
    {
        title: "Payment Automation",
        description: "Automate invoicing, payment collection, and reconciliation. Get paid faster.",
        features: ["Auto invoicing", "Payment reminders", "Paystack & Flutterwave", "Financial reports"],
        href: "/services/payment-automation",
        icon: CreditCard,
        comingSoon: true,
    },
    {
        title: "Workflow Automation",
        description: "Connect your tools and automate repetitive business processes end-to-end.",
        features: ["100+ app integrations", "Visual builder", "Approval workflows", "Data sync"],
        href: "/services/workflow-automation",
        icon: Workflow,
        comingSoon: true,
    },
];

export default function Services() {
    return (
        <MainLayout>
            <Head title="Services — Blueflow Automation" />

            <PageHero
                badge="Our Services"
                title="Complete Business Automation Suite"
                subtitle="Everything you need to automate and scale your Nigerian business — from WhatsApp to payments to custom workflows."
            />

            <section className="py-20 bg-white dark:bg-gray-900">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {services.map((service) => (
                            <div
                                key={service.title}
                                className={`relative bg-white dark:bg-gray-900 rounded-2xl p-6 border shadow-sm flex flex-col ${
                                    service.comingSoon
                                        ? "opacity-60 grayscale border-gray-100 dark:border-gray-800"
                                        : `hover:shadow-lg transition-all hover:-translate-y-1 ${service.popular ? "border-blue-500 ring-1 ring-blue-500" : "border-gray-100 dark:border-gray-800"}`
                                }`}
                            >
                                {service.popular && (
                                    <span className="absolute -top-3 left-6 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                        Most Popular
                                    </span>
                                )}
                                {service.comingSoon && (
                                    <span className="absolute -top-3 left-6 bg-gray-400 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                        Coming Soon
                                    </span>
                                )}

                                <IconTile icon={service.icon} color="blue" size="lg" className="mb-4" />
                                <h3 className="font-bold text-gray-900 dark:text-white text-lg mb-2">{service.title}</h3>
                                <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">{service.description}</p>

                                <ul className="space-y-2 mb-6 flex-1">
                                    {service.features.map((f) => (
                                        <li key={f} className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                            <svg className="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                            </svg>
                                            {f}
                                        </li>
                                    ))}
                                </ul>

                                {service.comingSoon ? (
                                    <span className="inline-flex items-center gap-1 text-sm font-semibold text-gray-400 dark:text-gray-500 cursor-not-allowed mt-auto">
                                        Learn More
                                    </span>
                                ) : (
                                    <Link
                                        href={service.href}
                                        className="inline-flex items-center gap-1 text-sm font-semibold text-blue-700 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors mt-auto"
                                    >
                                        Learn More
                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </Link>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Bottom CTA */}
            <section className="py-16 bg-blue-600">
                <div className="max-w-3xl mx-auto px-4 text-center">
                    <h2 className="text-2xl font-extrabold text-white mb-3">Not sure which service you need?</h2>
                    <p className="text-blue-100 text-sm mb-6">
                        Book a free call and we'll recommend exactly what will have the biggest impact on your business.
                    </p>
                    <Link
                        href="/contact"
                        className="inline-flex items-center gap-2 bg-white dark:bg-gray-900 text-blue-700 dark:text-blue-400 font-semibold px-6 py-3 rounded-xl hover:bg-blue-50 transition-colors text-sm"
                    >
                        Get a Free Recommendation →
                    </Link>
                </div>
            </section>
        </MainLayout>
    );
}
