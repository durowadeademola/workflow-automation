import { Link } from "@inertiajs/react";

const plans = [
    {
        name: "Starter",
        description: "Perfect for small businesses just getting started with automation",
        price: "₦20,000",
        period: "/month",
        cta: "Start Free Trial",
        ctaHref: "/demo",
        popular: false,
        features: [
            "WhatsApp Business API",
            "Up to 1,000 conversations/month",
            "Basic automation flows",
            "Email support",
            "Mobile app access",
            "2 team members",
            "Standard templates",
        ],
    },
    {
        name: "Professional",
        description: "For growing businesses that need more power and flexibility",
        price: "₦35,000",
        period: "/month",
        cta: "Start Free Trial",
        ctaHref: "/demo",
        popular: true,
        features: [
            "Everything in Starter",
            "Up to 5,000 conversations/month",
            "Advanced automation + AI",
            "CRM integration",
            "Priority support (24/7)",
            "5 team members",
            "Custom workflows",
            "Analytics dashboard",
            "Payment processing",
        ],
    },
    {
        name: "Enterprise",
        description: "For established businesses with complex needs",
        price: "₦60,000",
        period: "/month",
        cta: "Contact Sales",
        ctaHref: "/contact",
        popular: false,
        features: [
            "Everything in Professional",
            "Unlimited conversations",
            "Custom AI training",
            "Dedicated account manager",
            "White-label options",
            "Unlimited team members",
            "API access",
            "Custom integrations",
            "SLA guarantee",
            "Advanced security",
        ],
    },
];

const allPlansInclude = [
    "7-day free trial",
    "No setup fees",
    "Cancel anytime",
    "Free training",
    "Regular updates",
    "Nigerian payment options",
];

export default function Pricing() {
    return (
        <section className="py-20 bg-gray-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center mb-14">
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        Simple, Transparent Pricing
                    </h2>
                    <p className="text-gray-500 max-w-xl mx-auto">
                        No hidden fees. No long-term contracts. Cancel anytime.
                    </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                    {plans.map((plan) => (
                        <div
                            key={plan.name}
                            className={`relative bg-white rounded-3xl p-8 flex flex-col shadow-sm border transition-all hover:shadow-xl hover:-translate-y-1 ${
                                plan.popular
                                    ? "border-green-500 ring-2 ring-green-500 shadow-lg"
                                    : "border-gray-100"
                            }`}
                        >
                            {plan.popular && (
                                <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                                    <span className="bg-green-600 text-white text-xs font-semibold px-4 py-1.5 rounded-full shadow-md">
                                        Most Popular
                                    </span>
                                </div>
                            )}

                            <div className="mb-6">
                                <h3 className="text-xl font-extrabold text-gray-900 mb-1">{plan.name}</h3>
                                <p className="text-sm text-gray-500">{plan.description}</p>
                            </div>

                            <div className="mb-6">
                                <span className="text-4xl font-extrabold text-gray-900">{plan.price}</span>
                                <span className="text-gray-400 text-sm">{plan.period}</span>
                            </div>

                            <ul className="space-y-3 mb-8 flex-1">
                                {plan.features.map((f) => (
                                    <li key={f} className="flex items-start gap-2.5 text-sm text-gray-600">
                                        <svg className="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                        </svg>
                                        {f}
                                    </li>
                                ))}
                            </ul>

                            <Link
                                href={plan.ctaHref}
                                className={`block text-center font-semibold py-3 rounded-xl transition-all mt-auto ${
                                    plan.popular
                                        ? "bg-green-600 text-white hover:bg-green-700 shadow-md shadow-green-200"
                                        : "border-2 border-gray-200 text-gray-700 hover:border-green-500 hover:text-green-700"
                                }`}
                            >
                                {plan.cta}
                            </Link>
                        </div>
                    ))}
                </div>

                {/* All plans include */}
                <div className="mt-12 bg-white rounded-2xl p-6 border border-gray-100">
                    <h4 className="text-sm font-semibold text-gray-700 text-center mb-5">All Plans Include:</h4>
                    <div className="flex flex-wrap justify-center gap-4">
                        {allPlansInclude.map((f) => (
                            <span key={f} className="flex items-center gap-1.5 text-sm text-gray-600">
                                <svg className="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                </svg>
                                {f}
                            </span>
                        ))}
                    </div>
                </div>

                <div className="text-center mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link href="/pricing" className="text-green-700 font-semibold hover:underline text-sm">
                        Compare All Features →
                    </Link>
                    <span className="text-gray-300 hidden sm:block">|</span>
                    <Link href="/contact" className="text-green-700 font-semibold hover:underline text-sm">
                        Talk to Our Team →
                    </Link>
                </div>
            </div>
        </section>
    );
}
