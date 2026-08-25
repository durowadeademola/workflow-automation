import { useState } from "react";
import { createPortal } from "react-dom";
import { Link } from "@inertiajs/react";
import { useTheme } from "@/hooks/useTheme";

const servicesLinks = [
    { label: "Chat Widget", href: "/services/chat-widget" },
    { label: "Marketing Automation", href: "/services/marketing-automation" },
    { label: "WhatsApp Automation", href: "/services/whatsapp-automation", comingSoon: true },
    { label: "CRM Integration", href: "/services/crm-integration", comingSoon: true },
    { label: "Email Automation", href: "/services/email-automation", comingSoon: true },
    { label: "Payment Automation", href: "/services/payment-automation", comingSoon: true },
    { label: "Workflow Automation", href: "/services/workflow-automation", comingSoon: true },
    { label: "Custom Solutions", href: "/services/custom-solutions" },
];

const industriesLinks = [
    { label: "Healthcare & Clinics", href: "/industries/healthcare" },
    { label: "Real Estate", href: "/industries/real-estate" },
    { label: "E-commerce & Retail", href: "/industries/ecommerce" },
    { label: "Hotels & Hospitality", href: "/industries/hospitality" },
    { label: "Restaurants & Cafés", href: "/industries/restaurants" },
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
            <div className="bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-100 dark:border-gray-800 py-2">
                {items.map((item) =>
                    item.comingSoon ? (
                        <span
                            key={item.href}
                            className="flex items-center justify-between px-4 py-2.5 text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed"
                        >
                            {item.label}
                            <span className="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 rounded-full px-1.5 py-0.5">
                                N/A
                            </span>
                        </span>
                    ) : (
                        <Link
                            key={item.href}
                            href={item.href}
                            className="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                        >
                            {item.label}
                        </Link>
                    )
                )}
            </div>
        </div>
    );
}

function ThemeToggle({ className = "" }) {
    const { theme, toggleTheme } = useTheme();
    const isDark = theme === "dark";

    return (
        <button
            type="button"
            onClick={toggleTheme}
            aria-label={isDark ? "Switch to light mode" : "Switch to dark mode"}
            title={isDark ? "Switch to light mode" : "Switch to dark mode"}
            className={`relative w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 transition-colors ${className}`}
        >
            {isDark ? (
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            ) : (
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            )}
        </button>
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

    return createPortal(
        <>
            <nav className="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-100 dark:border-gray-800 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <Link href="/" className="flex items-center gap-2 flex-shrink-0">
                            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span className="text-white font-bold text-sm">BA</span>
                            </div>
                            <span className="font-bold text-gray-900 dark:text-white text-lg">Blueflow Automation</span>
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
                                            className="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-blue-700 dark:hover:text-blue-300 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-colors"
                                        >
                                            {item.label}
                                        </Link>
                                    ) : (
                                        <button className="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-blue-700 dark:hover:text-blue-300 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-colors flex items-center gap-1">
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
                            <ThemeToggle />
                            <Link
                                href="/user/login"
                                className="text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                            >
                                Log In
                            </Link>
                            <Link href="/register"
                                className="bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Sign Up
                            </Link>
                        </div>

                        {/* Mobile: theme toggle + menu button */}
                        <div className="md:hidden flex items-center gap-1">
                            <ThemeToggle />
                            <button
                                className="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
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
                </div>
            </nav>

            {/* Mobile Menu — rendered outside the navbar, fixed to viewport below the 64px (h-16) header */}
            {mobileOpen && (
                <div className="md:hidden fixed top-16 left-0 right-0 h-[calc(100vh-4rem)] z-40 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 overflow-y-auto px-4 py-4 space-y-1">
                    {navItems.map((item) =>
                        item.href ? (
                            <Link
                                key={item.label}
                                href={item.href}
                                className="block px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/40 rounded-lg"
                                onClick={() => setMobileOpen(false)}
                            >
                                {item.label}
                            </Link>
                        ) : (
                            <div key={item.label}>
                                <p className="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                    {item.label}
                                </p>
                                {item.dropdown.map((sub) =>
                                    sub.comingSoon ? (
                                        <span
                                            key={sub.href}
                                            className="flex items-center justify-between px-5 py-2 text-sm text-gray-400 dark:text-gray-500"
                                        >
                                            {sub.label}
                                            <span className="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 rounded-full px-1.5 py-0.5">
                                                N/A
                                            </span>
                                        </span>
                                    ) : (
                                        <Link
                                            key={sub.href}
                                            href={sub.href}
                                            className="block px-5 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/40 rounded-lg"
                                            onClick={() => setMobileOpen(false)}
                                        >
                                            {sub.label}
                                        </Link>
                                    )
                                )}
                            </div>
                        )
                    )}
                    <div className="pt-3 flex flex-col gap-2">
                        <Link
                            href="/user/login"
                            className="block text-center py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800"
                            onClick={() => setMobileOpen(false)}
                        >
                            Log in
                        </Link>
                        <Link
                            href="/register"
                            className="block text-center py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                            onClick={() => setMobileOpen(false)}
                        >
                            Sign Up
                        </Link>
                    </div>
                </div>
            )}
        </>,
        document.body,
    );
}
