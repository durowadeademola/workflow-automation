export default function Pricing() {
    const plans = [
        { name: "Starter", price: "₦25,000/mo", features: ["Basic Automation", "Email Support"] },
        { name: "Pro", price: "₦50,000/mo", features: ["CRM + WhatsApp", "Priority Support"] },
        { name: "Enterprise", price: "Custom", features: ["All Features", "Dedicated Manager"] },
    ]

    return (
        <section id="pricing" className="py-24 bg-gray-50">
            <div className="max-w-6xl mx-auto px-6 text-center">
                <h2 className="text-4xl font-bold">
                    Simple Pricing
                </h2>

                <div className="grid md:grid-cols-3 gap-8 mt-16">
                    {plans.map((plan, index) => (
                        <div key={index} className="bg-white p-8 rounded-2xl shadow">
                            <h3 className="text-xl font-bold">{plan.name}</h3>
                            <p className="text-3xl font-bold mt-4">{plan.price}</p>

                            <ul className="mt-6 space-y-2 text-gray-600">
                                {plan.features.map((f, i) => (
                                    <li key={i}>✔ {f}</li>
                                ))}
                            </ul>

                            <button className="mt-8 bg-blue-600 text-white px-6 py-3 rounded-xl w-full">
                                Choose Plan
                            </button>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    )
}
