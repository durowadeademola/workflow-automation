import { Link } from "@inertiajs/react";
import { useScrollAnimation, useCountUp } from "@/hooks/useScrollAnimation";

// No fabricated success stories — this renders nothing until real case
// studies (with the client's permission) are added here.
const caseStudies = [];

function ResultMetric({ value, prefix, suffix, label, isDecimal, delay, trigger }) {
    const count = useCountUp(isDecimal ? Math.round(value * 10) : value, 1800, 0, trigger);
    const display = isDecimal ? (count / 10).toFixed(1) : count;

    return (
        <div
            className="bg-white dark:bg-gray-900 rounded-2xl p-4 text-center shadow-sm border border-gray-100 dark:border-gray-800"
            style={{
                opacity: trigger ? 1 : 0,
                transform: trigger ? "translateY(0) scale(1)" : "translateY(20px) scale(0.95)",
                transition: `opacity 0.5s ease ${delay}ms, transform 0.5s ease ${delay}ms`,
            }}
        >
            <p className="text-2xl font-extrabold text-blue-600 mb-1">
                {prefix}{display}{suffix}
            </p>
            <p className="text-xs text-gray-500 dark:text-gray-400">{label}</p>
        </div>
    );
}

export default function CaseStudies() {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [cardRef, cardVisible] = useScrollAnimation(0.1);

    if (caseStudies.length === 0) {
        return null;
    }

    return (
        <section id="case-studies" className="py-20 bg-white dark:bg-gray-900">
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
                        Success Stories
                    </h2>
                    <p className="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        Real Nigerian businesses, real results. See how automation transformed their operations.
                    </p>
                </div>

                {caseStudies.map((cs) => (
                    <div
                        key={cs.business}
                        ref={cardRef}
                        className="bg-gradient-to-br from-blue-50 dark:from-blue-900/40 to-emerald-50 dark:to-emerald-900/40 rounded-3xl p-8 md:p-12 border border-blue-100 dark:border-blue-800"
                        style={{
                            opacity: cardVisible ? 1 : 0,
                            transform: cardVisible ? "translateY(0)" : "translateY(50px)",
                            transition: "opacity 0.7s ease 100ms, transform 0.7s ease 100ms",
                        }}
                    >
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10">
                            {/* Left */}
                            <div>
                                <div className="flex items-center gap-3 mb-6">
                                    <div className="w-12 h-12 bg-white dark:bg-gray-900 rounded-2xl flex items-center justify-center shadow-sm">
                                        <cs.icon className="w-6 h-6 text-blue-600" strokeWidth={1.75} />
                                    </div>
                                    <div>
                                        <p className="font-bold text-gray-900 dark:text-white">{cs.business}</p>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">{cs.category} • {cs.location}</p>
                                    </div>
                                </div>

                                <div className="mb-5">
                                    <h4 className="text-xs font-semibold uppercase tracking-wider text-red-600 mb-2">The Challenge</h4>
                                    <p className="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{cs.challenge}</p>
                                </div>

                                <div className="mb-5">
                                    <h4 className="text-xs font-semibold uppercase tracking-wider text-blue-600 mb-2">The Solution</h4>
                                    <p className="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{cs.solution}</p>
                                </div>

                                <p className="text-xs text-gray-400 dark:text-gray-500 italic">{cs.timeframe}</p>
                            </div>

                            {/* Right */}
                            <div className="flex flex-col justify-between gap-6">
                                <blockquote className="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-800 relative">
                                    <svg className="w-8 h-8 text-blue-200 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                    </svg>
                                    <p className="text-gray-700 dark:text-gray-200 text-sm leading-relaxed italic mb-4">{cs.quote}</p>
                                    <div className="flex items-center gap-3">
                                        <div className="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                            {cs.author[0]}
                                        </div>
                                        <div>
                                            <p className="font-semibold text-gray-900 dark:text-white text-sm">{cs.author}</p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">{cs.role}</p>
                                        </div>
                                    </div>
                                </blockquote>

                                {/* Animated metrics */}
                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    {cs.results.map((r, i) => (
                                        <ResultMetric
                                            key={r.label}
                                            {...r}
                                            delay={200 + i * 150}
                                            trigger={cardVisible}
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                ))}

                <div
                    className="text-center mt-8"
                    style={{
                        opacity: cardVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 600ms",
                    }}
                >
                    <Link
                        href="/contact"
                        className="inline-flex items-center gap-2 text-blue-700 dark:text-blue-400 font-semibold border-2 border-blue-600 px-6 py-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all"
                    >
                        Become Our Next Success Story
                    </Link>
                </div>
            </div>
        </section>
    );
}
