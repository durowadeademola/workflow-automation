import { Head, Link } from "@inertiajs/react";
import { ShoppingCart, HelpCircle, Target, ClipboardList, UserCheck, BookOpen, BarChart3 } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Shoppers get instant answers on stock, pricing, and policies any time of day",
    "AI notices genuine buying intent and automatically flags it as a qualified lead",
    "New inquiries captured automatically, along with what the customer wants",
    "Handoff to a real person when the AI can't answer — WhatsApp fallback after hours",
];

const features = [
    { icon: HelpCircle, title: "Instant FAQ Answers", description: "Questions about stock, pricing, and store policies are answered instantly from your own FAQ list — with zero AI cost and zero wait." },
    { icon: Target, title: "Automatic Lead Qualification", description: "The AI notices genuine purchase intent during a conversation and flags it as a qualified lead automatically — no checklist, no interrogation." },
    { icon: ClipboardList, title: "Inquiry Capture", description: "When a shopper wants to leave their details, the AI collects their name, contact info, and the reason they're reaching out." },
    { icon: UserCheck, title: "Working-Hours-Aware Handoff", description: "The AI knows your real store hours — handing off to a real person when you're open, and gracefully pointing to WhatsApp when you're closed." },
    { icon: BookOpen, title: "Your Own Knowledge Base", description: "Add details about your products, shipping, and return policies so the AI answers with your actual information, not generic guesses." },
    { icon: BarChart3, title: "FAQ View Analytics", description: "See exactly what shoppers ask most, so you always know what to clarify or add." },
];

const steps = [
    { title: "Add the chat widget to your store's website", description: "Embed Blueflow's widget on your site and set your assistant's name, greeting, and real working hours." },
    { title: "Add your FAQs and product knowledge", description: "Enter your most common shopper questions — stock, pricing, shipping, returns — and any other background the AI should know." },
    { title: "The AI starts answering and qualifying leads", description: "Shoppers ask questions and the AI notices genuine buying intent automatically, any time of day." },
    { title: "Refine as you go", description: "Check which FAQs get the most views, add details for anything the AI couldn't answer, and adjust hours any time from your dashboard." },
];

const metrics = [
    { value: "24/7", label: "AI Availability" },
    { value: "0", label: "Forms To Fill Out" },
    { value: "Instant", label: "FAQ Answers" },
    { value: "Auto", label: "Lead Qualification" },
];

export default function Ecommerce() {
    return (
        <>
            <Head title="Automation for E-commerce & Retail">
                <meta
                    name="description"
                    content="Give shoppers instant answers and let the AI notice genuine buying intent automatically. Blueflow helps Nigerian online stores respond faster and never miss a lead."
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
                                    <span className="inline-block bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                                        E-commerce & Retail
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-5">
                                        Instant Answers. Automatic Lead Capture.
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Running an online store means answering the same stock, pricing, and
                                        policy questions all day, and losing track of who was actually serious
                                        about buying. Blueflow's AI chat widget answers those questions instantly,
                                        notices genuine buying intent automatically, and only hands off to a real
                                        person when a conversation genuinely needs one.
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
                                <SectionIllustration icon={ShoppingCart} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">
                                Built for Nigerian Online Stores
                            </h2>
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
                    <section className="py-20 bg-white dark:bg-gray-900">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">
                                Ready to Automate Your Store?
                            </h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-8">
                                Book a free demo and we'll show you exactly how Blueflow works for e-commerce businesses like yours.
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
