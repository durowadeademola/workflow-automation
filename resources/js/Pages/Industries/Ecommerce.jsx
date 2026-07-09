import { Head, Link } from "@inertiajs/react";
import { ShoppingCart, BookOpen, CheckCircle2, CreditCard, Truck, Target, BarChart3, Quote } from "lucide-react";
import Navbar from "@/Components/Navbar";
import Footer from "@/Components/Footer";
import IconTile from "@/Components/Icons/IconTile";
import SectionIllustration from "@/Components/Icons/SectionIllustration";

const highlights = [
    "Answer product questions and process orders automatically — even at midnight",
    "Send payment links and confirm purchases without any manual follow-up",
    "Automated delivery updates keep customers informed at every step",
    "Reorder sequences that bring customers back before they go to a competitor",
];

const features = [
    { icon: BookOpen, title: "WhatsApp Product Catalog", description: "Customers browse your full catalog with images, pricing, and variants directly on WhatsApp — no app download, no website visit required." },
    { icon: CheckCircle2, title: "Auto Order Processing", description: "Orders placed, confirmed, and sent to fulfilment automatically. Your team gets notified without touching a single message." },
    { icon: CreditCard, title: "Instant Payment Collection", description: "Paystack and Flutterwave payment links sent automatically at checkout. Payments confirmed and receipts issued in seconds." },
    { icon: Truck, title: "Delivery Tracking Updates", description: "Customers receive automatic status updates at every stage — order confirmed, dispatched, out for delivery, delivered." },
    { icon: Target, title: "Abandoned Cart Recovery", description: "Customers who enquired but didn't complete their order get a timed follow-up message — recovering sales you'd otherwise lose." },
    { icon: BarChart3, title: "Sales & Customer Reports", description: "Daily reports showing top-selling products, peak order times, best customers, and revenue trends — sent straight to your phone." },
];

const steps = [
    { title: "Connect your product catalog", description: "We import your products, pricing, and inventory into the WhatsApp automation system — however many SKUs you have." },
    { title: "Build your sales and payment flows", description: "We configure your ordering conversation, payment links, and confirmation messages tailored to how your store works." },
    { title: "Integrate logistics and inventory", description: "We connect your delivery partner and stock management system so orders flow end-to-end without manual handoffs." },
    { title: "Launch and scale automatically", description: "Go live within days. Every order, update, and follow-up runs on its own — freeing you to focus on sourcing and growth." },
];

const metrics = [
    { value: "3x", label: "Higher Conversion Rate" },
    { value: "6hrs", label: "Saved Daily" },
    { value: "₦800K", label: "Avg Extra Monthly Sales" },
    { value: "35%", label: "Repeat Purchase Rate" },
];

const testimonial = {
    quote: "Our WhatsApp used to be overwhelming — hundreds of messages and no way to keep up. Now it's our number one sales channel and we barely touch it. Blueflow handles everything automatically.",
    name: "Aisha Bello",
    role: "Founder, Lagos Style Boutique",
};

export default function Ecommerce() {
    return (
        <>
            <Head title="Automation for E-commerce & Retail">
                <meta
                    name="description"
                    content="Automate orders, payments, delivery updates, and customer follow-ups for your online store. Blueflow helps Nigerian e-commerce businesses sell more with less effort."
                />
            </Head>
            <div className="min-h-screen bg-white">
                <Navbar />
                <main>
                    {/* Overview */}
                    <section className="py-20 bg-white">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                                <div>
                                    <span className="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                                        E-commerce & Retail
                                    </span>
                                    <h2 className="text-3xl font-extrabold text-gray-900 mb-5">
                                        Sell More Without Being Glued to Your Phone
                                    </h2>
                                    <p className="text-gray-600 leading-relaxed mb-6">
                                        Running an online store in Nigeria means managing hundreds of WhatsApp
                                        messages daily, manually sending payment links, updating customers on
                                        deliveries, and chasing repeat orders — all while trying to source new
                                        products. Blueflow automates your entire sales process so every customer
                                        gets a fast, professional experience and no order ever slips through the cracks.
                                    </p>
                                    <ul className="space-y-3">
                                        {highlights.map((h) => (
                                            <li key={h} className="flex items-start gap-3 text-gray-700 text-sm">
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
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">
                                Built for Nigerian Online Stores
                            </h2>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                {features.map((f) => (
                                    <div key={f.title} className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                                        <IconTile icon={f.icon} color="blue" size="md" className="mb-3" />
                                        <h3 className="font-bold text-gray-900 mb-2">{f.title}</h3>
                                        <p className="text-sm text-gray-500">{f.description}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* How it works */}
                    <section className="py-20 bg-white">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-14">How It Works</h2>
                            <div className="space-y-6">
                                {steps.map((step, i) => (
                                    <div key={step.title} className="flex items-start gap-5">
                                        <div className="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            {i + 1}
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-gray-900 mb-1">{step.title}</h3>
                                            <p className="text-sm text-gray-500">{step.description}</p>
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
                    <section className="py-20 bg-gray-50">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <div className="w-12 h-12 mx-auto mb-6 bg-blue-50 rounded-2xl flex items-center justify-center"><Quote className="w-6 h-6 text-blue-600" strokeWidth={1.75} /></div>
                            <blockquote className="text-xl font-medium text-gray-800 leading-relaxed mb-6">
                                "{testimonial.quote}"
                            </blockquote>
                            <p className="text-sm font-semibold text-gray-900">{testimonial.name}</p>
                            <p className="text-sm text-gray-500">{testimonial.role}</p>
                        </div>
                    </section>

                    {/* CTA */}
                    <section className="py-20 bg-white">
                        <div className="max-w-3xl mx-auto px-4 text-center">
                            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">
                                Ready to Automate Your Store?
                            </h2>
                            <p className="text-gray-500 mb-8">
                                Book a free demo and we'll show you exactly how Blueflow works for e-commerce businesses like yours.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/contact" className="bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                                    Schedule Free Demo
                                </Link>
                                <Link href="/#pricing" className="border-2 border-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-xl hover:border-blue-500 hover:text-blue-700 transition-all">
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
