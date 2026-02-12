export default function Features() {
    const features = [
        { title: "Workflow Automation", desc: "Build no-code automation pipelines easily." },
        { title: "CRM System", desc: "Track leads, manage customers & close deals faster." },
        { title: "WhatsApp Automation", desc: "Automate customer messaging & campaigns." },
        { title: "Analytics Dashboard", desc: "See insights and performance in real-time." },
        { title: "Integrations", desc: "Connect with payment gateways & tools." },
        { title: "Team Management", desc: "Collaborate with your team seamlessly." },
    ]

    return (
        <section id="features" className="py-24 bg-gray-50">
            <div className="max-w-7xl mx-auto px-6 text-center">
                <h2 className="text-4xl font-bold">
                    Powerful Features
                </h2>

                <div className="grid md:grid-cols-3 gap-10 mt-16">
                    {features.map((feature, index) => (
                        <div key={index} className="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition">
                            <div className="text-blue-600 text-4xl mb-4">⚡</div>
                            <h3 className="text-xl font-semibold">
                                {feature.title}
                            </h3>
                            <p className="mt-4 text-gray-600">
                                {feature.desc}
                            </p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    )
}
