import { Link } from "@inertiajs/react";

const stats = [
    { value: "100+", label: "Businesses Automated" },
    { value: "99.5%", label: "Uptime Guarantee" },
    { value: "24/7", label: "AI Support" },
];

export default function Hero() {
    return (
        <section className="relative bg-gradient-to-br from-green-50 via-white to-emerald-50 pt-32 pb-20 overflow-hidden">
            {/* Background decorative blobs */}
            <div className="absolute top-20 right-0 w-96 h-96 bg-green-100 rounded-full opacity-40 blur-3xl -z-0" />
            <div className="absolute bottom-10 left-0 w-72 h-72 bg-emerald-100 rounded-full opacity-30 blur-3xl -z-0" />

            <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                {/* Badge */}
                <div className="inline-flex items-center gap-2 bg-green-100 text-green-800 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
                    <span className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
                    AI-Powered Automation for Nigerian Businesses
                </div>

                {/* Headline */}
                <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6 max-w-4xl mx-auto">
                    Automate Everything.{" "}
                    <span className="text-green-600">Grow Faster.</span>
                </h1>

                {/* Subheadline */}
                <p className="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Transform your business operations with AI-powered automation.
                    Save time, reduce costs, and scale effortlessly.
                </p>

                {/* CTA Buttons */}
                <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <Link
                        href="/auth/signup"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-200 hover:shadow-green-300 hover:-translate-y-0.5"
                    >
                        Get Started Free
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                    <a
                        href="#how-it-works"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-gray-700 font-semibold px-8 py-3.5 rounded-xl border-2 border-gray-200 hover:border-green-300 hover:text-green-700 transition-all hover:-translate-y-0.5"
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
                    {stats.map((stat) => (
                        <div
                            key={stat.label}
                            className="bg-white rounded-2xl px-6 py-5 shadow-md border border-gray-100 hover:shadow-lg transition-shadow"
                        >
                            <p className="text-3xl font-extrabold text-green-600 mb-1">{stat.value}</p>
                            <p className="text-sm text-gray-500 font-medium">{stat.label}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
