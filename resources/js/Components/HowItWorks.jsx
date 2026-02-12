export default function HowItWorks() {
    const steps = [
        "Sign up and connect your tools",
        "Create your automation workflows",
        "Launch and monitor performance"
    ]

    return (
        <section id="how" className="py-24">
            <div className="max-w-5xl mx-auto px-6 text-center">
                <h2 className="text-4xl font-bold">
                    How BlueFlow Works
                </h2>

                <div className="mt-16 grid md:grid-cols-3 gap-8">
                    {steps.map((step, index) => (
                        <div key={index} className="p-6 border rounded-2xl">
                            <div className="text-3xl font-bold text-blue-600 mb-4">
                                {index + 1}
                            </div>
                            <p>{step}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    )
}
