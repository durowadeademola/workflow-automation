import { Head, Link } from "@inertiajs/react";
import { Zap, Target, Mail, Repeat, BarChart3, Link2 } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Send the right email to the right customer at exactly the right time — automatically",
    "Personalised campaigns powered by customer behaviour, not guesswork",
    "Drag-and-drop email builder with mobile-optimised templates",
    "Full analytics: opens, clicks, revenue generated, and unsubscribes in real time",
];

const features = [
    { icon: Zap, title: "Triggered Sequences", description: "Automatically send emails when customers sign up, abandon a cart, make a purchase, or go quiet for too long." },
    { icon: Target, title: "Audience Segmentation", description: "Group customers by behaviour, location, purchase history, or any custom field — and speak to each group differently." },
    { icon: Mail, title: "Template Builder", description: "Professional, mobile-ready email templates built for Nigerian businesses. No design skills required." },
    { icon: Repeat, title: "Drip Campaigns", description: "Nurture leads over days or weeks with automated sequences that move people from curious to paying customer." },
    { icon: BarChart3, title: "Revenue Attribution", description: "See exactly which emails are generating sales. Know your open rate, click rate, and naira earned per campaign." },
    { icon: Link2, title: "CRM & WhatsApp Sync", description: "Email data flows directly into your CRM and can trigger WhatsApp follow-ups for maximum reach." },
];

const steps = [
    { title: "Import your contact list", description: "Bring in your existing customers from spreadsheets, your CRM, or your e-commerce platform in minutes." },
    { title: "Segment your audience", description: "We help you group contacts by what matters — purchase history, industry, location, or engagement level." },
    { title: "Design your sequences", description: "Our team builds your welcome series, nurture flows, and promotional campaigns — copy, design, and logic included." },
    { title: "Launch, track, and optimise", description: "Go live and watch the dashboard. We A/B test subject lines and send times to continuously improve results." },
];

const metrics = [
    { value: "45%", label: "Avg. Open Rate" },
    { value: "5x", label: "ROI on Campaigns" },
    { value: "80%", label: "Less Manual Sending" },
    { value: "24/7", label: "Emails Going Out" },
];

export default function EmailAutomation() {
    return (
        <>
            <Head title="Email Automation - Blueflow Automation">
                <meta
                    name="description"
                    content="Send smarter emails that convert. Blueflow's Email Automation handles your campaigns, follow-ups, and sequences — built for Nigerian businesses."
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
                                        Email That Works While You Sleep
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Most businesses send the same bulk email to everyone and wonder why nobody
                                        buys. Blueflow's Email Automation is different — it sends personalised,
                                        behaviour-triggered emails that land at the perfect moment. From welcoming
                                        new leads to re-engaging customers who've gone quiet, every email is
                                        automated, targeted, and tracked.
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
                                <SectionIllustration icon={Mail} accent="blue" />
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
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Ready to Send Smarter Emails?</h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-8">
                                Book a free demo and see how Email Automation can turn your contact list into consistent revenue.
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
