import { Head, Link } from "@inertiajs/react";
import { MapPin, Handshake, Settings2 } from "lucide-react";
import MainLayout from "@/Components/Layout/MainLayout";
import PageHero from "@/Components/Layout/PageHero";
import IconTile from "@/Components/Icons/IconTile";

const values = [
    {
        icon: MapPin,
        title: "Built for Nigerian Businesses",
        description: "Every workflow we build accounts for how Nigerian businesses actually operate — WhatsApp-first, cash and transfer payments, and customers who expect a fast reply.",
    },
    {
        icon: Handshake,
        title: "No Long-Term Lock-In",
        description: "We earn your business every month. No aggressive contracts, no hidden fees — just automation that keeps proving its worth.",
    },
    {
        icon: Settings2,
        title: "Practical Automation, Not Hype",
        description: "We focus on automation that solves a real, measurable problem — missed orders, slow follow-ups, manual data entry — not automation for its own sake.",
    },
];

export default function About() {
    return (
        <MainLayout>
            <Head title="About Us — Blueflow Automation">
                <meta
                    name="description"
                    content="Blueflow Automation is a Nigerian automation agency based in Benin City, helping businesses automate WhatsApp, CRM, payments, and workflows with AI and n8n."
                />
            </Head>

            <PageHero
                badge="About Blueflow"
                title="We Help Nigerian Businesses Automate the Busywork"
                subtitle="Blueflow Automation is a Nigerian automation agency based in Benin City. We build AI-powered WhatsApp assistants, CRM integrations, and custom workflow automation for businesses across Nigeria."
            />

            <section className="py-20 bg-white dark:bg-gray-900">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 className="text-2xl font-extrabold text-gray-900 dark:text-white mb-5">Our Story</h2>
                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Blueflow Automation started with a simple observation: most Nigerian businesses lose money
                        every day to missed calls, slow follow-ups, and manual work that a well-configured system
                        could handle instantly. We built Blueflow to close that gap — combining WhatsApp automation,
                        n8n workflows, and AI to give small and mid-sized businesses the same operational efficiency
                        that larger companies pay much more for.
                    </p>
                </div>
            </section>

            <section className="py-20 bg-gray-50 dark:bg-gray-800">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 className="text-2xl font-extrabold text-gray-900 dark:text-white text-center mb-14">What We Believe</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        {values.map((v) => (
                            <div key={v.title} className="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm">
                                <IconTile icon={v.icon} color="blue" size="lg" className="mb-4" />
                                <h3 className="font-bold text-gray-900 dark:text-white mb-2">{v.title}</h3>
                                <p className="text-sm text-gray-500 dark:text-gray-400">{v.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            <section className="py-20 bg-white dark:bg-gray-900">
                <div className="max-w-3xl mx-auto px-4 text-center">
                    <h2 className="text-2xl font-extrabold text-gray-900 dark:text-white mb-4">Want to Work With Us?</h2>
                    <p className="text-gray-500 dark:text-gray-400 mb-8">
                        Tell us about your business and what you'd like to automate — we'll take it from there.
                    </p>
                    <Link
                        href="/contact"
                        className="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors"
                    >
                        Get in Touch
                    </Link>
                </div>
            </section>
        </MainLayout>
    );
}
