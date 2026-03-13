const painPoints = [
    {
        icon: "📞",
        title: "Missing 40-60% of customer calls",
        description: "Customers can't reach you during busy hours",
    },
    {
        icon: "⏰",
        title: "Staff wasting 4-5 hours daily",
        description: "Answering repetitive questions instead of serving customers",
    },
    {
        icon: "💸",
        title: "Losing ₦500k+ monthly",
        description: "Paying high commissions to third-party platforms",
    },
    {
        icon: "📊",
        title: "Zero customer insights",
        description: "No data about your best customers or their preferences",
    },
];

export default function PainPoints() {
    return (
        <section className="py-20 bg-gray-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center mb-14">
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        Running a Business Shouldn't Be This Hard
                    </h2>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {painPoints.map((item) => (
                        <div
                            key={item.title}
                            className="bg-white rounded-2xl p-6 border border-red-100 shadow-sm hover:shadow-md transition-shadow"
                        >
                            <div className="text-3xl mb-4">{item.icon}</div>
                            <h3 className="font-bold text-gray-900 mb-2 text-base leading-snug">
                                {item.title}
                            </h3>
                            <p className="text-sm text-gray-500">{item.description}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
