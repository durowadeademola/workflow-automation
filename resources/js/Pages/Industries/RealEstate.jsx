import { Head, Link } from "@inertiajs/react";
import { Home, Bot, Building2, CalendarCheck, Camera, RefreshCw, BarChart3, Quote } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "AI chatbot qualifies every lead automatically before an agent gets involved",
    "Property listings with photos and pricing sent instantly to interested prospects",
    "Viewing appointments booked via WhatsApp without any back-and-forth",
    "Automated follow-up sequences keep prospects warm until they are ready to buy",
];

const features = [
    { icon: Bot, title: "Lead Qualification Bot", description: "Every enquiry is automatically screened for budget, location preference, property type, and timeline — so agents only spend time on serious buyers." },
    { icon: Building2, title: "Property Listing Broadcasts", description: "Send new listings with photos, pricing, and key details to all interested prospects the moment a property becomes available." },
    { icon: CalendarCheck, title: "Viewing Appointment Scheduler", description: "Prospects book property viewings directly via WhatsApp based on agent availability — no phone tag, no missed opportunities." },
    { icon: Camera, title: "Virtual Tour & Media Delivery", description: "Automatically send photo galleries, video walkthroughs, floor plans, and location links to interested buyers on request." },
    { icon: RefreshCw, title: "Follow-Up Sequences", description: "Timed follow-up messages keep prospects engaged at every stage of their decision — from first enquiry all the way to signing." },
    { icon: BarChart3, title: "Lead Pipeline Dashboard", description: "See every lead, their current stage, interest level, last contact date, and projected deal value — all in one clear view." },
];

const steps = [
    { title: "Connect your WhatsApp Business number", description: "We link your agency's number to Blueflow and configure your property types, locations, price ranges, and agent availability." },
    { title: "Build your lead qualification flow", description: "We design a conversation that screens every new enquiry for budget, timeline, and preferences before routing to the right agent." },
    { title: "Set up your listing and follow-up sequences", description: "We configure automated property matching, media delivery, viewing scheduling, and the follow-up cadence that keeps leads from going cold." },
    { title: "Launch and let agents focus on closing", description: "Go live within days. Agents stop answering repetitive questions and start spending their time on qualified prospects ready to move." },
];

const metrics = [
    { value: "2x", label: "More Qualified Leads" },
    { value: "70%", label: "Less Admin Time" },
    { value: "15+", label: "Extra Deals Monthly" },
    { value: "< 1min", label: "Lead Response Time" },
];

const testimonial = {
    quote: "Before Blueflow, our agents were spending most of the day answering the same basic questions over and over. Now the bot handles all of that and only passes over leads who are genuinely ready to view. Our close rate has never been higher.",
    name: "Tobi Adeyemi",
    role: "CEO, Lekki Properties — Lagos",
};

export default function RealEstate() {
    return (
        <>
            <Head title="Automation for Real Estate Agents">
                <meta
                    name="description"
                    content="Qualify leads automatically, send property listings, and book viewings via WhatsApp. Blueflow helps Nigerian real estate agents close more deals with less admin."
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
                                    <span className="inline-block bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                                        Real Estate
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-5">
                                        Close More Deals. Spend Less Time on Admin.
                                    </h2>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        Real estate agents in Nigeria are losing deals every day — not because
                                        of bad properties or bad pricing, but because leads go cold while agents
                                        are busy answering basic questions, sending listings manually, and
                                        trying to schedule viewings over the phone. Blueflow automates every
                                        step of the lead journey from first enquiry to booked viewing, so your
                                        agents can focus entirely on what they do best: building relationships
                                        and closing deals.
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
                                <SectionIllustration icon={Home} accent="blue" />
                            </div>
                        </div>
                    </section>

                    {/* Features */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-14">
                                Built for Nigerian Real Estate Agencies
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

                    {/* Testimonial */}
                    <section className="py-20 bg-gray-50 dark:bg-gray-800">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <div className="w-12 h-12 mx-auto mb-6 bg-blue-50 dark:bg-blue-900/40 rounded-2xl flex items-center justify-center"><Quote className="w-6 h-6 text-blue-600" strokeWidth={1.75} /></div>
                            <blockquote className="text-xl font-medium text-gray-800 dark:text-gray-100 leading-relaxed mb-6">
                                "{testimonial.quote}"
                            </blockquote>
                            <p className="text-sm font-semibold text-gray-900 dark:text-white">{testimonial.name}</p>
                            <p className="text-sm text-gray-500 dark:text-gray-400">{testimonial.role}</p>
                        </div>
                    </section>

                    {/* CTA */}
                    <section className="py-20 bg-white dark:bg-gray-900">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">
                                Ready to Automate Your Real Estate Business?
                            </h2>
                            <p className="text-gray-500 dark:text-gray-400 mb-8">
                                Book a free demo and we'll show you exactly how Blueflow works for real estate agencies like yours.
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
