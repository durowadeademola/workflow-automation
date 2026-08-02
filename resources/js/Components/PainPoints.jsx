import { PhoneMissed, Clock, Banknote, ChartColumnDecreasing } from "lucide-react";
import { useScrollAnimation } from "@/hooks/useScrollAnimation";
import IconTile from "@/Components/Icons/IconTile";

const painPoints = [
    {
        icon: PhoneMissed,
        title: "Missing 40-60% of customer calls or messages",
        description: "Customers can't reach you during busy hours",
    },
    {
        icon: Clock,
        title: "Staff wasting 4-5 hours daily",
        description: "Answering repetitive questions instead of serving customers",
    },
    {
        icon: Banknote,
        title: "Losing ₦500k+ monthly",
        description: "Paying high commissions to third-party platforms",
    },
    {
        icon: ChartColumnDecreasing,
        title: "Zero customer insights",
        description: "No data about your best customers or their preferences",
    },
];

export default function PainPoints() {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [cardsRef, cardsVisible] = useScrollAnimation(0.15);

    return (
        <section className="py-20 bg-gray-50 dark:bg-gray-800">
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
                        Running a Business Shouldn't Be This Hard
                    </h2>
                </div>

                <div ref={cardsRef} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {painPoints.map((item, i) => (
                        <div
                            key={item.title}
                            className="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-red-100 dark:border-red-900/50 shadow-sm hover:shadow-md transition-shadow"
                            style={{
                                opacity: cardsVisible ? 1 : 0,
                                transform: cardsVisible ? "translateY(0)" : "translateY(50px)",
                                transition: `opacity 0.6s ease ${i * 120}ms, transform 0.6s ease ${i * 120}ms`,
                            }}
                        >
                            <IconTile icon={item.icon} color="red" size="lg" className="mb-4" />
                            <h3 className="font-bold text-gray-900 dark:text-white mb-2 text-base leading-snug">
                                {item.title}
                            </h3>
                            <p className="text-sm text-gray-500 dark:text-gray-400">{item.description}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
