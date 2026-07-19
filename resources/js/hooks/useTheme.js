import { useEffect, useState } from "react";

// Same key Filament's own dark-mode JS reads/writes under /admin and /user
// (vendor/filament/filament/resources/views/components/layout/base.blade.php).
// Deliberately shared — toggling the theme here also carries over into
// Filament (and a Filament toggle carries back here), so the whole site
// feels like one unit rather than two independently-themed halves.
const STORAGE_KEY = "theme";

function getStoredTheme() {
    try {
        return localStorage.getItem(STORAGE_KEY) === "light" ? "light" : "dark";
    } catch {
        return "dark";
    }
}

function applyTheme(theme) {
    document.documentElement.classList.toggle("dark", theme === "dark");
    try {
        localStorage.setItem(STORAGE_KEY, theme);
    } catch {
        // Private browsing/storage disabled — the toggle still works for
        // this page view, it just won't carry over to the next visit.
    }
}

/**
 * Site-wide light/dark toggle. Persisted in localStorage and applied via a
 * `dark` class on <html> (see app.css's @custom-variant + app.blade.php's
 * inline pre-paint script), so it survives Inertia's client-side page
 * navigations without needing to be wired into every individual page.
 */
export function useTheme() {
    const [theme, setTheme] = useState(getStoredTheme);

    useEffect(() => {
        applyTheme(theme);
    }, [theme]);

    return {
        theme,
        toggleTheme: () => setTheme((t) => (t === "dark" ? "light" : "dark")),
    };
}
