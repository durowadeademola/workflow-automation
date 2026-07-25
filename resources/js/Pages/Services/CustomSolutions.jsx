import { Head, Link } from "@inertiajs/react";
import { Blocks, Plug, BrainCircuit, Smartphone, ShieldCheck, RefreshCw } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Built from scratch around your exact business process — not a generic template",
    "Dedicated automation engineer assigned to your project from day one",
    "Integrates with any tool, platform, or legacy system your business already uses",
    "Ongoing support and iterations as your business grows and requirements change",
];

const features = [
    { icon: Blocks, title: "Bespoke Development", description: "When off-the-shelf solutions don't fit, we build exactly what you need — custom logic, custom integrations, custom interfaces." },
    { icon: Plug, title: "Legacy System Integration", description: "Already running on older software? We connect your existing systems to modern automation tools without replacing what works." },
    { icon: BrainCircuit, title: "AI-Powered Features", description: "Add AI capabilities to your operations — document processing, intelligent routing, predictive analytics, or custom chatbots." },
    { icon: Smartphone, title: "Custom Dashboards", description: "Purpose-built reporting dashboards that show the exact metrics your leadership team needs — nothing more, nothing less." },
    { icon: ShieldCheck, title: "Enterprise-Grade Security", description: "Custom solutions built with data privacy and security at the core — especially important for healthcare, finance, and legal businesses." },
    { icon: RefreshCw, title: "Ongoing Iteration", description: "Your business evolves — your automation should too. We provide continuous support, improvements, and new feature rollouts." },
];

const steps = [
    { title: "Discovery and scoping session", description: "We spend time understanding your business deeply — your tools, your team, your pain points, and your growth ambitions." },
    { title: "Solution design and proposal", description: "We produce a detailed technical proposal outlining the solution architecture, timeline, and investment required." },
    { title: "Agile build and testing", description: "Development happens in short sprints with regular demos. You see progress weekly and give feedback throughout — no surprises at the end." },
    { title: "Deployment and handover", description: "We deploy your solution, train your team, document everything, and remain available for support and future enhancements." },
];

const metrics = [
    { value: "100%", label: "Built for Your Business" },
    { value: "2–6wk", label: "Typical Build Time" },
    { value: "∞", label: "Integration Possibilities" },
    { value: "Ongoing", label: "Support Included" },
];

export default function CustomSolutions() {
    return (
        <>
            <Head title="Custom Solutions - Blueflow Automation">
                <meta
                    name="description"
                    content="When standard automation tools don't fit, Blueflow builds custom solutions tailored to your exact business needs. Purpose-built for Nigerian businesses."
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
                                        Your Business Is Unique. Your Automation Should Be Too.
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Some businesses have processes too specific, too complex, or too critical
                                        to fit into a standard automation package. That's where Blueflow's Custom
                                        Solutions come in. We work as an extension of your team — understanding
                                        your operations at a deep level and engineering automation that fits
                                        perfectly, integrates completely, and scales with you as you grow.
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
                                <SectionIllustration icon={Blocks} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">What We Can Build</h2>
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
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Have a Complex Problem to Solve?</h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-4">
                                Tell us what you're trying to automate. If it's possible, we'll build it —
                                and give you a clear scope and timeline before any work begins.
                            </p>
                            <p className="text-sm text-gray-400 dark:text-gray-500 mb-8">
                                Just need to connect tools you already use?{" "}
                                <Link href="/services/workflow-automation" className="text-blue-600 dark:text-blue-400 hover:underline">
                                    See Workflow Automation
                                </Link>{" "}
                                instead — it's a faster, more focused fit for that.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contact" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Discuss Your Project
                                </Link>
                                <a href="https://wa.me/2347064706193" target="_blank" rel="noreferrer" className="border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-semibold px-6 py-3 rounded-xl hover:border-blue-500 hover:text-blue-700 dark:hover:text-blue-300 transition-all">
                                    WhatsApp Us
                                </a>
                            </div>
                        </div>
                    </section>
                </main>
                <Footer />
            </div>
        </>
    );
}
