import { Link } from "@inertiajs/react";
import { useScrollAnimation } from "@/hooks/useScrollAnimation";

const badges = [
    { value: "7 Days", label: "Free Trial" },
    { value: "₦0", label: "Setup Fee" },
    { value: "24/7", label: "Support" },
];

export default function CTABanner() {
    const [ref, isVisible] = useScrollAnimation(0.2);

    return (
        <section className="py-20 bg-gradient-to-br from-blue-700 to-emerald-800 relative overflow-hidden">
            <div className="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full opacity-10 blur-3xl" />
            <div className="absolute bottom-0 left-0 w-72 h-72 bg-emerald-500 rounded-full opacity-10 blur-3xl" />

            <div
                ref={ref}
                className="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center"
            >
                <h2
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease, transform 0.6s ease",
                    }}
                    className="text-3xl sm:text-4xl font-extrabold text-white mb-4"
                >
                    Ready to Automate Your Business?
                </h2>
                <p
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease 100ms, transform 0.6s ease 100ms",
                    }}
                    className="text-blue-100 text-lg mb-10 max-w-2xl mx-auto"
                >
                    Join 100+ Nigerian businesses that have automated their customer communications and never looked back.
                </p>

                {/* Badges */}
                <div
                    className="flex flex-wrap items-center justify-center gap-6 mb-10"
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 200ms",
                    }}
                >
                    {badges.map((b, i) => (
                        <div
                            key={b.label}
                            className="text-center"
                            style={{
                                opacity: isVisible ? 1 : 0,
                                transform: isVisible ? "scale(1)" : "scale(0.8)",
                                transition: `opacity 0.5s ease ${200 + i * 100}ms, transform 0.5s ease ${200 + i * 100}ms`,
                            }}
                        >
                            <p className="text-3xl font-extrabold text-white">{b.value}</p>
                            <p className="text-xs text-blue-200 font-medium">{b.label}</p>
                        </div>
                    ))}
                </div>

                {/* CTA Buttons */}
                <div
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(20px)",
                        transition: "opacity 0.6s ease 350ms, transform 0.6s ease 350ms",
                    }}
                    className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-6"
                >
                    <Link
                        href="/contact"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold px-8 py-3.5 rounded-xl hover:bg-blue-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-0.5"
                    >
                        Schedule Free Demo
                    </Link>
                    <a
                        href="https://wa.me/2347064706193"
                        target="_blank"
                        rel="noreferrer"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 border-2 border-white text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-white/10 transition-all"
                    >
                        Talk to Our Team
                    </a>
                </div>

                <p
                    style={{ opacity: isVisible ? 1 : 0, transition: "opacity 0.6s ease 450ms" }}
                    className="text-xs text-blue-300"
                >
                    No credit card required • Cancel anytime • Free training included
                </p>

                {/* Testimonial */}
                <div
                    style={{
                        opacity: isVisible ? 1 : 0,
                        transform: isVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.7s ease 500ms, transform 0.7s ease 500ms",
                    }}
                    className="mt-12 bg-white/10 backdrop-blur-sm rounded-2xl p-6 max-w-2xl mx-auto border border-white/20"
                >
                    <p className="text-white text-sm italic leading-relaxed mb-4">
                        "Blueflow transformed our business overnight. We went from missing half our customer calls to capturing every single inquiry. The ROI was immediate and massive."
                    </p>
                    <div className="flex items-center justify-center gap-3">
                        <div className="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                            C
                        </div>
                        <div className="text-left">
                            <p className="text-white font-semibold text-sm">Babatunde Adebayo</p>
                            <p className="text-blue-300 text-xs">Owner, Mama's Kitchen (Lagos)</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
