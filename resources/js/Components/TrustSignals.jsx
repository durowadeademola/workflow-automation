import { ShieldCheck, MapPin, CreditCard, Zap, Globe, Trophy, BarChart3, Handshake } from "lucide-react";
import { useScrollAnimation } from "@/hooks/useScrollAnimation";
import IconTile from "@/Components/Icons/IconTile";

const trustItems = [
    {
        icon: ShieldCheck,
        title: "Bank-Level Security",
        description: "Your data is encrypted with 256-bit SSL. We take security as seriously as Nigerian banks do.",
    },
    {
        icon: MapPin,
        title: "Local Support Team",
        description: "Real Nigerians based in Lagos, Abuja, and Port Harcourt. We understand your business.",
    },
    {
        icon: CreditCard,
        title: "Nigerian Payment Options",
        description: "Pay with bank transfer, Paystack, Flutterwave, or POS. No dollar accounts required.",
    },
    {
        icon: Zap,
        title: "24/7 Availability",
        description: "Your automation never sleeps. 99.5% uptime guaranteed with instant failover.",
    },
    {
        icon: Globe,
        title: "Made for Nigeria",
        description: "Built specifically for Nigerian businesses. We understand the unique challenges you face.",
    },
    {
        icon: Trophy,
        title: "100+ Happy Businesses",
        description: "From small shops to large enterprises, businesses across Nigeria trust us daily.",
    },
    {
        icon: BarChart3,
        title: "Transparent Reporting",
        description: "See exactly what your automation is doing with real-time analytics and reports.",
    },
    {
        icon: Handshake,
        title: "No Long-Term Contracts",
        description: "Cancel anytime. No penalties. No questions asked. We earn your business every month.",
    },
];

const industries = ["Restaurants", "E-commerce", "Hotels", "Healthcare", "Real Estate", "Professional Services"];

export default function TrustSignals() {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [cardsRef, cardsVisible] = useScrollAnimation(0.05);

    return (
        <section className="py-20 bg-gray-50">
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
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        Why Nigerian Businesses Trust Us
                    </h2>
                    <p className="text-gray-500 max-w-xl mx-auto">
                        We are not just a automation company. We are your technology partner committed to your success.
                    </p>
                </div>

                <div ref={cardsRef} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    {trustItems.map((item, i) => (
                        <div
                            key={item.title}
                            className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow hover:-translate-y-0.5"
                            style={{
                                opacity: cardsVisible ? 1 : 0,
                                transform: cardsVisible ? "translateY(0)" : "translateY(50px)",
                                transition: `opacity 0.6s ease ${i * 80}ms, transform 0.6s ease ${i * 80}ms`,
                            }}
                        >
                            <IconTile icon={item.icon} color="blue" size="md" className="mb-3" />
                            <h3 className="font-bold text-gray-900 text-sm mb-2">{item.title}</h3>
                            <p className="text-xs text-gray-500 leading-relaxed">{item.description}</p>
                        </div>
                    ))}
                </div>

                {/* Industries ticker */}
                <div
                    className="text-center"
                    style={{
                        opacity: cardsVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 700ms",
                    }}
                >
                    <p className="text-sm font-medium text-gray-400 mb-4">Trusted by leading Nigerian businesses:</p>
                    <div className="flex flex-wrap justify-center gap-3">
                        {industries.map((ind, i) => (
                            <span key={ind} className="flex items-center gap-2 text-sm text-gray-500">
                                {ind}
                                {i < industries.length - 1 && <span className="text-gray-300">•</span>}
                            </span>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
