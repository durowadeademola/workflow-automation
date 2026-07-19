import { useState } from "react";
import { Link } from "@inertiajs/react";
import { useScrollAnimation } from "@/hooks/useScrollAnimation";

const allPlansInclude = [
    "14-day free trial",
    "Cancel anytime",
    "Refund policy",
    "Free training",
    "Regular updates",
    "Nigerian payment options",
];

export default function Pricing({ plans = [] }) {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [cardsRef, cardsVisible] = useScrollAnimation(0.1);
    const [cycle, setCycle] = useState("monthly");
    const isYearly = cycle === "yearly";
    const anyYearlyDiscount = plans.some((p) => p.has_yearly_discount);

    return (
        <section id="pricing" className="py-20 bg-gray-50 dark:bg-gray-800">
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
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Simple, Transparent Pricing
                    </h2>
                    <p className="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        No hidden fees. No long-term contracts. Cancel anytime.
                    </p>
                </div>

                {plans.length > 0 && (
                    <div className="flex items-center justify-center gap-3 mb-10">
                        <span className={`text-sm font-medium ${!isYearly ? "text-gray-900 dark:text-white" : "text-gray-400 dark:text-gray-500"}`}>Monthly</span>
                        <button
                            type="button"
                            onClick={() => setCycle(isYearly ? "monthly" : "yearly")}
                            className={`relative w-11 h-6 rounded-full transition-colors ${isYearly ? "bg-blue-600" : "bg-gray-200 dark:bg-gray-700"}`}
                            aria-label="Toggle yearly billing"
                        >
                            <span
                                className={`absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform ${isYearly ? "translate-x-5" : "translate-x-0"}`}
                            />
                        </button>
                        <span className={`text-sm font-medium ${isYearly ? "text-gray-900 dark:text-white" : "text-gray-400 dark:text-gray-500"}`}>
                            Yearly{anyYearlyDiscount && <span className="text-emerald-600 font-semibold"> (save more)</span>}
                        </span>
                    </div>
                )}

                {plans.length > 0 ? (
                    <div ref={cardsRef} className="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                        {plans.map((plan, i) => (
                            <div
                                key={plan.slug}
                                className={`relative bg-white dark:bg-gray-900 rounded-3xl p-8 flex flex-col shadow-sm border transition-all hover:shadow-xl hover:-translate-y-1 ${
                                    plan.is_popular
                                        ? "border-blue-500 ring-2 ring-blue-500 shadow-lg"
                                        : "border-gray-100 dark:border-gray-800"
                                }`}
                                style={{
                                    opacity: cardsVisible ? 1 : 0,
                                    transform: cardsVisible ? "translateY(0)" : "translateY(60px)",
                                    transition: `opacity 0.65s ease ${i * 130}ms, transform 0.65s ease ${i * 130}ms`,
                                }}
                            >
                                {plan.is_popular && (
                                    <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                                        <span className="bg-blue-600 text-white text-xs font-semibold px-4 py-1.5 rounded-full shadow-md">
                                            Most Popular
                                        </span>
                                    </div>
                                )}

                                {!isYearly && plan.has_active_promo && (
                                    <span className="absolute -top-4 right-4 bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-md">
                                        {plan.promo_percent}% off
                                    </span>
                                )}

                                {isYearly && plan.has_yearly_discount && (
                                    <span className="absolute -top-4 right-4 bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-md">
                                        Save {plan.yearly_discount_percent}%
                                    </span>
                                )}

                                <div className="mb-6">
                                    <h3 className="text-xl font-extrabold text-gray-900 dark:text-white mb-1">{plan.name}</h3>
                                    {plan.description && (
                                        <p className="text-sm text-gray-500 dark:text-gray-400">{plan.description}</p>
                                    )}
                                </div>

                                <div className="mb-6">
                                    {isYearly ? (
                                        plan.has_yearly_discount ? (
                                            <>
                                                <span className="text-lg text-gray-400 dark:text-gray-500 line-through mr-2">
                                                    ₦{Number(plan.yearly_regular_price).toLocaleString("en-NG")}
                                                </span>
                                                <span className="text-4xl font-extrabold text-red-600">
                                                    ₦{Number(plan.yearly_effective_price).toLocaleString("en-NG")}
                                                </span>
                                            </>
                                        ) : (
                                            <span className="text-4xl font-extrabold text-gray-900 dark:text-white">
                                                ₦{Number(plan.yearly_effective_price).toLocaleString("en-NG")}
                                            </span>
                                        )
                                    ) : plan.has_active_promo ? (
                                        <>
                                            <span className="text-lg text-gray-400 dark:text-gray-500 line-through mr-2">
                                                ₦{Number(plan.amount).toLocaleString("en-NG")}
                                            </span>
                                            <span className="text-4xl font-extrabold text-red-600">
                                                ₦{Number(plan.promo_price).toLocaleString("en-NG")}
                                            </span>
                                        </>
                                    ) : (
                                        <span className="text-4xl font-extrabold text-gray-900 dark:text-white">
                                            ₦{Number(plan.amount).toLocaleString("en-NG")}
                                        </span>
                                    )}
                                    <span className="text-gray-400 dark:text-gray-500 text-sm">/{isYearly ? "year" : "month"}</span>
                                </div>

                                {!isYearly && plan.has_active_promo && plan.promo_ends_at && (
                                    <p className="text-xs text-red-600 -mt-4 mb-6">
                                        Offer ends {new Date(plan.promo_ends_at).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}
                                    </p>
                                )}

                                <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    {plan.appointment_limit ? `${Number(plan.appointment_limit).toLocaleString("en-NG")} appointments/month` : "Unlimited appointments"}
                                </p>
                                <p className="text-xs text-gray-500 dark:text-gray-400 mb-6">
                                    {plan.lead_limit ? `${Number(plan.lead_limit).toLocaleString("en-NG")} qualified leads/month` : "Unlimited qualified leads"}
                                </p>

                                {plan.features?.length > 0 && (
                                    <ul className="space-y-3 mb-8 flex-1">
                                        {plan.features.map((f) => (
                                            <li key={f} className="flex items-start gap-2.5 text-sm text-gray-600 dark:text-gray-300">
                                                <svg className="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                                </svg>
                                                {f}
                                            </li>
                                        ))}
                                    </ul>
                                )}

                                <Link
                                    href={`/register?plan=${plan.slug}`}
                                    className={`block text-center font-semibold py-3 rounded-xl transition-all mt-auto ${
                                        plan.is_popular
                                            ? "bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-200"
                                            : "border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:border-blue-500 hover:text-blue-700 dark:hover:text-blue-400"
                                    }`}
                                >
                                    Get Started
                                </Link>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-center text-sm text-gray-400 dark:text-gray-500">
                        Pricing is being updated — {" "}
                        <Link href="/contact" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                            contact us
                        </Link>{" "}
                        for current plans.
                    </p>
                )}

                {/* All plans include */}
                <div
                    className="mt-12 bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800"
                    style={{
                        opacity: cardsVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 500ms",
                    }}
                >
                    <h4 className="text-sm font-semibold text-gray-700 dark:text-gray-200 text-center mb-5">All Plans Include:</h4>
                    <div className="flex flex-wrap justify-center gap-4">
                        {allPlansInclude.map((f) => (
                            <span key={f} className="flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
                                <svg className="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                </svg>
                                {f}
                            </span>
                        ))}
                    </div>
                </div>

                <div className="text-center mt-8">
                    <Link href="/contact" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline text-sm">
                        Not sure which plan fits? Talk to our team →
                    </Link>
                </div>
            </div>
        </section>
    );
}
