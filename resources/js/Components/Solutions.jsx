const solutions = [
    {
        icon: "✓",
        title: "Never Miss an Order",
        description: "24/7 AI handles unlimited customers simultaneously",
        metric: "100% capture rate",
    },
    {
        icon: "⚡",
        title: "Staff Focus on What Matters",
        description: "Automation handles routine tasks, staff handle customers",
        metric: "4 hours saved daily",
    },
    {
        icon: "📈",
        title: "Keep All Your Revenue",
        description: "Fixed monthly fee, no commission on orders",
        metric: "₦465k saved monthly",
    },
    {
        icon: "📊",
        title: "Know Your Customers",
        description: "Complete database with preferences, history, analytics",
        metric: "360° customer view",
    },
];

export default function Solutions() {
    return (
        <section className="py-20 bg-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center mb-14">
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        Blueflow Automates Everything
                    </h2>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {solutions.map((item) => (
                        <div
                            key={item.title}
                            className="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100 hover:shadow-lg transition-shadow group"
                        >
                            <div className="w-10 h-10 bg-green-600 text-white rounded-xl flex items-center justify-center text-lg font-bold mb-4 group-hover:scale-110 transition-transform">
                                {item.icon}
                            </div>
                            <h3 className="font-bold text-gray-900 mb-2 text-base leading-snug">
                                {item.title}
                            </h3>
                            <p className="text-sm text-gray-600 mb-4">{item.description}</p>
                            <span className="inline-block text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full">
                                {item.metric}
                            </span>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
