import { Link } from "@inertiajs/react";

const footerLinks = {
    Services: [
        { label: "WhatsApp Automation", href: "/services/whatsapp-automation" },
        { label: "CRM Integration", href: "/services/crm-integration" },
        { label: "Email Automation", href: "/services/email-automation" },
        { label: "Payment Automation", href: "/services/payment-automation" },
        { label: "Workflow Automation", href: "/services/workflow-automation" },
        { label: "Custom Solutions", href: "/services/custom-solutions" },
    ],
    Industries: [
        { label: "Restaurants", href: "/industries/restaurants" },
        { label: "E-commerce", href: "/industries/ecommerce" },
        { label: "Hotels & Hospitality", href: "/industries/hospitality" },
        { label: "Healthcare", href: "/industries/healthcare" },
        { label: "Real Estate", href: "/industries/real-estate" },
        { label: "Professional Services", href: "/industries/professional-services" },
    ],
    Company: [
        { label: "About Us", href: "/about" },
        { label: "Case Studies", href: "/#case-studies" },
        { label: "Pricing", href: "/#pricing" },
        { label: "Contact", href: "/contact" },
    ],
    Resources: [
        { label: "FAQs", href: "/#faq" },
        { label: "ROI Calculator", href: "/#roi-calculator" },
        { label: "Support", href: "/contact" },
        { label: "Privacy Policy", href: "/privacy-policy" },
        { label: "Terms of Service", href: "/terms-of-service" },
    ],
};

export default function Footer() {
    return (
        <footer className="bg-gray-900 text-gray-400">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
                    {/* Brand col */}
                    <div className="lg:col-span-1">
                        <Link href="/" className="flex items-center gap-2 mb-4">
                            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span className="text-white font-bold text-sm">BF</span>
                            </div>
                            <span className="font-bold text-white text-base">Blueflow Automation</span>
                        </Link>
                        <p className="text-sm leading-relaxed mb-6">
                            AI-powered automation for Nigerian businesses. Automate everything, grow faster.
                        </p>
                        <div className="space-y-2 text-sm">
                            <a href="mailto:hello@blueflowautomation.com" className="flex items-center gap-2 hover:text-blue-400 transition-colors">
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                hello@blueflowautomation.com
                            </a>
                            <a href="tel:+2347064706193" className="flex items-center gap-2 hover:text-blue-400 transition-colors">
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                +234 706 470 6193
                            </a>
                        </div>
                    </div>

                    {/* Link cols */}
                    {Object.entries(footerLinks).map(([title, links]) => (
                        <div key={title}>
                            <h4 className="text-white font-semibold text-sm mb-4">{title}</h4>
                            <ul className="space-y-2.5">
                                {links.map((link) => (
                                    <li key={link.href}>
                                        <Link
                                            href={link.href}
                                            className="text-sm hover:text-blue-400 transition-colors"
                                        >
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                {/* Bottom bar */}
                <div className="border-t border-gray-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p className="text-sm">© 2026 Blueflow Automation. All rights reserved.</p>

                    <div className="flex items-center gap-4 text-sm">
                        <Link href="/privacy-policy" className="hover:text-blue-400 transition-colors">Privacy Policy</Link>
                        <Link href="/terms-of-service" className="hover:text-blue-400 transition-colors">Terms of Service</Link>
                    </div>
                </div>
            </div>
        </footer>
    );
}
