// FAQ.jsx
import { useState } from "react";
import { Link } from "@inertiajs/react";
import { useScrollAnimation } from "@/hooks/useScrollAnimation";

const faqs = [
    {
        question: "How long does implementation take?",
        answer: "There's no long onboarding queue. Sign up and choose the service(s) you need — about two minutes — then share your requirements and preferences right in your dashboard. Our team configures everything behind the scenes and lets you know the moment it's ready to go live.",
    },
    {
        question: "Do I need any technical skills to use Blueflow?",
        answer: "Not at all. Blueflow is designed to be simple for business owners and staff. Our interface is intuitive, and we provide full training for you and your team.",
    },
    {
        question: "What happens if I need to cancel?",
        answer: "You can cancel anytime with no penalties or hidden fees, whether you're on a monthly or yearly plan. By default you keep access until your current billing period ends and it simply won't renew — or if you'd rather stop right away, you can request a refund for the unused portion instead.",
    },
    {
        question: "Can I integrate Blueflow with my existing systems?",
        answer: "Yes. Blueflow integrates seamlessly with your existing systems whether it's with our Chat Widget or your Custom Workflows — so reach out and we'll walk you through exactly what's available for your specific setup.",
    },
    {
        question: "Is my data safe and secure?",
        answer: "Yes. Your connection to Blueflow is encrypted end to end, and we never sell or share your customer data.",
    },
    {
        question: "What kind of support do you offer?",
        answer: "Every subscription includes support via WhatsApp, phone, and email — plus an in-app Support Tickets section right in your dashboard, where our team replies directly and you're notified the moment we respond.",
    },
];

function FAQItem({ question, answer, delay, visible }) {
    const [open, setOpen] = useState(false);
    return (
        <div
            className="border-b border-gray-100 dark:border-gray-800 last:border-0"
            style={{
                opacity: visible ? 1 : 0,
                transform: visible ? "translateX(0)" : "translateX(-30px)",
                transition: `opacity 0.5s ease ${delay}ms, transform 0.5s ease ${delay}ms`,
            }}
        >
            <button
                onClick={() => setOpen(!open)}
                className="w-full flex items-center justify-between py-5 text-left group"
            >
                <span className="font-semibold text-gray-900 dark:text-white text-sm pr-4 group-hover:text-blue-700 dark:group-hover:text-blue-400 transition-colors">
                    {question}
                </span>
                <span className={`flex-shrink-0 w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all ${
                    open ? "bg-blue-600 border-blue-600 rotate-45" : "border-gray-300 dark:border-gray-600 group-hover:border-blue-500"
                }`}>
                    <svg className={`w-3 h-3 ${open ? "text-white" : "text-gray-400 dark:text-gray-500"}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M12 4v16m8-8H4" />
                    </svg>
                </span>
            </button>
            {open && (
                <div className="pb-5 text-sm text-gray-600 dark:text-gray-300 leading-relaxed pr-10">{answer}</div>
            )}
        </div>
    );
}

export default function FAQ() {
    const [headingRef, headingVisible] = useScrollAnimation(0.3);
    const [faqRef, faqVisible] = useScrollAnimation(0.05);

    return (
        <section id="faq" className="py-20 bg-white dark:bg-gray-900">
            <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    ref={headingRef}
                    className="text-center mb-12"
                    style={{
                        opacity: headingVisible ? 1 : 0,
                        transform: headingVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease, transform 0.6s ease",
                    }}
                >
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Frequently Asked Questions
                    </h2>
                    <p className="text-gray-500 dark:text-gray-400">Got questions? We've got your answers.</p>
                </div>

                <div ref={faqRef} className="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm px-6 divide-y divide-gray-100 dark:divide-gray-800 mb-10">
                    {faqs.map((faq, i) => (
                        <FAQItem
                            key={faq.question}
                            {...faq}
                            delay={i * 80}
                            visible={faqVisible}
                        />
                    ))}
                </div>

                <div
                    className="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 text-center border border-gray-100 dark:border-gray-800"
                    style={{
                        opacity: faqVisible ? 1 : 0,
                        transform: faqVisible ? "translateY(0)" : "translateY(30px)",
                        transition: "opacity 0.6s ease 500ms, transform 0.6s ease 500ms",
                    }}
                >
                    <h3 className="font-bold text-gray-900 dark:text-white mb-2">Still have questions?</h3>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        We are here to help. Talk to our team and get answers in minutes, not days.
                    </p>
                    <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a
                            href="https://wa.me/2347064706193"
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-blue-700 transition-colors text-sm"
                        >
                            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp Us
                        </a>
                        <Link
                            href="/contact"
                            className="inline-flex items-center gap-2 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-semibold px-5 py-2.5 rounded-xl hover:border-blue-500 hover:text-blue-700 dark:hover:text-blue-400 transition-colors text-sm"
                        >
                            Send a Message
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    );
}
