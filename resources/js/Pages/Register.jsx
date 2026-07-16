import { useState } from "react";
import { Head, Link } from "@inertiajs/react";
import axios from "axios";
import { CircleCheckBig } from "lucide-react";
import MainLayout from "@/Components/Layout/MainLayout";
import PageHero from "@/Components/Layout/PageHero";

const SERVICES = [
    { value: "chat-widget", label: "Chat Widget", description: "AI assistant embedded on your website" },
    { value: "whatsapp-automation", label: "WhatsApp Automation", description: "Automated replies & workflows on WhatsApp", comingSoon: true },
    { value: "email-automation", label: "Email Automation", description: "Automated email sequences and replies", comingSoon: true },
    { value: "payment-automation", label: "Payment Automation", description: "Automated invoicing and payment collection", comingSoon: true },
    { value: "crm-integration", label: "CRM Integration", description: "Sync leads and customers into your CRM", comingSoon: true },
    { value: "workflow-automation", label: "Workflow Automation", description: "Custom internal process automation", comingSoon: true },
];

const BUSINESS_TYPES = [
    { value: "commercial-bank", label: "Commercial Bank" },
    { value: "ecommerce", label: "Ecommerce" },
    { value: "fintech", label: "Fintech" },
    { value: "food-beverage", label: "Food & Beverage" },
    { value: "government", label: "Government" },
    { value: "healthcare", label: "Healthcare" },
    { value: "tech", label: "Technology" },
    { value: "law", label: "Law" },
    { value: "logistics", label: "Logistics" },
    { value: "microfinance", label: "Microfinance Bank" },
    { value: "online-store", label: "Online Store" },
    { value: "real-estate", label: "Real Estate" },
    { value: "school", label: "School" },
    { value: "sme", label: "SME" },
    { value: "others", label: "Others" },
];

function getQueryParam(name) {
    if (typeof window === "undefined") return "";
    return new URLSearchParams(window.location.search).get(name) || "";
}

export default function Register() {
    const plan = getQueryParam("plan");

    const [form, setForm] = useState({
        business_name: "",
        email: "",
        telephone: "",
        type: "",
        features: ["chat-widget"],
        password: "",
        password_confirmation: "",
        terms_accepted: false,
        website: "", // honeypot — left empty by real visitors
    });
    const [status, setStatus] = useState("idle"); // idle | submitting | success | error
    const [errors, setErrors] = useState({});

    function update(field) {
        return (e) => setForm((f) => ({ ...f, [field]: e.target.value }));
    }

    function updateCheckbox(field) {
        return (e) => setForm((f) => ({ ...f, [field]: e.target.checked }));
    }

    function toggleFeature(slug) {
        setForm((f) => ({
            ...f,
            features: f.features.includes(slug)
                ? f.features.filter((s) => s !== slug)
                : [...f.features, slug],
        }));
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setStatus("submitting");
        setErrors({});

        try {
            await axios.post("/api/register", form);
            setStatus("success");
        } catch (err) {
            setStatus("error");
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors || {});
            }
        }
    }

    return (
        <MainLayout>
            <Head title="Create Your Account — Blueflow Automation">
                <meta
                    name="description"
                    content="Register your business on Blueflow Automation. Get started in minutes — an admin will review and approve your account shortly."
                />
            </Head>

            <PageHero
                badge="Get Started"
                title="Create Your Business Account"
                subtitle={
                    plan
                        ? `You selected the ${plan} plan — create your account below, and you'll be able to subscribe as soon as you're approved.`
                        : "Tell us about your business. Once approved, you'll get full access to your dashboard, chat widget, and billing and more."
                }
            />

            <section className="py-16 bg-white">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    {status === "success" ? (
                        <div className="bg-emerald-50 border border-emerald-200 rounded-2xl p-10 text-center">
                            <div className="w-14 h-14 mx-auto mb-4 bg-emerald-100 rounded-2xl flex items-center justify-center">
                                <CircleCheckBig className="w-7 h-7 text-emerald-600" strokeWidth={1.75} />
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 mb-2">Account created!</h3>
                            <p className="text-gray-600">
                                Your business is pending approval. We'll email you as soon as an admin approves your
                                account — you'll then be able to{" "}
                                <Link href="/user/login" className="text-blue-700 font-semibold hover:underline">
                                    log in
                                </Link>{" "}
                                and set up your chat widget and subscription.
                            </p>
                        </div>
                    ) : (
                        <form
                            onSubmit={handleSubmit}
                            className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5"
                        >
                            {/* Honeypot field — hidden from real users */}
                            <input
                                type="text"
                                name="website"
                                value={form.website}
                                onChange={update("website")}
                                className="hidden"
                                tabIndex={-1}
                                autoComplete="off"
                            />

                            <div>
                                <label className="text-sm font-medium text-gray-700 mb-1 block">Business Name *</label>
                                <input
                                    type="text"
                                    required
                                    value={form.business_name}
                                    onChange={update("business_name")}
                                    className="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                                {errors.business_name && (
                                    <p className="text-red-600 text-xs mt-1">{errors.business_name[0]}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-1 block">Email *</label>
                                    <input
                                        type="email"
                                        required
                                        value={form.email}
                                        onChange={update("email")}
                                        className="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    {errors.email && <p className="text-red-600 text-xs mt-1">{errors.email[0]}</p>}
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-1 block">Phone / WhatsApp</label>
                                    <input
                                        type="tel"
                                        value={form.telephone}
                                        onChange={update("telephone")}
                                        className="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700 mb-1 block">Business Type *</label>
                                <select
                                    required
                                    value={form.type}
                                    onChange={update("type")}
                                    className="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                >
                                    <option value="" disabled>Select your industry</option>
                                    {BUSINESS_TYPES.map((t) => (
                                        <option key={t.value} value={t.value}>{t.label}</option>
                                    ))}
                                </select>
                                {errors.type && <p className="text-red-600 text-xs mt-1">{errors.type[0]}</p>}
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700 mb-1 block">Which services are you interested in? *</label>
                                <p className="text-xs text-gray-400 mb-2">This decides what shows up in your dashboard — you can change it later.</p>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    {SERVICES.map((s) => (
                                        <label
                                            key={s.value}
                                            className={`flex items-start gap-2.5 text-sm rounded-xl border px-3.5 py-2.5 transition-colors ${
                                                s.comingSoon
                                                    ? "border-gray-100 bg-gray-50 cursor-not-allowed opacity-60"
                                                    : form.features.includes(s.value)
                                                        ? "border-blue-500 bg-blue-50 cursor-pointer"
                                                        : "border-gray-200 hover:border-gray-300 cursor-pointer"
                                            }`}
                                        >
                                            <input
                                                type="checkbox"
                                                checked={form.features.includes(s.value)}
                                                onChange={() => toggleFeature(s.value)}
                                                disabled={s.comingSoon}
                                                className="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 disabled:cursor-not-allowed"
                                            />
                                            <span>
                                                <span className="flex items-center gap-1.5 font-medium text-gray-800">
                                                    {s.label}
                                                    {s.comingSoon && (
                                                        <span className="text-[10px] font-semibold uppercase tracking-wide text-gray-400 bg-gray-200 rounded-full px-1.5 py-0.5">
                                                            Coming soon
                                                        </span>
                                                    )}
                                                </span>
                                                <span className="block text-xs text-gray-400">{s.description}</span>
                                            </span>
                                        </label>
                                    ))}
                                </div>
                                {errors.features && <p className="text-red-600 text-xs mt-1">{errors.features[0]}</p>}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-1 block">Password *</label>
                                    <input
                                        type="password"
                                        required
                                        minLength={8}
                                        value={form.password}
                                        onChange={update("password")}
                                        className="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    {errors.password && (
                                        <p className="text-red-600 text-xs mt-1">{errors.password[0]}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-1 block">Confirm Password *</label>
                                    <input
                                        type="password"
                                        required
                                        minLength={8}
                                        value={form.password_confirmation}
                                        onChange={update("password_confirmation")}
                                        className="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="flex items-start gap-2.5 text-sm text-gray-600 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        required
                                        checked={form.terms_accepted}
                                        onChange={updateCheckbox("terms_accepted")}
                                        className="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span>
                                        By continuing, I agree to Blueflow's{" "}
                                        <Link href="/terms-of-service" target="_blank" className="text-blue-700 font-semibold hover:underline">
                                            Terms of Service
                                        </Link>{" "}
                                        and{" "}
                                        <Link href="/privacy-policy" target="_blank" className="text-blue-700 font-semibold hover:underline">
                                            Privacy Policy
                                        </Link>
                                        . *
                                    </span>
                                </label>
                                {errors.terms_accepted && (
                                    <p className="text-red-600 text-xs mt-1">{errors.terms_accepted[0]}</p>
                                )}
                            </div>

                            {status === "error" && !Object.keys(errors).length && (
                                <p className="text-red-600 text-sm">
                                    Something went wrong. Please try again or message us on WhatsApp.
                                </p>
                            )}

                            <button
                                type="submit"
                                disabled={status === "submitting" || !form.terms_accepted || form.features.length === 0}
                                className="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-60"
                            >
                                {status === "submitting" ? "Creating account..." : "Create Account"}
                            </button>

                            <p className="text-center text-xs text-gray-400">
                                Already registered?{" "}
                                <Link href="/user/login" className="text-blue-700 font-semibold hover:underline">
                                    Log in
                                </Link>
                            </p>
                        </form>
                    )}
                </div>
            </section>
        </MainLayout>
    );
}
