import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { logout } from '@/routes';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();

// Global fetch wrapper: if any fetch returns 401, trigger server logout
// to force the user to re-authenticate (e.g. when refresh token was revoked).
const _originalFetch = (typeof window !== 'undefined' && (window as any).fetch) || undefined;
if (_originalFetch) {
    (window as any).fetch = async (...args: any[]) => {
        const res = await _originalFetch(...args);
        if (res && res.status === 401) {
            try {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const token = csrfMeta ? csrfMeta.getAttribute('content') : null;

                const form = document.createElement('form');
                form.method = 'post';
                form.action = logout().url;
                form.style.display = 'none';

                if (token) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_token';
                    input.value = token;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            } catch (e) {
                // ignore
            }
        }

        return res;
    };
}
