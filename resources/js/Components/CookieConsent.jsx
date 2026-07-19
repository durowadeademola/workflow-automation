import { useEffect, useState } from "react";

const STORAGE_KEY = "cookie_consent";
// Kept in sync with the offset chat-widget.js applies to #cw-root/#cw-bubble
// via the same class name, so the widget bubble moves clear of this banner
// instead of the two fighting over the same corner of the screen.
const BODY_CLASS = "cw-cookie-banner-visible";

function loadConsent() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
}

function saveConsent(consent) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(consent));
    } catch {
        // Private-browsing/storage-disabled — the banner still gets
        // dismissed for this visit, it just won't remember next time.
    }
    window.__cookieConsent = consent;
    window.dispatchEvent(new CustomEvent("cookie-consent-updated", { detail: consent }));
}

export default function CookieConsent() {
    const [visible, setVisible] = useState(false);
    const [expanded, setExpanded] = useState(false);
    const [analytics, setAnalytics] = useState(false);
    const [marketing, setMarketing] = useState(false);

    useEffect(() => {
        const existing = loadConsent();
        if (existing) {
            window.__cookieConsent = existing;
            return;
        }
        setVisible(true);
        document.body.classList.add(BODY_CLASS);
        return () => document.body.classList.remove(BODY_CLASS);
    }, []);

    function dismiss() {
        setVisible(false);
        document.body.classList.remove(BODY_CLASS);
    }

    function acceptAll() {
        saveConsent({ necessary: true, analytics: true, marketing: true, timestamp: new Date().toISOString() });
        dismiss();
    }

    function necessaryOnly() {
        saveConsent({ necessary: true, analytics: false, marketing: false, timestamp: new Date().toISOString() });
        dismiss();
    }

    function savePreferences() {
        saveConsent({ necessary: true, analytics, marketing, timestamp: new Date().toISOString() });
        dismiss();
    }

    if (!visible) return null;

    return (
        <div
            className="fixed bottom-0 inset-x-0 z-[999999] bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-[0_-4px_24px_rgba(0,0,0,0.1)] px-4 py-4 sm:px-6"
            role="dialog"
            aria-label="Cookie consent"
        >
            <div className="max-w-5xl mx-auto">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                    <p className="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                        We use cookies to run this site and, with your permission, to understand how it's used.{" "}
                        <a href="/privacy-policy" className="text-blue-700 dark:text-blue-400 font-medium hover:underline">
                            Learn more
                        </a>
                    </p>
                    <div className="flex flex-wrap items-center gap-2 shrink-0">
                        <button
                            type="button"
                            onClick={() => setExpanded((e) => !e)}
                            className="px-3 py-2.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100 transition-colors"
                        >
                            Customize
                        </button>
                        <button
                            type="button"
                            onClick={necessaryOnly}
                            className="flex-1 sm:flex-none px-4 py-2.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                        >
                            Necessary only
                        </button>
                        <button
                            type="button"
                            onClick={acceptAll}
                            className="flex-1 sm:flex-none px-4 py-2.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                        >
                            Accept all
                        </button>
                        <button
                            type="button"
                            onClick={necessaryOnly}
                            aria-label="Close (uses necessary cookies only)"
                            title="Close (uses necessary cookies only)"
                            className="w-8 h-8 shrink-0 flex items-center justify-center rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        >
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {expanded && (
                    <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-3.5">
                        <label className="flex items-center justify-between gap-4">
                            <span>
                                <span className="block text-sm font-medium text-gray-800 dark:text-gray-100">Necessary</span>
                                <span className="block text-xs text-gray-500 dark:text-gray-400">Required for the site and chat widget to work. Always on.</span>
                            </span>
                            <input type="checkbox" checked disabled className="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 opacity-60 shrink-0" />
                        </label>
                        <label className="flex items-center justify-between gap-4 cursor-pointer">
                            <span>
                                <span className="block text-sm font-medium text-gray-800 dark:text-gray-100">Analytics</span>
                                <span className="block text-xs text-gray-500 dark:text-gray-400">Helps us understand how visitors use the site.</span>
                            </span>
                            <input
                                type="checkbox"
                                checked={analytics}
                                onChange={(e) => setAnalytics(e.target.checked)}
                                className="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 shrink-0"
                            />
                        </label>
                        <label className="flex items-center justify-between gap-4 cursor-pointer">
                            <span>
                                <span className="block text-sm font-medium text-gray-800 dark:text-gray-100">Marketing</span>
                                <span className="block text-xs text-gray-500 dark:text-gray-400">Used to show more relevant ads.</span>
                            </span>
                            <input
                                type="checkbox"
                                checked={marketing}
                                onChange={(e) => setMarketing(e.target.checked)}
                                className="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 shrink-0"
                            />
                        </label>
                        <div className="flex justify-end pt-1">
                            <button
                                type="button"
                                onClick={savePreferences}
                                className="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                            >
                                Save preferences
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
