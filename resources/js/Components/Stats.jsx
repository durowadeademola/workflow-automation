import { useScrollAnimation, useCountUp } from "@/hooks/useScrollAnimation";

const stats = [
    { value: 100, suffix: "+", label: "Businesses Automated", prefix: "" },
    { value: 500, suffix: "K+", label: "Messages Automated/Month", prefix: "₦" },
    { value: 99.5, suffix: "%", label: "Uptime Guarantee", isDecimal: true, prefix: "" },
    { value: 4.9, suffix: "★", label: "Average Client Rating", isDecimal: true, prefix: "" },
];

function StatItem({ value, suffix, label, prefix, isDecimal, delay, trigger }) {
    const count = useCountUp(isDecimal ? Math.round(value * 10) : value, 2000, 0, trigger);
    const display = isDecimal ? (count / 10).toFixed(1) : count;

    return (
        <div
            className="text-center"
            style={{
                opacity: trigger ? 1 : 0,
                transform: trigger ? "translateY(0)" : "translateY(40px)",
                transition: `opacity 0.7s ease ${delay}ms, transform 0.7s ease ${delay}ms`,
            }}
        >
            <p className="text-4xl sm:text-5xl font-extrabold text-blue-600 mb-2">
                {prefix}{display}{suffix}
            </p>
            <p className="text-sm text-gray-500 font-medium">{label}</p>
        </div>
    );
}

export default function Stats() {
    const [ref, isVisible] = useScrollAnimation(0.2);

    return (
        <section ref={ref} className="py-16 bg-white border-y border-gray-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-10 divide-x divide-gray-100">
                    {stats.map((stat, i) => (
                        <StatItem
                            key={stat.label}
                            {...stat}
                            delay={i * 150}
                            trigger={isVisible}
                        />
                    ))}
                </div>
            </div>
        </section>
    );
}
