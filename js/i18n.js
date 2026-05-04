/**
 * i18n.js - Multi-language support for CardStudio
 */

class I18n {
    constructor() {
        this.languages = ['es', 'en'];
        this.defaultLang = 'es';
        this.currentLang = localStorage.getItem('language') || this.getBrowserLang() || this.defaultLang;
        this.translations = {};
        this.rootPath = this.detectRootPath();
    }

    detectRootPath() {
        // Detect if we are in admin folder or root
        const path = window.location.pathname;
        if (path.includes('/admin/')) {
            return '../';
        }
        return '';
    }

    getBrowserLang() {
        const lang = navigator.language.split('-')[0];
        return this.languages.includes(lang) ? lang : null;
    }

    async init() {
        await this.loadTranslations(this.currentLang);
        this.translatePage();
        this.updateSwitcherUI();
    }

    async loadTranslations(lang) {
        try {
            const response = await fetch(`${this.rootPath}languages/${lang}.json`);
            this.translations = await response.json();
            this.currentLang = lang;
            localStorage.setItem('language', lang);
            document.documentElement.lang = lang;
        } catch (error) {
            console.error(`Could not load translations for ${lang}:`, error);
        }
    }

    t(key) {
        return this.translations[key] || key;
    }

    translatePage() {
        const elements = document.querySelectorAll('[data-i18n]');
        elements.forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = this.t(key);
            
            if (el.tagName === 'INPUT' && (el.type === 'text' || el.type === 'password' || el.type === 'email')) {
                el.placeholder = translation;
            } else {
                el.innerHTML = translation;
            }
        });
        
        // Handle placeholders specifically if needed
        const placeholders = document.querySelectorAll('[data-i18n-placeholder]');
        placeholders.forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            el.placeholder = this.t(key);
        });
    }

    async setLanguage(lang) {
        if (lang === this.currentLang) return;
        await this.loadTranslations(lang);
        this.translatePage();
        this.updateSwitcherUI();
        
        // Dispatch custom event for dynamic parts
        window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang } }));
    }

    updateSwitcherUI() {
        const switchers = document.querySelectorAll('.lang-switcher');
        switchers.forEach(s => {
            s.value = this.currentLang;
        });
    }

    createSwitcher() {
        const html = `
            <select class="lang-switcher" onchange="i18n.setLanguage(this.value)" style="
                background: rgba(255,255,255,0.1);
                color: white;
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 5px;
                padding: 5px 10px;
                cursor: pointer;
                font-family: inherit;
                font-size: 0.85rem;
                margin-left: 15px;
                outline: none;
            ">
                <option value="es">🇪🇸 ES</option>
                <option value="en">🇺🇸 EN</option>
            </select>
        `;
        return html;
    }
}

// Global instance
const i18n = new I18n();
document.addEventListener('DOMContentLoaded', () => i18n.init());
