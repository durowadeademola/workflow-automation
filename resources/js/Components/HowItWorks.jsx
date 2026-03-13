import { Link } from "@inertiajs/react";

const steps = [
    {
        number: "01",
        title: "Discovery Call",
        description: "We understand your business, challenges, and goals. No technical jargon, just real talk.",
        duration: "30 minutes",
        outcome: "Clear understanding of your needs",
    },
    {
        number: "02",
        title: "Solution Design",
        description: "We create a custom automation plan tailored to your business processes and workflows.",
        duration: "2-3 days",
        outcome: "Custom automation greenprint",
    },
    {
        number: "03",
        title: "Development & Setup",
        description: "Our team builds and configures your automation system. We handle all the technical heavy lifting.",
        duration: "3-5 days",
        outcome: "Fully configured system",
    },
    {
        number: "04",
        title: "Training & Launch",
        description: "We train your team and launch your automation. You will be an expert before we are done.",
        duration: "1 day",
        outcome: "Live system + trained team",
    },
    {
        number: "05",
        title: "Ongoing Support",
        description: "We are always here to help. Updates, optimizations, and 24/7 support included.",
        duration: "Forever",
        outcome: "Peace of mind",
    },
];

export default function HowItWorks() {
    return (
        <section id="how-it-works" className="py-20 bg-gray-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center mb-14">
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                        How It Works
                    </h2>
                    <p className="text-gray-500 max-w-xl mx-auto">
                        From first call to full automation in just 7-10 days. Here's our proven process.
                    </p>
                </div>

                <div className="relative">
                    {/* Vertical connector line (desktop) */}
                    <div className="hidden md:block absolute left-1/2 top-0 bottom-0 w-0.5 bg-green-100 -translate-x-1/2 z-0" />

                    <div className="space-y-8 relative z-10">
                        {steps.map((step, idx) => (
                            <div
                                key={step.number}
                                className={`flex flex-col md:flex-row items-center gap-6 md:gap-12 ${
                                    idx % 2 === 1 ? "md:flex-row-reverse" : ""
                                }`}
                            >
                                {/* Card */}
                                <div className="flex-1 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow w-full">
                                    <h3 className="font-bold text-gray-900 text-lg mb-2">{step.title}</h3>
                                    <p className="text-gray-500 text-sm mb-4">{step.description}</p>
                                    <div className="flex items-center gap-4 text-xs">
                                        <span className="flex items-center gap-1.5 text-gray-400">
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Duration: {step.duration}
                                        </span>
                                        <span className="flex items-center gap-1.5 text-green-600 font-medium">
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                            </svg>
                                            You get: {step.outcome}
                                        </span>
                                    </div>
                                </div>

                                {/* Step number badge */}
                                <div className="flex-shrink-0 w-14 h-14 bg-green-600 text-white rounded-full flex items-center justify-center font-extrabold text-lg shadow-lg shadow-green-200 z-10">
                                    {step.number}
                                </div>

                                {/* Empty spacer for alternating layout */}
                                <div className="flex-1 hidden md:block" />
                            </div>
                        ))}
                    </div>
                </div>

                {/* CTA below steps */}
                <div className="mt-16 bg-green-600 rounded-3xl p-8 md:p-12 text-center text-white">
                    <h3 className="text-2xl font-extrabold mb-3">Ready to Get Started?</h3>
                    <p className="text-green-100 mb-8 max-w-lg mx-auto">
                        Book a free 30-minute discovery call. No commitment, no sales pressure.
                        Just honest advice on how automation can help your business.
                    </p>
                    <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            href="/demo"
                            className="bg-white text-green-700 font-semibold px-6 py-3 rounded-xl hover:bg-green-50 transition-colors"
                        >
                            Schedule Discovery Call
                        </Link>
                        <Link
                            href="/contact"
                            className="border-2 border-white text-white font-semibold px-6 py-3 rounded-xl hover:bg-green-700 transition-colors"
                        >
                            Ask a Question
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    );
}
