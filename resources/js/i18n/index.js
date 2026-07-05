import { createI18n } from 'vue-i18n';
import en from './en.json';
import he from './he.json';

export function createAppI18n(initialLocale) {
    return createI18n({
        legacy: false,
        locale: initialLocale || 'en',
        fallbackLocale: 'en',
        messages: { en, he },
    });
}

export function applyDocumentDirection(locale) {
    const dir = locale === 'he' ? 'rtl' : 'ltr';
    document.documentElement.setAttribute('lang', locale);
    document.documentElement.setAttribute('dir', dir);
}
