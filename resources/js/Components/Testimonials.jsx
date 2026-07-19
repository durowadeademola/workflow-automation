import { useScrollAnimation } from "@/hooks/useScrollAnimation";

function Stars({ count = 5 }) {
    return (
        <div className="flex gap-0.5 mb-3">
            {Array.from({ length: count }).map((_, i) => (
                <svg key={i} className="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
            ))}
        </div>
    );
}

function ReviewCard({ r }) {
    return (
        <div className="w-[300px] sm:w-[360px] shrink-0 bg-gray-50 dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 hover:shadow-md transition-shadow flex flex-col">
            <Stars count={r.rating} />
            <p className="text-gray-700 dark:text-gray-200 text-sm leading-relaxed italic flex-1 mb-5">
                "{r.description}"
            </p>
            <div className="flex items-center gap-3">
                <div className="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">
                    {r.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <p className="font-semibold text-gray-900 dark:text-white text-sm">{r.name}</p>
                    <p className="text-xs text-gray-500 dark:text-gray-400">
                        {[r.job_title, r.company].filter(Boolean).join(", ")}
                        {r.location ? ` • ${r.location}` : ""}
                    </p>
                </div>
            </div>
        </div>
    );
}

export default function Testimonials({ reviews = [] }) {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);

    if (reviews.length === 0) {
        return null;
    }

    // Speed scales with how many reviews there are so the pace feels the
    // same whether there are 2 or 20. The track is rendered twice back to
    // back and animated exactly halfway (-50%), so the loop point lands on
    // an identical copy and reads as an endless, seamless scroll.
    const duration = Math.max(20, reviews.length * 6);

    return (
        <section className="py-20 bg-white dark:bg-gray-900 overflow-hidden">
            <style>{`
                @keyframes testimonialsScroll {
                    from { transform: translateX(0); }
                    to { transform: translateX(-50%); }
                }
                .testimonials-track {
                    animation: testimonialsScroll ${duration}s linear infinite;
                }
                .testimonials-track:hover {
                    animation-play-state: paused;
                }
                @media (prefers-reduced-motion: reduce) {
                    .testimonials-track {
                        animation: none;
                    }
                }
            `}</style>

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
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                        What Our Clients Say
                    </h2>
                    <p className="text-gray-400 max-w-xl mx-auto">
                        Feedbacks from some of our happy customers
                    </p>
                </div>
            </div>

            <div
                className="w-full"
                style={{
                    maskImage: "linear-gradient(to right, transparent, black 5%, black 95%, transparent)",
                    WebkitMaskImage: "linear-gradient(to right, transparent, black 5%, black 95%, transparent)",
                }}
            >
                <div className="testimonials-track flex gap-6 w-max px-4 sm:px-6">
                    {[...reviews, ...reviews].map((r, i) => (
                        <ReviewCard key={i} r={r} />
                    ))}
                </div>
            </div>
        </section>
    );
}
