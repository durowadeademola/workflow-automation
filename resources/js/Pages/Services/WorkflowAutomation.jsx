import { Head, Link } from "@inertiajs/react";
import { Route, Settings2, Puzzle, Shuffle, ClipboardCheck, Radio, Workflow } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Automate repetitive tasks across every department — sales, operations, finance, and support",
    "Connect all your tools so data flows automatically without manual copying or switching apps",
    "Build multi-step workflows with conditional logic — no coding required",
    "Every workflow is monitored 24/7 with instant alerts if something needs attention",
];

const features = [
    { icon: Route, title: "Multi-Step Workflows", description: "Chain actions across multiple tools — when X happens in app A, do Y in app B and notify via WhatsApp. Infinitely flexible." },
    { icon: Settings2, title: "No-Code Builder", description: "Our team builds your automations using a visual workflow editor. No developers needed, no technical debt created." },
    { icon: Puzzle, title: "App Integrations", description: "Connect Google Workspace, Paystack, WhatsApp, your CRM, inventory systems, and hundreds of other tools out of the box." },
    { icon: Shuffle, title: "Conditional Logic", description: "Workflows that think — route tasks differently based on order value, customer type, time of day, or any condition you define." },
    { icon: ClipboardCheck, title: "Task & Approval Flows", description: "Automate internal handoffs — assign tasks, request approvals, and escalate issues without anyone having to remember to do it." },
    { icon: Radio, title: "Real-Time Monitoring", description: "Every workflow run is logged. See what triggered, what succeeded, and get alerted instantly if anything fails." },
];

const steps = [
    { title: "Map your current manual processes", description: "We sit down with your team to identify the repetitive tasks eating the most time — from order processing to staff onboarding." },
    { title: "Design the automation logic", description: "We translate your processes into workflow diagrams, defining every trigger, action, condition, and exception path." },
    { title: "Build and connect your tools", description: "Our team builds the workflows and integrates every app involved — testing thoroughly before anything goes live." },
    { title: "Launch and continuously improve", description: "Workflows go live with full monitoring in place. We track performance and refine automations as your business evolves." },
];

const metrics = [
    { value: "70%", label: "Reduction in Manual Tasks" },
    { value: "10hrs", label: "Saved Per Employee Weekly" },
    { value: "99.9%", label: "Workflow Uptime" },
    { value: "0", label: "Tasks Falling Through Cracks" },
];

export default function WorkflowAutomation() {
    return (
        <>
            <Head title="Workflow Automation - Blueflow Automation">
                <meta
                    name="description"
                    content="Eliminate repetitive manual work across your entire business. Blueflow builds custom workflow automations that connect your tools and keep operations running smoothly."
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
                                        Run Your Operations on Autopilot
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Every business has dozens of repetitive processes that eat time without
                                        adding value — copying data between apps, sending status updates, chasing
                                        approvals, assigning tasks. Blueflow's Workflow Automation identifies these
                                        bottlenecks and eliminates them entirely. The result is a business that
                                        moves faster, makes fewer errors, and scales without proportionally
                                        increasing your headcount.
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
                                <SectionIllustration icon={Workflow} accent="blue" />
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
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Ready to Automate Your Operations?</h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-4">
                                Every workflow automation is custom-built for your business — tell us what you need and we'll scope it out together.
                            </p>
                            <p className="text-sm text-gray-400 dark:text-gray-500 mb-2">
                                No sign-up required — just fill in a few details and we'll get back to you.
                            </p>
                            <p className="text-sm text-gray-400 dark:text-gray-500 mb-8">
                                Need something broader — custom dashboards, AI features, legacy integrations?{" "}
                                <Link href="/services/custom-solutions" className="text-blue-600 dark:text-blue-400 hover:underline">
                                    See Custom Solutions
                                </Link>
                                .
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contact?interest=Workflow%20Automation" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Request a Custom Quote
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
