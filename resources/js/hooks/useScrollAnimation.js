import { useEffect, useRef, useState } from "react";

/**
 * useScrollAnimation
 * Returns a ref to attach to a container and a boolean `isVisible`.
 * When the element enters the viewport, `isVisible` becomes true.
 *
 * @param {number} threshold - 0-1, how much of the element must be visible (default 0.15)
 * @param {boolean} once - if true, stays visible once triggered (default true)
 */
export function useScrollAnimation(threshold = 0.15, once = true) {
    const ref = useRef(null);
    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        const el = ref.current;
        if (!el) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                    if (once) observer.disconnect();
                } else if (!once) {
                    setIsVisible(false);
                }
            },
            { threshold }
        );

        observer.observe(el);
        return () => observer.disconnect();
    }, [threshold, once]);

    return [ref, isVisible];
}

/**
 * useCountUp
 * Counts a number from `start` to `end` over `duration` ms.
 * Starts when `trigger` becomes true.
 *
 * @param {number} end - target number
 * @param {number} duration - ms (default 1800)
 * @param {number} start - starting value (default 0)
 * @param {boolean} trigger - when true, starts counting
 */
export function useCountUp(end, duration = 1800, start = 0, trigger = true) {
    const [count, setCount] = useState(start);
    const frameRef = useRef(null);

    useEffect(() => {
        if (!trigger) return;

        const startTime = performance.now();
        const range = end - start;

        const step = (now) => {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease-out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            setCount(Math.round(start + range * eased));
            if (progress < 1) {
                frameRef.current = requestAnimationFrame(step);
            }
        };

        frameRef.current = requestAnimationFrame(step);
        return () => cancelAnimationFrame(frameRef.current);
    }, [trigger, end, start, duration]);

    return count;
}
