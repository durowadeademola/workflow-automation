import { useEffect } from 'react';
import { router } from '@inertiajs/react';
import Lenis from 'lenis';

/**
 * Site-wide eased/momentum scrolling. Mounted once at the app root (see
 * app.jsx) so it persists across Inertia page swaps instead of being torn
 * down and rebuilt on every navigation.
 *
 * Skipped entirely for prefers-reduced-motion — Lenis's eased momentum is
 * exactly the kind of motion that preference exists to opt out of, and the
 * page works perfectly well with native scrolling.
 */
export default function SmoothScroll() {
    useEffect(() => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const lenis = new Lenis({
            duration: 1.1,
            smoothWheel: true,
            // Without this, Lenis hijacks wheel/touch scrolling even over
            // nested scrollable elements (the chat widget's message list,
            // its FAQ tab, any future modal) and applies it to the page
            // instead — this auto-detects those and lets them scroll
            // natively, which Lenis's own docs call the most reliable fix
            // (more so than the data-lenis-prevent attribute alone).
            allowNestedScroll: true,
        });

        let frameId;
        function raf(time) {
            lenis.raf(time);
            frameId = requestAnimationFrame(raf);
        }
        frameId = requestAnimationFrame(raf);

        // Inertia swaps pages client-side (no real page load), so the
        // browser never resets scroll on its own — without this, Lenis
        // would carry the old page's scroll position into the new one.
        const removeListener = router.on('navigate', () => {
            lenis.scrollTo(0, { immediate: true });
        });

        return () => {
            cancelAnimationFrame(frameId);
            removeListener();
            lenis.destroy();
        };
    }, []);

    return null;
}
