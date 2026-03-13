import { Link } from "@inertiajs/react";

const badges = [
    { value: "7 Days", label: "Free Trial" },
    { value: "₦0", label: "Setup Fee" },
    { value: "24/7", label: "Support" },
];

export default function CTABanner() {
    return (
        <section className="py-20 bg-gradient-to-br from-green-700 to-emerald-800 relative overflow-hidden">
            {/* Decorative background */}
            <div className="absolute top-0 right-0 w-96 h-96 bg-green-500 rounded-full opacity-10 blur-3xl" />
            <div className="absolute bottom-0 left-0 w-72 h-72 bg-emerald-500 rounded-full opacity-10 blur-3xl" />

            <div className="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 className="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                    Ready to Automate Your Business?
                </h2>
                <p className="text-green-100 text-lg mb-10 max-w-2xl mx-auto">
                    Join 100+ Nigerian businesses that have automated their customer communications and never looked back.
                </p>

                {/* Badges */}
                <div className="flex flex-wrap items-center justify-center gap-6 mb-10">
                    {badges.map((b) => (
                        <div key={b.label} className="text-center">
                            <p className="text-3xl font-extrabold text-white">{b.value}</p>
                            <p className="text-xs text-green-200 font-medium">{b.label}</p>
                        </div>
                    ))}
                </div>

                {/* CTA Buttons */}
                <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-6">
                    <Link
                        href="/demo"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-green-700 font-bold px-8 py-3.5 rounded-xl hover:bg-green-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-0.5"
                    >
                        Schedule Free Demo
                    </Link>
                    <Link
                        href="/contact"
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 border-2 border-white text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-white/10 transition-all"
                    >
                        Talk to Our Team
                    </Link>
                </div>

                <p className="text-xs text-green-300">
                    No credit card required • Cancel anytime • Free training included
                </p>

                {/* Testimonial */}
                <div className="mt-12 bg-white/10 backdrop-blur-sm rounded-2xl p-6 max-w-2xl mx-auto border border-white/20">
                    <p className="text-white text-sm italic leading-relaxed mb-4">
                        "greenflow transformed our business overnight. We went from missing half our customer calls to capturing every single inquiry. The ROI was immediate and massive."
                    </p>
                    <div className="flex items-center justify-center gap-3">
                        <div className="w-9 h-9 bg-green-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                            C
                        </div>
                        <div className="text-left">
                            <p className="text-white font-semibold text-sm">Chinedu Okafor</p>
                            <p className="text-green-300 text-xs">Owner, Mama's Kitchen (Lagos)</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
