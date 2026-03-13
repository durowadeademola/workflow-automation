import { Link } from "@inertiajs/react";

const caseStudies = [
    {
        emoji: "🍲",
        business: "Mama's Kitchen",
        category: "Restaurant",
        location: "Lagos, NG",
        challenge: "Missing 40–60% of incoming calls during peak hours which translated to sizable lost revenue and frustrated customers. Peak lunch and dinner rushes meant staff couldn't answer phones, leaving customers frustrated and orders going to competitors.",
        solution: "Implemented a WhatsApp-first ordering flow with automated confirmations, menu quick-replies integrated with their POS system, and smart reservation management.",
        timeframe: "Results achieved in 3 months",
        quote: "Blueflow changed everything for us. We never miss an order now, and our customers love how fast we respond. The WhatsApp system paid for itself in the first month. Best investment we made this year!",
        author: "Chinedu Okafor",
        role: "Owner, Mama's Kitchen",
        results: [
            { value: "₦465K", label: "Additional Revenue" },
            { value: "100%", label: "Calls Captured" },
            { value: "4.8★", label: "Customer Rating" },
        ],
    },
];

export default function CaseStudies() {
    return (
        <section className="py-20 bg-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center mb-14">
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        Success Stories
                    </h2>
                    <p className="text-gray-500 max-w-xl mx-auto">
                        Real Nigerian businesses, real results. See how automation transformed their operations.
                    </p>
                </div>

                {caseStudies.map((cs) => (
                    <div key={cs.business} className="bg-gradient-to-br from-blue-50 to-emerald-50 rounded-3xl p-8 md:p-12 border border-blue-100">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10">
                            {/* Left */}
                            <div>
                                {/* Header */}
                                <div className="flex items-center gap-3 mb-6">
                                    <div className="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-2xl shadow-sm">
                                        {cs.emoji}
                                    </div>
                                    <div>
                                        <p className="font-bold text-gray-900">{cs.business}</p>
                                        <p className="text-sm text-gray-500">{cs.category} • {cs.location}</p>
                                    </div>
                                </div>

                                {/* Challenge */}
                                <div className="mb-5">
                                    <h4 className="text-xs font-semibold uppercase tracking-wider text-red-600 mb-2">The Challenge</h4>
                                    <p className="text-gray-600 text-sm leading-relaxed">{cs.challenge}</p>
                                </div>

                                {/* Solution */}
                                <div className="mb-5">
                                    <h4 className="text-xs font-semibold uppercase tracking-wider text--600 mb-2">The Solution</h4>
                                    <p className="text-gray-600 text-sm leading-relaxed">{cs.solution}</p>
                                </div>

                                <p className="text-xs text-gray-400 italic">{cs.timeframe}</p>
                            </div>

                            {/* Right */}
                            <div className="flex flex-col justify-between gap-6">
                                {/* Quote */}
                                <blockquote className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative">
                                    <svg className="w-8 h-8 text--200 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                    </svg>
                                    <p className="text-gray-700 text-sm leading-relaxed italic mb-4">
                                        {cs.quote}
                                    </p>
                                    <div className="flex items-center gap-3">
                                        <div className="w-9 h-9 bg--600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                            {cs.author[0]}
                                        </div>
                                        <div>
                                            <p className="font-semibold text-gray-900 text-sm">{cs.author}</p>
                                            <p className="text-xs text-gray-500">{cs.role}</p>
                                        </div>
                                    </div>
                                </blockquote>

                                {/* Metrics */}
                                <div className="grid grid-cols-3 gap-4">
                                    {cs.results.map((r) => (
                                        <div key={r.label} className="bg-white rounded-2xl p-4 text-center shadow-sm border border-gray-100">
                                            <p className="text-2xl font-extrabold text--600 mb-1">{r.value}</p>
                                            <p className="text-xs text-gray-500">{r.label}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                ))}

                <div className="text-center mt-8">
                    <Link
                        href="/case-studies"
                        className="inline-flex items-center gap-2 text--700 font-semibold border-2 border--600 px-6 py-3 rounded-xl hover:bg--600 hover:text-white transition-all"
                    >
                        View All Case Studies
                    </Link>
                </div>
            </div>
        </section>
    );
}
