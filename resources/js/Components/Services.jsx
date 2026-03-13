import { Link } from "@inertiajs/react";

const services = [
    {
        title: "WhatsApp Automation",
        description: "Transform WhatsApp into your 24/7 sales, support, and booking assistant",
        features: ["Order processing", "Booking management", "Customer support", "Payment integration"],
        href: "/services/whatsapp-automation",
        popular: true,
    },
    {
        title: "CRM Integration",
        description: "Centralize customer data and automate relationship management",
        features: ["Data synchronization", "Lead management", "Pipeline automation", "Analytics"],
        href: "/services/crm-integration",
    },
    {
        title: "Email Automation",
        description: "Automated email campaigns, follow-ups, and customer journeys",
        features: ["Campaign automation", "Segmentation", "A/B testing", "Analytics"],
        href: "/services/email-automation",
    },
    {
        title: "Payment Automation",
        description: "Seamless payment processing, invoicing, and reconciliation",
        features: ["Paystack integration", "Auto-invoicing", "Payment tracking", "Refunds"],
        href: "/services/payment-automation",
    },
    {
        title: "Workflow Automation",
        description: "Connect your tools and automate repetitive business processes",
        features: ["Multi-tool integration", "Custom workflows", "Data processing", "Scheduling"],
        href: "/services/workflow-automation",
    },
    {
        title: "Custom Solutions",
        description: "Tailored automation solutions for your specific business needs",
        features: ["Consultation", "Custom development", "Integration", "Support"],
        href: "/services/custom-solutions",
    },
];

export default function Services() {
    return (
        <section className="py-20 bg-gray-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center mb-14">
                    <p className="text-blue-600 font-semibold text-sm uppercase tracking-widest mb-3">Our Services</p>
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        Complete Business Automation Suite
                    </h2>
                    <p className="text-gray-500 max-w-xl mx-auto">
                        Everything you need to automate and scale your business operations
                    </p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {services.map((service) => (
                        <div
                            key={service.title}
                            className={`relative bg-white rounded-2xl p-6 border shadow-sm hover:shadow-lg transition-all hover:-translate-y-1 flex flex-col ${
                                service.popular ? "border-blue-500 ring-1 ring-blue-500" : "border-gray-100"
                            }`}
                        >
                            {service.popular && (
                                <span className="absolute -top-3 left-6 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Popular
                                </span>
                            )}
                            <h3 className="font-bold text-gray-900 text-lg mb-2">{service.title}</h3>
                            <p className="text-sm text-gray-500 mb-4">{service.description}</p>
                            <ul className="space-y-2 mb-6 flex-1">
                                {service.features.map((f) => (
                                    <li key={f} className="flex items-center gap-2 text-sm text-gray-600">
                                        <svg className="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                        </svg>
                                        {f}
                                    </li>
                                ))}
                            </ul>
                            <Link
                                href={service.href}
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

                <div className="text-center mt-10">
                    <Link
                        href="/services"
                        className="inline-flex items-center gap-2 text-blue-700 font-semibold border-2 border-blue-600 px-6 py-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all"
                    >
                        View All Services
                    </Link>
                </div>
            </div>
        </section>
    );
}
