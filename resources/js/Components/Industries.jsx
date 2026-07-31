import { useState } from "react";
import { Link } from "@inertiajs/react";
import { useScrollAnimation, useCountUp } from "@/hooks/useScrollAnimation";

const industries = [
    {
        id: "healthcare",
        label: "Healthcare & Clinics",
        title: "Healthcare & Clinics",
        description: "Let patients book appointments and get answers instantly, day or night — with a real staff member only when it truly needs one.",
        features: [
            "AI books appointments straight from a conversation — no forms",
            "Instant answers to common questions from your own FAQ list",
            "Patient contact details captured automatically for callbacks",
            "Handoff to staff during working hours, WhatsApp fallback outside them",
        ],
        metric: { value: 24, suffix: "/7", label: "Patient Q&A Coverage" },
        metricNote: "The AI answers and books appointments even outside clinic hours",
        href: "/industries/healthcare",
    },
      {
        id: "real-estate",
        label: "Real Estate",
        title: "Real Estate",
        description: "Let the AI notice genuine buyer interest automatically and never lose a lead to a slow reply.",
        features: [
            "AI notices genuine interest and captures qualified leads automatically",
            "Instant answers to common listing and process questions",
            "Visitor contact details captured for follow-up",
            "Handoff to an agent when a conversation needs a real person",
        ],
        metric: { value: 24, suffix: "/7", label: "Lead Capture Coverage" },
        metricNote: "The AI keeps qualifying leads even when no agent is online",
        href: "/industries/real-estate",
    },
    {
        id: "ecommerce",
        label: "E-commerce & Retail",
        title: "E-commerce & Retail",
        description: "Give shoppers instant answers on stock, pricing, and policies, and let the AI notice genuine buying intent automatically.",
        features: [
            "Instant answers from your FAQ list — no waiting on a reply",
            "AI notices genuine purchase intent and flags qualified leads",
            "Visitor contact details captured for follow-up",
            "Human handoff when the AI can't answer, WhatsApp fallback after hours",
        ],
        metric: { value: 24, suffix: "/7", label: "Shopper Q&A Coverage" },
        metricNote: "Instant FAQ answers with zero wait, any time of day",
        href: "/industries/ecommerce",
    },
    {
        id: "hotels",
        label: "Hotels & Hospitality",
        title: "Hotels & Hospitality",
        description: "Handle booking questions and requests around the clock, with human handoff exactly when a guest truly needs one.",
        features: [
            "AI answers common guest questions instantly from your FAQ list",
            "Booking requests collected through natural conversation",
            "Guest contact details captured for callbacks",
            "Handoff to staff during working hours, WhatsApp fallback outside them",
        ],
        metric: { value: 24, suffix: "/7", label: "Guest Q&A Coverage" },
        metricNote: "The AI keeps answering guest questions after the front desk closes",
        href: "/industries/hospitality",
    },
      {
        id: "restaurants",
        label: "Restaurants & Cafés",
        title: "Restaurants & Cafés",
        description: "Answer menu and hours questions instantly, and let the AI collect bookings and contact details while you focus on the kitchen.",
        features: [
            "Instant FAQ answers for menu, hours, and location questions",
            "AI collects booking requests through natural conversation",
            "Visitor details captured when they want to be contacted back",
            "Handoff to staff during service hours, WhatsApp link when closed",
        ],
        metric: { value: 24, suffix: "/7", label: "Booking & Q&A Coverage" },
        metricNote: "The AI keeps answering and taking booking requests after hours",
        href: "/industries/restaurants",
    },
    {
        id: "professional-services",
        label: "Professional Services",
        title: "Professional Services",
        description: "Let the AI answer routine client questions and capture new inquiries, so your team's time goes to billable work.",
        features: [
            "Instant answers to common client questions from your FAQ list",
            "New inquiries captured along with their reason for reaching out",
            "AI books consultation appointments through natural conversation",
            "Handoff to your team when a question needs a real person",
        ],
        metric: { value: 24, suffix: "/7", label: "Client Q&A Coverage" },
        metricNote: "The AI keeps answering client questions outside office hours",
        href: "/industries/professional-services",
    },
];

function MetricCard({ value, suffix, label, note, trigger }) {
    const count = useCountUp(value, 1600, 0, trigger);

    return (
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-10 text-center max-w-xs w-full">
            <p className="text-6xl font-extrabold text-blue-600 mb-2">
                {count}{suffix}
            </p>
            <p className="text-xl font-bold text-gray-900 dark:text-white mb-3">{label}</p>
            <p className="text-sm text-gray-500 dark:text-gray-400">{note}</p>
        </div>
    );
}

export default function Industries() {
    const [active, setActive] = useState("healthcare");
    const industry = industries.find((i) => i.id === active);

    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [contentRef, contentVisible] = useScrollAnimation(0.1);

    return (
        <section className="py-20 bg-white dark:bg-gray-900">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    ref={headingRef}
                    className="text-center mb-12"
                    style={{
                        opacity: headingVisible ? 1 : 0,
                        transform: headingVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease, transform 0.6s ease",
                    }}
                >
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Built for Your Industry
                    </h2>
                    <p className="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        Pre-built solutions designed specifically for Nigerian businesses in your sector
                    </p>
                </div>

                {/* Tabs */}
                <div
                    className="flex flex-wrap justify-center gap-2 mb-10"
                    style={{
                        opacity: headingVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 200ms",
                    }}
                >
                    {industries.map((ind) => (
                        <button
                            key={ind.id}
                            onClick={() => setActive(ind.id)}
                            className={`text-sm font-medium px-4 py-2 rounded-full transition-all ${
                                active === ind.id
                                    ? "bg-blue-600 text-white shadow-md"
                                    : "bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-blue-700 dark:hover:text-blue-300"
                            }`}
                        >
                            {ind.label}
                        </button>
                    ))}
                </div>

                {/* Tab Content */}
                <div
                    ref={contentRef}
                    className="bg-gradient-to-br from-blue-50 dark:from-blue-900/40 to-emerald-50 dark:to-emerald-900/40 rounded-3xl p-8 md:p-12"
                    style={{
                        opacity: contentVisible ? 1 : 0,
                        transform: contentVisible ? "translateY(0)" : "translateY(40px)",
                        transition: "opacity 0.7s ease 100ms, transform 0.7s ease 100ms",
                    }}
                >
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                        <div>
                            <h3 className="text-2xl font-extrabold text-gray-900 dark:text-white mb-3">{industry.title}</h3>
                            <p className="text-gray-600 dark:text-gray-300 mb-6">{industry.description}</p>
                            <h4 className="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                                What You Get:
                            </h4>
                            <ul className="space-y-3 mb-8">
                                {industry.features.map((f) => (
                                    <li key={f} className="flex items-center gap-3 text-gray-700 dark:text-gray-200">
                                        <span className="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                        {f}
                                    </li>
                                ))}
                            </ul>
                            <Link
                                href={industry.href}
                                className="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors"
                            >
                                See {industry.title} Solutions
                            </Link>
                        </div>

                        <div className="flex items-center justify-center">
                            <MetricCard
                                value={industry.metric.value}
                                suffix={industry.metric.suffix}
                                label={industry.metric.label}
                                note={industry.metricNote}
                                trigger={contentVisible}
                            />
                        </div>
                    </div>
                </div>

                <p
                    className="text-center text-gray-500 dark:text-gray-400 mt-6 text-sm"
                    style={{
                        opacity: contentVisible ? 1 : 0,
                        transition: "opacity 0.6s ease 400ms",
                    }}
                >
                    Don't see your industry? We've got you covered.{" "}
                    <Link href="/industries" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                        View All Industries
                    </Link>
                </p>
            </div>
        </section>
    );
}
