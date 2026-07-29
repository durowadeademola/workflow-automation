import { useEffect } from 'react';
import { router } from '@inertiajs/react';
import Lenis from 'lenis';

// How far the page is allowed to visually "give" at the very top/bottom,
// and how much harder it gets to push further the closer it gets there —
// a simple rubber-band feel rather than a linear one.
const MAX_OVERSCROLL = 64;
const RESISTANCE = 0.35;
// If no further boundary-wheel input arrives within this window, treat the
// gesture as over and spring back — shorter than a typical debounce so it
// doesn't feel laggy once the visitor stops scrolling.
const SETTLE_DELAY = 110;
const SPRING_EASING = 'cubic-bezier(0.34, 1.56, 0.64, 1)';
const SPRING_DURATION = 550;

/**
 * Site-wide eased/momentum scrolling, plus a stylized elastic bounce at the
 * very top/bottom (the "you've reached the end" push effect seen on sites
 * like Semrush's homepage) — purely decorative, layered on top of Lenis
 * rather than replacing it.
 *
 * Deliberately uses margin-top, not transform, for the bounce: the site's
 * Navbar is `position: fixed`, and a `transform` on any ancestor turns that
 * ancestor into the containing block for fixed descendants (a real CSS
 * behavior, not a bug) — which would make the fixed navbar move with the
 * bounce instead of staying pinned to the viewport. margin-top has no such
 * side effect.
 *
 * Mounted once at the app root (see app.jsx) so it persists across Inertia
 * page swaps instead of being torn down and rebuilt on every navigation.
 *
 * Skipped entirely for prefers-reduced-motion — both Lenis's eased momentum
 * and this bounce are exactly the kind of motion that preference exists to
 * opt out of, and the page works perfectly well with native scrolling.
 */
export default function SmoothScroll() {
    useEffect(() => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const lenis = new Lenis({
            duration: 1.6,
            // Each wheel/trackpad tick travels less distance, on top of the
            // longer duration above — the combination is what actually
            // reads as "slower" rather than just "delayed."
            wheelMultiplier: 0.8,
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

        // ── Boundary bounce ──
        const content = document.getElementById('scroll-content');
        let overscroll = 0;
        let settleTimer = null;

        function setOffset(value, { animate }) {
            content.style.transition = animate
                ? `margin-top ${SPRING_DURATION}ms ${SPRING_EASING}`
                : 'none';
            content.style.marginTop = value === 0 ? '' : `${value}px`;
        }

        function springBack() {
            overscroll = 0;
            setOffset(0, { animate: true });
        }

        function handleWheel(event) {
            if (!content) return;

            const scroll = lenis.animatedScroll ?? lenis.scroll ?? window.scrollY;
            const limit = lenis.limit ?? (document.documentElement.scrollHeight - window.innerHeight);
            const atTop = scroll <= 0.5;
            const atBottom = scroll >= limit - 0.5;

            const pullingUpPastTop = atTop && event.deltaY < 0;
            const pullingDownPastBottom = atBottom && event.deltaY > 0;

            if (!pullingUpPastTop && !pullingDownPastBottom) {
                return;
            }

            const direction = pullingUpPastTop ? 1 : -1;
            // Resistance grows as it nears the max, so it never feels like
            // it can be pushed indefinitely.
            const remaining = 1 - Math.abs(overscroll) / MAX_OVERSCROLL;
            const pull = Math.abs(event.deltaY) * RESISTANCE * Math.max(remaining, 0.08);

            overscroll = Math.max(-MAX_OVERSCROLL, Math.min(MAX_OVERSCROLL, overscroll + direction * pull));
            setOffset(overscroll, { animate: false });

            clearTimeout(settleTimer);
            settleTimer = setTimeout(springBack, SETTLE_DELAY);
        }

        window.addEventListener('wheel', handleWheel, { passive: true });

        return () => {
            cancelAnimationFrame(frameId);
            removeListener();
            window.removeEventListener('wheel', handleWheel);
            clearTimeout(settleTimer);
            if (content) {
                content.style.transition = '';
                content.style.marginTop = '';
            }
            lenis.destroy();
        };
    }, []);

    return null;
}
