import { Link } from "@inertiajs/react";
import { useScrollAnimation } from "@/hooks/useScrollAnimation";

const services = [
    {
        title: "Chat Widget",
        description: "An AI assistant embedded on your website that answers visitors instantly, 24/7",
        features: ["Trained on your website content", "Hands off to a human on request", "Fully customizable branding", "Live agent inbox", "Appointment booking", "Lead qualification", "FAQs and knowledge base"],
        href: "/services/chat-widget",
        popular: true,
    },
    {
        title: "Marketing Automation",
        description: "Multi-step customer journeys that nurture leads, remind about appointments, and win back inactive customers automatically",
        features: ["Multi-step customer journeys", "Behavior-based triggers", "Personalization", "Multi-channel support"],
        href: "/services/marketing-automation",
        isNew: true,
    },
    {
        title: "Workflow Automation",
        description: "Connect your tools and automate repetitive business processes",
        features: ["Multi-tool integration", "Custom workflows", "Data processing", "Scheduling"],
        href: "/services/workflow-automation",
        custom: true,
    },
    {
        title: "WhatsApp Automation",
        description: "Transform WhatsApp into your 24/7 sales, support, and booking assistant",
        features: ["Order processing", "Booking management", "Customer support", "Payment integration"],
        href: "/services/whatsapp-automation",
        comingSoon: true,
    },
    {
        title: "Email Automation",
        description: "Automated operational emails — confirmations, receipts, and reports, sent the moment they're triggered",
        features: ["Appointment confirmations", "Billing & payment receipts", "Performance reports", "Real-time triggers"],
        href: "/services/email-automation",
        comingSoon: true,
    },
    {
        title: "Payment Automation",
        description: "Seamless payment processing, invoicing, and reconciliation",
        features: ["Paystack integration", "Auto-invoicing", "Payment tracking", "Refunds"],
        href: "/services/payment-automation",
        comingSoon: true,
    },
];

export default function Services() {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [cardsRef, cardsVisible] = useScrollAnimation(0.1);

    return (
        <section className="py-20 bg-gray-50 dark:bg-gray-800">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    ref={headingRef}
                    className="text-center mb-14"
                    style={{
                        opacity: headingVisible ? 1 : 0,
                        transform: headingVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease, transform 0.6s ease",
                    }}
                >
                    <p className="text-blue-600 font-semibold text-sm uppercase tracking-widest mb-3">Our Services</p>
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Complete Business Automation Suite
                    </h2>
                    <p className="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        Everything you need to automate and scale your business operations
                    </p>
                </div>

                <div ref={cardsRef} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {services.map((service, i) => (
                        <div
                            key={service.title}
                            className={`relative bg-white dark:bg-gray-900 rounded-2xl p-6 border shadow-sm flex flex-col ${
                                service.comingSoon
                                    ? "opacity-60 grayscale border-gray-100 dark:border-gray-800"
                                    : `hover:shadow-lg transition-all hover:-translate-y-1 ${service.popular ? "border-blue-500 ring-1 ring-blue-500" : "border-gray-100 dark:border-gray-800"}`
                            }`}
                            style={{
                                opacity: cardsVisible ? (service.comingSoon ? 0.6 : 1) : 0,
                                transform: cardsVisible ? "translateY(0)" : "translateY(55px)",
                                transition: `opacity 0.6s ease ${i * 100}ms, transform 0.6s ease ${i * 100}ms`,
                            }}
                        >
                            {service.popular && (
                                <span className="absolute -top-3 left-6 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Popular
                                </span>
                            )}
                            {service.comingSoon && (
                                <span className="absolute -top-3 left-6 bg-gray-400 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Coming Soon
                                </span>
                            )}
                            {service.custom && (
                                <span className="absolute -top-3 left-6 bg-violet-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Custom Quote
                                </span>
                            )}
                            {service.isNew && (
                                <span className="absolute -top-3 left-6 bg-emerald-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    New
                                </span>
                            )}
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

                <div
                    className="text-center mt-10"
                    style={{
                        opacity: cardsVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 700ms",
                    }}
                >
                    <Link
                        href="/services"
                        className="inline-flex items-center gap-2 text-blue-700 dark:text-blue-400 font-semibold border-2 border-blue-600 px-6 py-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all"
                    >
                        View All Services
                    </Link>
                </div>
            </div>
        </section>
    );
}
