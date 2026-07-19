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

const socialLinks = [
    {
        label: "X (Twitter)",
        href: "https://twitter.com/blueflowautomation",
        icon: (
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H15.17l-5.214-6.817L3.99 21.75H.68l7.73-8.835L.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
        ),
    },
    {
        label: "Facebook",
        href: "https://facebook.com/blueflowautomation",
        icon: (
            <path d="M22 12.06C22 6.505 17.523 2 12 2S2 6.505 2 12.06c0 5.02 3.657 9.184 8.438 9.94v-7.03H7.898v-2.91h2.54V9.845c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562v1.878h2.773l-.443 2.91h-2.33V22c4.78-.756 8.437-4.92 8.437-9.94z" />
        ),
    },
    {
        label: "Instagram",
        href: "https://instagram.com/blueflowautomation",
        icon: (
            <path d="M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.427.465a4.9 4.9 0 011.772 1.153 4.9 4.9 0 011.153 1.772c.248.637.415 1.362.465 2.427.05 1.066.06 1.405.06 4.122s-.01 3.056-.06 4.122c-.05 1.065-.217 1.79-.465 2.427a4.9 4.9 0 01-1.153 1.772 4.9 4.9 0 01-1.772 1.153c-.637.248-1.362.415-2.427.465-1.066.05-1.405.06-4.122.06s-3.056-.01-4.122-.06c-1.065-.05-1.79-.217-2.427-.465a4.9 4.9 0 01-1.772-1.153 4.9 4.9 0 01-1.153-1.772c-.248-.637-.415-1.362-.465-2.427C2.01 15.056 2 14.717 2 12s.01-3.056.06-4.122c.05-1.065.217-1.79.465-2.427a4.9 4.9 0 011.153-1.772A4.9 4.9 0 015.45 2.526c.637-.248 1.362-.415 2.427-.465C8.944 2.01 9.283 2 12 2zm0 1.802c-2.67 0-2.986.01-4.04.059-.976.045-1.505.207-1.858.344-.467.182-.8.399-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.05 1.055-.06 1.372-.06 4.04s.01 2.986.06 4.041c.045.975.207 1.504.344 1.857.182.467.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.05 1.37.06 4.041.06s2.987-.01 4.041-.06c.975-.045 1.504-.207 1.857-.344.467-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.05-1.055.06-1.372.06-4.041s-.01-2.985-.06-4.04c-.045-.975-.207-1.504-.344-1.857a3.1 3.1 0 00-.748-1.15 3.1 3.1 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.054-.05-1.37-.06-4.041-.06zm0 3.063a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-1.008a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0z" />
        ),
    },
    {
        label: "LinkedIn",
        href: "https://linkedin.com/company/blueflowautomation",
        icon: (
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455zM5.337 7.433a2.062 2.062 0 110-4.124 2.062 2.062 0 010 4.124zM7.114 20.452H3.558V9h3.556z" />
        ),
    },
];

export default function Footer() {
    return (
        <footer className="bg-gray-900 text-gray-400">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
                    {/* Brand col */}
                    <div className="lg:col-span-1">
                        <Link href="/" className="flex items-center gap-2 mb-4">
                            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span className="text-white font-bold text-sm">BA</span>
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
                        <div className="flex items-center gap-3 mt-6">
                            {socialLinks.map((social) => (
                                <a
                                    key={social.label}
                                    href={social.href}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label={social.label}
                                    className="w-9 h-9 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"
                                >
                                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        {social.icon}
                                    </svg>
                                </a>
                            ))}
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
