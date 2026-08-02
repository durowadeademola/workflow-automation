import { Head, Link } from "@inertiajs/react";
import { Megaphone, Target, FlaskConical, BarChart3, Workflow, Send } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Automated campaigns that reach customers by email, WhatsApp, or phone — whichever fits the moment",
    "Segment your audience by behaviour, not just a static list, so every message is relevant",
    "A/B test subject lines, content, and send times to keep improving results",
    "One dashboard to see what's actually working — opens, clicks, and outcomes",
];

const features = [
    { icon: Workflow, title: "Campaign Automation", description: "Build a sequence once — a welcome series, a re-engagement drip, a follow-up flow — and let it run automatically from then on." },
    { icon: Target, title: "Segmentation", description: "Group contacts by what they've actually done — registered but didn't book, viewed pricing, went quiet — and speak to each group differently." },
    { icon: FlaskConical, title: "A/B Testing", description: "Test two versions of a message and automatically learn which performs better, instead of guessing." },
    { icon: BarChart3, title: "Analytics", description: "Track opens, clicks, and conversions per campaign, so you know exactly what's driving results." },
    { icon: Send, title: "Channel-Flexible Delivery", description: "Runs on email, WhatsApp, or phone — whichever channel reaches your customers best. No separate subscription required to any single channel." },
    { icon: Megaphone, title: "Built for Local Business", description: "Campaign templates and timing designed around how Nigerian businesses actually reach customers." },
];

const steps = [
    { title: "Import or connect your contacts", description: "Bring in your existing customer list, or let it grow automatically from your chat widget's leads and registrations." },
    { title: "Build your segments", description: "Group contacts by behaviour or status — qualified leads, past customers, no-shows — whatever matters to your business." },
    { title: "Set up your campaigns", description: "Create automated sequences for the moments that matter — welcome, follow-up, re-engagement — once, and they keep running." },
    { title: "Test, track, and improve", description: "Watch the analytics, A/B test what isn't working, and let each campaign get better over time." },
];

const metrics = [
    { value: "24/7", label: "Campaigns Running" },
    { value: "Auto", label: "Segmentation" },
    { value: "A/B", label: "Continuous Testing" },
    { value: "Live", label: "Performance Analytics" },
];

export default function MarketingAutomation() {
    return (
        <>
            <Head title="Marketing Automation - Blueflow Automation">
                <meta
                    name="description"
                    content="Automated marketing campaigns across email, WhatsApp, and phone — with segmentation, A/B testing, and analytics built in. Built for Nigerian businesses."
                />
            </Head>
            <div className="min-h-screen bg-white dark:bg-gray-900">
                <Navbar />
                <main>
                    {/* Overview */}
                    <section className="py-20 bg-white dark:bg-gray-900">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                                <div>
                                    <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-5">
                                        Turn Interest Into Customers, Automatically
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Most businesses either message everyone the same thing, or don't follow up
                                        at all. Blueflow's Marketing Automation runs targeted campaigns automatically
                                        — reaching the right customer, on the right channel, at the right moment —
                                        so no interested lead gets forgotten.
                                    </p>
                                    <ul className="space-y-3">
                                        {highlights.map((h) => (
                                            <li key={h} className="flex items-start gap-3 text-gray-700 dark:text-gray-200 text-sm">
                                                <span className="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </span>
                                                {h}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <SectionIllustration icon={Megaphone} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">Everything Included</h2>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                {features.map((f) => (
                                    <div key={f.title} className="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                                        <IconTile icon={f.icon} color="blue" size="md" className="mb-3" />
                                        <h3 className="font-bold text-gray-900 dark:text-white mb-2">{f.title}</h3>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">{f.description}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* How it works */}
                    <section className="py-20 bg-white dark:bg-gray-900">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">How It Works</h2>
                            <div className="space-y-6">
                                {steps.map((step, i) => (
                                    <div key={step.title} className="flex items-start gap-5">
                                        <div className="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            {i + 1}
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-gray-900 dark:text-white mb-1">{step.title}</h3>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{step.description}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Metrics */}
                    <section className="py-16 bg-blue-600">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-6">
                                {metrics.map((m) => (
                                    <div key={m.label} className="text-center">
                                        <p className="text-3xl font-extrabold text-white mb-1">{m.value}</p>
                                        <p className="text-xs text-blue-100">{m.label}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* CTA */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Ready to Automate Your Campaigns?</h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-8">
                                Book a free demo and see how Marketing Automation can turn your leads into loyal customers.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contact" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Schedule Free Demo
                                </Link>
                                <Link href="/#pricing" className="border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-semibold px-6 py-3 rounded-xl hover:border-blue-500 hover:text-blue-700 dark:hover:text-blue-300 transition-all">
                                    View Pricing
                                </Link>
                            </div>
                        </div>
                    </section>
                </main>
                <Footer />
            </div>
        </>
    );
}
