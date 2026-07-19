import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import CookieConsent from './Components/CookieConsent';

document.addEventListener('inertia:invalid', (event) => {
    event.preventDefault()

    const url = event.detail.response.request.responseURL

    if (url) {
        window.location.assign(url)
    } else {
        window.location.reload()
    }
})

const appName = import.meta.env.VITE_APP_NAME || 'Blueflow';

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <>
                <App {...props} />
                <CookieConsent />
            </>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});
