import { CircleCheckBig, Zap, TrendingUp, Users } from "lucide-react";
import { useScrollAnimation } from "@/hooks/useScrollAnimation";

const solutions = [
    {
        icon: CircleCheckBig,
        title: "Never Miss an Order",
        description: "24/7 AI handles unlimited customers simultaneously",
        metric: "100% capture rate",
    },
    {
        icon: Zap,
        title: "Staff Focus on What Matters",
        description: "Automation handles routine tasks, staff handle customers",
        metric: "4 hours saved daily",
    },
    {
        icon: TrendingUp,
        title: "Keep All Your Revenue",
        description: "Fixed monthly fee, no commission on orders",
        metric: "₦465k saved monthly",
    },
    {
        icon: Users,
        title: "Know Your Customers",
        description: "Complete database with preferences, history, analytics",
        metric: "360° customer view",
    },
];

export default function Solutions() {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [cardsRef, cardsVisible] = useScrollAnimation(0.1);

    return (
        <section className="py-20 bg-white">
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
                        Blueflow Automates Everything
                    </h2>
                </div>

                <div ref={cardsRef} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {solutions.map((item, i) => (
                        <div
                            key={item.title}
                            className="bg-gradient-to-br from-blue-50 to-emerald-50 rounded-2xl p-6 border border-blue-100 hover:shadow-lg transition-shadow group"
                            style={{
                                opacity: cardsVisible ? 1 : 0,
                                transform: cardsVisible ? "translateY(0)" : "translateY(60px)",
                                transition: `opacity 0.65s ease ${i * 130}ms, transform 0.65s ease ${i * 130}ms`,
                            }}
                        >
                            <div className="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <item.icon className="w-5 h-5" strokeWidth={2.25} />
                            </div>
                            <h3 className="font-bold text-gray-900 mb-2 text-base leading-snug">
                                {item.title}
                            </h3>
                            <p className="text-sm text-gray-600 mb-4">{item.description}</p>
                            <span className="inline-block text-xs font-semibold text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                                {item.metric}
                            </span>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
