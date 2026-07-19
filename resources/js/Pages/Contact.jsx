import { useState } from "react";
import { Head } from "@inertiajs/react";
import axios from "axios";
import { CircleCheckBig, Mail, Phone } from "lucide-react";
import MainLayout from "@/Components/Layout/MainLayout";
import PageHero from "@/Components/Layout/PageHero";

function getQueryParam(name) {
    if (typeof window === "undefined") return "";
    return new URLSearchParams(window.location.search).get(name) || "";
}

export default function Contact() {
    const [form, setForm] = useState({
        name: "",
        business_name: "",
        email: "",
        phone: "",
        interest: getQueryParam("plan") || getQueryParam("interest") || "",
        message: "",
        website: "", // honeypot — left empty by real visitors
    });
    const [status, setStatus] = useState("idle"); // idle | submitting | success | error
    const [errors, setErrors] = useState({});

    function update(field) {
        return (e) => setForm((f) => ({ ...f, [field]: e.target.value }));
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setStatus("submitting");
        setErrors({});

        try {
            await axios.post("/api/leads", {
                ...form,
                source: getQueryParam("source") || "contact_page",
            });
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
            <Head title="Contact Us — Blueflow Automation">
                <meta
                    name="description"
                    content="Book a free demo or ask a question. Tell us about your business and we'll get back to you within one business day."
                />
            </Head>

            <PageHero
                badge="Get In Touch"
                title="Let's Automate Your Business"
                subtitle="Tell us a bit about your business and what you'd like to automate. We'll get back to you within one business day."
            />

            <section className="py-16 bg-white dark:bg-gray-900">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-10">
                    {/* Contact info */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-gray-50 dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
                            <h3 className="font-bold text-gray-900 dark:text-white mb-4">Prefer to reach out directly?</h3>
                            <div className="space-y-3 text-sm">
                                <a href="mailto:hello@blueflowautomation.com" className="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    <svg className="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    hello@blueflowautomation.com
                                </a>
                                <a href="tel:+2347064706193" className="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    <svg className="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    +234 706 470 6193
                                </a>
                                <a href="https://wa.me/2347064706193" target="_blank" rel="noreferrer" className="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    <svg className="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                                    </svg>
                                    WhatsApp Us
                                </a>
                            </div>
                        </div>
                        <div className="bg-blue-50 dark:bg-blue-900/30 rounded-2xl p-6 border border-blue-100 dark:border-blue-800/50">
                            <p className="text-sm text-blue-800 dark:text-blue-300">
                                We typically respond within a few hours during business days (9am–6pm WAT).
                            </p>
                        </div>
                    </div>

                    {/* Form */}
                    <div className="lg:col-span-2">
                        {status === "success" ? (
                            <div className="bg-emerald-50 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl p-10 text-center">
                                <div className="w-14 h-14 mx-auto mb-4 bg-emerald-100 dark:bg-emerald-900/60 rounded-2xl flex items-center justify-center">
                                    <CircleCheckBig className="w-7 h-7 text-emerald-600 dark:text-emerald-400" strokeWidth={1.75} />
                                </div>
                                <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-2">Thanks, {form.name.split(" ")[0]}!</h3>
                                <p className="text-gray-600 dark:text-gray-300">
                                    We've received your message and will get back to you shortly. In the meantime,
                                    feel free to{" "}
                                    <a href="https://wa.me/2347064706193" target="_blank" rel="noreferrer" className="text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                                        message us on WhatsApp
                                    </a>.
                                </p>
                            </div>
                        ) : (
                            <form onSubmit={handleSubmit} className="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 sm:p-8 space-y-5">
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

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 block">Full Name *</label>
                                        <input
                                            type="text"
                                            required
                                            value={form.name}
                                            onChange={update("name")}
                                            className="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        {errors.name && <p className="text-red-600 dark:text-red-400 text-xs mt-1">{errors.name[0]}</p>}
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 block">Business Name</label>
                                        <input
                                            type="text"
                                            value={form.business_name}
                                            onChange={update("business_name")}
                                            className="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 block">Email *</label>
                                        <input
                                            type="email"
                                            required
                                            value={form.email}
                                            onChange={update("email")}
                                            className="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        {errors.email && <p className="text-red-600 dark:text-red-400 text-xs mt-1">{errors.email[0]}</p>}
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 block">Phone / WhatsApp</label>
                                        <input
                                            type="tel"
                                            value={form.phone}
                                            onChange={update("phone")}
                                            className="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 block">What are you interested in?</label>
                                    <input
                                        type="text"
                                        placeholder="e.g. WhatsApp Automation, Professional plan..."
                                        value={form.interest}
                                        onChange={update("interest")}
                                        className="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1 block">Message</label>
                                    <textarea
                                        rows={4}
                                        value={form.message}
                                        onChange={update("message")}
                                        className="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                {status === "error" && !Object.keys(errors).length && (
                                    <p className="text-red-600 dark:text-red-400 text-sm">
                                        Something went wrong. Please try again or message us on WhatsApp.
                                    </p>
                                )}

                                <button
                                    type="submit"
                                    disabled={status === "submitting"}
                                    className="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-60"
                                >
                                    {status === "submitting" ? "Sending..." : "Send Message"}
                                </button>
                            </form>
                        )}
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
