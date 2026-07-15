import { useState } from "react";
import { Link } from "@inertiajs/react";

const servicesLinks = [
    { label: "Chat Widget", href: "/services/chat-widget" },
    { label: "WhatsApp Automation", href: "/services/whatsapp-automation" },
    { label: "CRM Integration", href: "/services/crm-integration" },
    { label: "Email Automation", href: "/services/email-automation" },
    { label: "Payment Automation", href: "/services/payment-automation" },
    { label: "Workflow Automation", href: "/services/workflow-automation" },
    { label: "Custom Solutions", href: "/services/custom-solutions" },
];

const industriesLinks = [
    { label: "Restaurants & Cafés", href: "/industries/restaurants" },
    { label: "E-commerce & Retail", href: "/industries/ecommerce" },
    { label: "Hotels & Hospitality", href: "/industries/hospitality" },
    { label: "Healthcare & Clinics", href: "/industries/healthcare" },
    { label: "Real Estate", href: "/industries/real-estate" },
    { label: "Professional Services", href: "/industries/professional-services" },
];

const resourcesLinks = [
    { label: "FAQs", href: "/#faq" },
    { label: "ROI Calculator", href: "/#roi-calculator" },
    { label: "Support", href: "/contact" },
];

function DropdownMenu({ items }) {
    return (
        <div className="absolute top-full left-1/2 -translate-x-1/2 w-56 z-50">
            <div className="h-2 w-full" />
            <div className="bg-white rounded-xl shadow-xl border border-gray-100 py-2">
                {items.map((item) => (
                    <Link
                        key={item.href}
                        href={item.href}
                        className="block px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                    >
                        {item.label}
                    </Link>
                ))}
            </div>
        </div>
    );
}

export default function Navbar() {
    const [mobileOpen, setMobileOpen] = useState(false);
    const [activeDropdown, setActiveDropdown] = useState(null);

    const navItems = [
        { label: "Services", dropdown: servicesLinks },
        { label: "Industries", dropdown: industriesLinks },
        { label: "Pricing", href: "/#pricing" },
        { label: "Case Studies", href: "/#case-studies" },
        { label: "Resources", dropdown: resourcesLinks },
        { label: "About", href: "/about" },
    ];

    return (
        <>
            <nav className="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <Link href="/" className="flex items-center gap-2 flex-shrink-0">
                            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span className="text-white font-bold text-sm">BF</span>
                            </div>
                            <span className="font-bold text-gray-900 text-lg">Blueflow Automation</span>
                        </Link>

                        {/* Desktop Nav */}
                        <div className="hidden md:flex items-center gap-1">
                            {navItems.map((item) => (
                                <div
                                    key={item.label}
                                    className="relative"
                                    onMouseEnter={() => item.dropdown && setActiveDropdown(item.label)}
                                    onMouseLeave={() => setActiveDropdown(null)}
                                >
                                    {item.href ? (
                                        <Link
                                            href={item.href}
                                            className="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-700 rounded-lg hover:bg-blue-50 transition-colors"
                                        >
                                            {item.label}
                                        </Link>
                                    ) : (
                                        <button className="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-700 rounded-lg hover:bg-blue-50 transition-colors flex items-center gap-1">
                                            {item.label}
                                            <svg className="w-3.5 h-3.5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    )}
                                    {item.dropdown && activeDropdown === item.label && (
                                        <DropdownMenu items={item.dropdown} />
                                    )}
                                </div>
                            ))}
                        </div>

                        {/* Desktop Auth Buttons */}
                        <div className="hidden md:flex items-center gap-3">
                            <Link
                                href="/admin/login"
                                className="text-sm font-medium text-gray-700 hover:text-blue-700 transition-colors"
                            >
                                Log In
                            </Link>
                            <Link href="/register"
                                className="bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Get Started
                            </Link>
                        </div>

                        {/* Mobile Menu Toggle */}
                        <button
                            className="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100"
                            onClick={() => setMobileOpen(!mobileOpen)}
                            aria-label="Toggle menu"
                        >
                            {mobileOpen ? (
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            ) : (
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            )}
                        </button>
                    </div>
                </div>
            </nav>

            {/* Mobile Menu — rendered outside the navbar, fixed to viewport below the 64px (h-16) header */}
            {mobileOpen && (
                <div className="md:hidden fixed top-16 left-0 right-0 h-[calc(100vh-4rem)] z-40 bg-white border-t border-gray-100 overflow-y-auto px-4 py-4 space-y-1">
                    {navItems.map((item) =>
                        item.href ? (
                            <Link
                                key={item.label}
                                href={item.href}
                                className="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg"
                                onClick={() => setMobileOpen(false)}
                            >
                                {item.label}
                            </Link>
                        ) : (
                            <div key={item.label}>
                                <p className="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    {item.label}
                                </p>
                                {item.dropdown.map((sub) => (
                                    <Link
                                        key={sub.href}
                                        href={sub.href}
                                        className="block px-5 py-2 text-sm text-gray-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg"
                                        onClick={() => setMobileOpen(false)}
                                    >
                                        {sub.label}
                                    </Link>
                                ))}
                            </div>
                        )
                    )}
                    <div className="pt-3 flex flex-col gap-2">
                        <Link
                            href="/admin/login"
                            className="block text-center py-2 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50"
                            onClick={() => setMobileOpen(false)}
                        >
                            Log in
                        </Link>
                        <Link
                            href="/register"
                            className="block text-center py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                            onClick={() => setMobileOpen(false)}
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            )}
        </>
    );
}
