import { Link } from "@inertiajs/react";
import { useScrollAnimation, useCountUp } from "@/hooks/useScrollAnimation";

const stats = [
    { value: 100, suffix: "+", label: "Businesses Automated" },
    { value: 99.5, suffix: "%", label: "Uptime Guarantee", isDecimal: true },
    { value: 24, suffix: "/7", label: "AI Support" },
];

function StatCard({ value, suffix, label, isDecimal, delay, trigger }) {
    const count = useCountUp(isDecimal ? value * 10 : value, 2000, 0, trigger);
    const display = isDecimal ? (count / 10).toFixed(1) : count;

    return (
        <div
            className="bg-white rounded-2xl px-6 py-5 shadow-md border border-gray-100 hover:shadow-lg transition-shadow"
            style={{
                opacity: trigger ? 1 : 0,
                transform: trigger ? "translateY(0)" : "translateY(40px)",
                transition: `opacity 0.6s ease ${delay}ms, transform 0.6s ease ${delay}ms`,
            }}
        >
            <p className="text-3xl font-extrabold text-blue-600 mb-1">
                {display}{suffix}
            </p>
            <p className="text-sm text-gray-500 font-medium">{label}</p>
        </div>
    );
}

export default function Hero() {
    const [ref, isVisible] = useScrollAnimation(0.1);

    return (
        <section
            ref={ref}
            className="relative bg-gradient-to-br from-blue-50 via-white to-emerald-50 pt-32 pb-20 overflow-hidden"
        >
            {/* Background decorative blobs */}
            <div className="absolute top-20 right-0 w-96 h-96 bg-blue-100 rounded-full opacity-40 blur-3xl -z-0" />
            <div className="absolute bottom-10 left-0 w-72 h-72 bg-emerald-100 rounded-full opacity-30 blur-3xl -z-0" />

            <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                {/* Badge */}
                <div
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(24px)",
                        transition: "opacity 0.5s ease 0ms, transform 0.5s ease 0ms",
                    }}
                    className="inline-flex items-center gap-2 bg-blue-100 text-blue-800 text-xs sm:text-sm font-medium px-3 sm:px-4 py-1.5 rounded-full mb-6 text-center"
                >
                    <span className="w-2 h-2 bg-blue-500 rounded-full animate-pulse flex-shrink-0" />
                    AI-Powered Automation for Nigerian Businesses
                </div>

                {/* Headline */}
                <h1
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(32px)",
                        transition: "opacity 0.6s ease 100ms, transform 0.6s ease 100ms",
                    }}
                    className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6 max-w-4xl mx-auto"
                >
                    Automate Everything.{" "}
                    <span className="text-blue-600">Grow Faster.</span>
                </h1>

                {/* Subheadline */}
                <p
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(32px)",
                        transition: "opacity 0.6s ease 200ms, transform 0.6s ease 200ms",
                    }}
                    className="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed"
                >
                    Transform your business operations with AI-powered automation.
                    Save time, reduce costs, and scale effortlessly.
                </p>

                {/* CTA Buttons */}
                <div
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(32px)",
                        transition: "opacity 0.6s ease 300ms, transform 0.6s ease 300ms",
                    }}
                    className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16"
                >
                   <Link href="/contact"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 hover:shadow-blue-300 hover:-translate-y-0.5"
                    >
                        Get Started Free
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                    <a
                        href="#how-it-works"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-gray-700 font-semibold px-8 py-3.5 rounded-xl border-2 border-gray-200 hover:border-blue-300 hover:text-blue-700 transition-all hover:-translate-y-0.5"
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        See How It Works
                    </a>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-2xl mx-auto">
                    {stats.map((stat, i) => (
                        <StatCard
                            key={stat.label}
                            {...stat}
                            delay={400 + i * 120}
                            trigger={isVisible}
                        />
                    ))}
                </div>
            </div>
        </section>
    );
}
