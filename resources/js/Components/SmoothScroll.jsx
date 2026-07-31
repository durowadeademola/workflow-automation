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

// Lenis's eased/momentum scrolling is disabled for now — flip this back to
// true to re-enable it (the setup below is left intact rather than removed).
// The boundary bounce effect further down does not depend on Lenis and keeps
// working off native scroll either way.
const SMOOTH_SCROLL_ENABLED = false;

/**
 * Site-wide eased/momentum scrolling, plus a stylized elastic bounce at the
 * very top/bottom (the "you've reached the end" push effect seen on sites
 * like Semrush's homepage) — purely decorative, layered on top of Lenis
 * rather than replacing it.
 *
 * Uses a CSS transform for the bounce, not margin: margin-top/margin-bottom
 * change the document's actual scrollHeight, which only reveals a real gap
 * at the TOP (scrollY=0 is a hard floor, so pushing content down is "free").
 * At the BOTTOM, shrinking/growing height instead makes the browser
 * re-clamp scrollY to match in the same frame — content and viewport shrink
 * together and no gap is ever visible. transform sidesteps this entirely
 * (it's paint-only, doesn't touch layout/scrollHeight), so both ends behave
 * the same way. This is safe from the usual "transform on an ancestor
 * becomes the containing block for fixed descendants" trap because Navbar
 * renders through a portal to document.body (see Navbar.jsx) — it's a React
 * child of each page, but not a DOM descendant of #scroll-content.
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

        let lenis = null;
        let frameId;
        let removeListener = () => {};

        if (SMOOTH_SCROLL_ENABLED) {
            lenis = new Lenis({
                duration: 1.25,
                // Lenis's default easing is an exponential ease-out with a long,
                // gradual tail — most of the motion happens early, then it keeps
                // gliding almost imperceptibly for a while after, which reads as
                // "slippery"/never quite stopping. easeOutCubic (a steeper curve)
                // fixed that but removed the glide entirely, feeling too rigid.
                // easeOutQuad splits the difference: still a bit of drag/momentum
                // after the input stops, but bounded — it settles in well under a
                // second rather than coasting indefinitely.
                easing: (t) => 1 - (1 - t) * (1 - t),
                // Each wheel/trackpad tick travels a bit further, on top of the
                // duration/easing above — restoring some of the glide feel.
                wheelMultiplier: 0.85,
                smoothWheel: true,
                // Without this, Lenis hijacks wheel/touch scrolling even over
                // nested scrollable elements (the chat widget's message list,
                // its FAQ tab, any future modal) and applies it to the page
                // instead — this auto-detects those and lets them scroll
                // natively, which Lenis's own docs call the most reliable fix
                // (more so than the data-lenis-prevent attribute alone).
                allowNestedScroll: true,
            });

            function raf(time) {
                lenis.raf(time);
                frameId = requestAnimationFrame(raf);
            }
            frameId = requestAnimationFrame(raf);

            // Inertia swaps pages client-side (no real page load), so the
            // browser never resets scroll on its own — without this, Lenis
            // would carry the old page's scroll position into the new one.
            removeListener = router.on('navigate', () => {
                lenis.scrollTo(0, { immediate: true });
            });
        }

        // ── Boundary bounce ──
        const content = document.getElementById('scroll-content');
        let overscroll = 0;
        let settleTimer = null;

        function setOffset(value, { animate }) {
            const transition = animate
                ? `transform ${SPRING_DURATION}ms ${SPRING_EASING}`
                : 'none';

            content.style.transition = transition;
            // Positive value (top) pushes content down, revealing space
            // above it; negative value (bottom) pulls content up, revealing
            // space below it — symmetric, since transform never touches
            // scrollHeight/scroll position the way margin would.
            content.style.transform = value === 0 ? '' : `translateY(${value}px)`;

            // The cookie-consent banner is fixed to the viewport bottom and
            // sits directly on top of the strip of background a bottom bounce
            // reveals — without moving it too, the whole effect is invisible
            // behind the banner until it's dismissed. Move it in lockstep so
            // the reveal stays visible just above/below it either way.
            const cookieBanner = document.querySelector('[aria-label="Cookie consent"]');
            if (cookieBanner) {
                cookieBanner.style.transition = transition;
                cookieBanner.style.transform = value < 0 ? `translateY(${value}px)` : '';
            }
        }

        function springBack() {
            overscroll = 0;
            setOffset(0, { animate: true });
        }

        function handleWheel(event) {
            if (!content) return;

            const nativeLimit = document.documentElement.scrollHeight - window.innerHeight;
            const scroll = lenis ? (lenis.animatedScroll ?? lenis.scroll ?? window.scrollY) : window.scrollY;
            const limit = lenis ? (lenis.limit ?? nativeLimit) : nativeLimit;
            // A tight sub-pixel tolerance here only ever gets hit reliably on
            // short pages. Non-100% OS display scaling (125%/150%, common on
            // Windows laptops) introduces subpixel rounding in scrollHeight
            // that compounds across a long, many-section page like the
            // homepage, so real scrollY can land a few px shy of the exact
            // computed limit even when the user is genuinely at the bottom.
            const BOUNDARY_TOLERANCE = 3;
            const atTop = scroll <= BOUNDARY_TOLERANCE;
            const atBottom = scroll >= limit - BOUNDARY_TOLERANCE;

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
            window.removeEventListener('wheel', handleWheel);
            clearTimeout(settleTimer);
            if (content) {
                content.style.transition = '';
                content.style.transform = '';
            }
            const cookieBanner = document.querySelector('[aria-label="Cookie consent"]');
            if (cookieBanner) {
                cookieBanner.style.transition = '';
                cookieBanner.style.transform = '';
            }
            if (SMOOTH_SCROLL_ENABLED) {
                cancelAnimationFrame(frameId);
                removeListener();
                lenis.destroy();
            }
        };
    }, []);

    return null;
}
